// Modül: Binalar — liste + detay (okuma, ağırlıklı) + yeni bina ekleme (yazma, seyrek).
// Çalıştırmak için: k6 run buildings-test.js
//
// ZİNCİR DOĞRULAMASI (sadece "200 döndü" değil, gerçek iş kuralı kontrol edilir):
// Bina eklendiğinde BuildingFinancialService şunları otomatik oluşturur:
//   1) BuildingFinancialRecord (API'den görünmüyor, DB'de — test edilemiyor)
//   2) RecurringPayment — sözleşme başlangıç/bitiş tarihleriyle, aktif
//   3) TEK bir Receivable — SADECE ilk ay için (sözleşme 3 ay da olsa, 12 ay da olsa
//      baştan sadece 1 tane oluşur; kalan aylar her gece çalışan cron ile ay ay üretilir —
//      bu bug DEĞİL, sistemin tasarımı). Test bunu doğrular: tam 1 tane olmalı, fazlası değil.
//
// ÖNEMLİ: Bu script yeni bina KAYDEDER. Karışıklık olmasın diye tüm test binaları
// "LOADTEST_" önekiyle oluşturulur. Test bitince web panelinde Sistem Sağlığı
// sayfasındaki "Yük Testi Verisini Temizle" butonuyla bunları silin.

import http from 'k6/http';
import { check, sleep } from 'k6';
import { getSession, authHeaders, createLoadTestBuilding, BASE_URL } from './_helpers.js';

export const options = {
  vus: 5,
  duration: '30s',
};

export default function () {
  const session = getSession();
  if (!session) return;
  const headers = authHeaders(session.token);

  // 1) Bina listesi (gerçek kullanımda en sık çağrılan)
  const listRes = http.get(`${BASE_URL}/api/mobile/buildings`, { headers });
  check(listRes, { 'buildings list 200': (r) => r.status === 200 });

  sleep(1);

  // 2) İlk binanın detayı (varsa)
  try {
    const buildings = JSON.parse(listRes.body).data.data;
    if (buildings && buildings.length > 0) {
      const detailRes = http.get(`${BASE_URL}/api/mobile/buildings/${buildings[0].id}`, { headers });
      check(detailRes, { 'building detail 200': (r) => r.status === 200 });
    }
  } catch (e) {
    // liste boşsa veya format farklıysa detay adımını atla
  }

  sleep(1);

  // 3) Yeni bina ekleme + zincir doğrulaması — düşük oranda (her sanal kullanıcının
  // ~%20'si dener), gerçekte de bina ekleme sık yapılan bir işlem değil.
  if (Math.random() < 0.2) {
    const monthlyFee = 500;
    const building = createLoadTestBuilding(headers, monthlyFee);

    if (building) {
      sleep(0.5);

      // 3a) Bu binaya ait alacakları çek → tam 1 tane, tutarı monthly_fee, durumu 'beklemede' olmalı
      const receivablesRes = http.get(
        `${BASE_URL}/api/mobile/financial/receivables?building_id=${building.id}`,
        { headers }
      );
      check(receivablesRes, {
        'receivables 200': (r) => r.status === 200,
        'tam 1 alacak oluşmuş (gelecek aylar önceden oluşmamalı)': (r) => {
          try {
            return JSON.parse(r.body).data.data.length === 1;
          } catch {
            return false;
          }
        },
        'alacak tutarı aylık ücretle eşleşiyor': (r) => {
          try {
            const rec = JSON.parse(r.body).data.data[0];
            return Number(rec.total_amount) === monthlyFee;
          } catch {
            return false;
          }
        },
        "alacak durumu 'beklemede'": (r) => {
          try {
            return JSON.parse(r.body).data.data[0].status === 'beklemede';
          } catch {
            return false;
          }
        },
      });

      sleep(0.5);

      // 3b) Düzenli ödemeler listesinde bu binaya ait kayıt var mı, tutarı doğru mu, aktif mi
      const recurringRes = http.get(`${BASE_URL}/api/mobile/financial/recurring-payments`, { headers });
      check(recurringRes, {
        'recurring payments 200': (r) => r.status === 200,
        'bu binaya ait aktif düzenli ödeme var, tutarı doğru': (r) => {
          try {
            const list = JSON.parse(r.body).data.data;
            const match = list.find((p) => p.building_id === building.id);
            return !!match && match.is_active === true && Number(match.amount) === monthlyFee;
          } catch {
            return false;
          }
        },
      });
    }
  }

  sleep(1);
}
