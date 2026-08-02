// Modül: Arıza Bildirimi (Issues) — bildirim oluşturma zinciri.
// Çalıştırmak için: k6 run issues-test.js
//
// ZİNCİR DOĞRULAMASI: Arıza bildirimi oluşturulduğunda IssueMaintenanceService
// OTOMATİK olarak bağlı bir MaintenanceSchedule oluşturur (issue_report_id ile
// eşleştirilmiş, maintenance_type='ariza_onarim') ve arızanın durumunu günceller
// ('ekip_atandi' eğer personel atanmışsa, yoksa 'inceleniyor'). Bu, response'ta
// 'maintenance_schedule_id' olarak doğrudan dönüyor — test bunu kontrol eder.

import http from 'k6/http';
import { check, sleep } from 'k6';
import { login, authHeaders, createLoadTestBuilding, uniqueName, BASE_URL } from './_helpers.js';

export const options = {
  vus: 3,
  duration: '30s',
};

export default function () {
  const session = login();
  if (!session) return;
  const headers = authHeaders(session.token);

  const building = createLoadTestBuilding(headers);
  if (!building) return;

  sleep(0.5);

  // 1) Okuma: arıza listesi ve istatistikler (gerçek kullanımda en sık çağrılan)
  const listRes = http.get(`${BASE_URL}/api/mobile/issues`, { headers });
  check(listRes, { 'issues list 200': (r) => r.status === 200 });

  const statsRes = http.get(`${BASE_URL}/api/mobile/issues/stats`, { headers });
  check(statsRes, { 'issues stats 200': (r) => r.status === 200 });

  sleep(0.5);

  // 2) Yazma: yeni arıza bildirimi (personel atamadan — 'bildirildi' durumu bekleniyor)
  const payload = JSON.stringify({
    building_id: building.id,
    reported_by: uniqueName(),
    issue_type: 'mekanik_ariza',
    priority: 'orta',
    description: 'Yük testi kapsamında otomatik oluşturulan arıza bildirimi.',
  });

  const createRes = http.post(`${BASE_URL}/api/mobile/issues`, payload, { headers });

  check(createRes, {
    'issue create 200': (r) => r.status === 200,
    "durum 'bildirildi' (personel atanmadı)": (r) => {
      try {
        return JSON.parse(r.body).data.status === 'bildirildi';
      } catch {
        return false;
      }
    },
    'bağlı bakım kaydı otomatik oluşmuş (maintenance_schedule_id var)': (r) => {
      try {
        return !!JSON.parse(r.body).data.maintenance_schedule_id;
      } catch {
        return false;
      }
    },
  });

  // 3) Oluşan bağlı bakım kaydının gerçekten var olduğunu ve doğru binaya bağlı olduğunu doğrula
  try {
    const data = JSON.parse(createRes.body).data;
    if (data.maintenance_schedule_id) {
      sleep(0.5);
      const maintenanceRes = http.get(
        `${BASE_URL}/api/mobile/maintenance/${data.maintenance_schedule_id}`,
        { headers }
      );
      check(maintenanceRes, {
        'otomatik oluşan bakım kaydı erişilebilir': (r) => r.status === 200,
        'otomatik bakım doğru binaya bağlı': (r) => {
          try {
            return JSON.parse(r.body).data.building.id === building.id;
          } catch {
            return false;
          }
        },
      });
    }
  } catch (e) {
    // create başarısızsa bu adımı atla
  }

  sleep(1);
}
