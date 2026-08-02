// Modül: Personel — SADECE OKUMA.
// Çalıştırmak için: k6 run employees-test.js
//
// BİLİNÇLİ SINIRLAMA: Mobil API'de personel oluşturma (POST) endpoint'i yok,
// sadece güncelleme (PUT) var — ve o da sadece company_admin yetkisiyle çalışıyor.
// Kendi izole LOADTEST_ personelimizi oluşturamadığımız için, gerçek bir personel
// kaydını güncelleyip bozma riskini almıyorum. Bu yüzden bu modül salt-okunur.
//
// AYRICA DOĞRULANAN: maaş (salary) alanının sadece admin hesaplarına göründüğü,
// employee hesabına döndüğünde response'ta bulunmaması gerektiği (güvenlik kuralı).

import http from 'k6/http';
import { check, sleep } from 'k6';
import { login, authHeaders, BASE_URL } from './_helpers.js';

export const options = {
  vus: 5,
  duration: '30s',
};

export default function () {
  const session = login();
  if (!session) return;
  const headers = authHeaders(session.token);

  const listRes = http.get(`${BASE_URL}/api/mobile/employees`, { headers });
  const listOk = check(listRes, { 'employees list 200': (r) => r.status === 200 });

  if (listOk && session.isEmployee) {
    check(listRes, {
      'employee hesabında maaş bilgisi GİZLENMİŞ (güvenlik kuralı)': (r) => {
        try {
          const list = JSON.parse(r.body).data.data;
          return list.every((e) => e.salary === undefined);
        } catch {
          return false;
        }
      },
    });
  }

  sleep(0.5);

  try {
    const employees = JSON.parse(listRes.body).data.data;
    if (employees && employees.length > 0) {
      const detailRes = http.get(`${BASE_URL}/api/mobile/employees/${employees[0].id}`, { headers });
      check(detailRes, { 'employee detail 200': (r) => r.status === 200 });

      sleep(0.3);

      const perfRes = http.get(`${BASE_URL}/api/mobile/employees/${employees[0].id}/performance`, { headers });
      check(perfRes, { 'employee performance 200': (r) => r.status === 200 });
    }
  } catch (e) {
    // liste boşsa bu adımları atla
  }

  sleep(1);
}
