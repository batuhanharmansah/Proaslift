// Modül: Bakım (Maintenance) — planlama → başlatma → tamamlama zinciri.
// Çalıştırmak için: k6 run maintenance-test.js
//
// ÖNEMLİ ÖN KOŞUL: 'start' endpoint'i SADECE işin atandığı çalışana izin veriyor
// (Employee kaydı olmayan saf admin hesapları işi başlatamaz — 403 döner).
// Test hesabınızın bir Employee kaydına bağlı olması gerekiyor, yoksa bu script
// sadece "planlama" adımını test eder, start/complete adımlarını atlar.
//
// GÜVENLİK NOTU: 'complete' (tamamlandi) durumu MaintenanceApprovalService'i tetikler,
// bu servis binanın birincil kişisine SMS atmaya çalışır. LOADTEST_ binalarımızın
// hiçbir BuildingContact'ı olmadığı için sendApprovalSms() 'no_contact' ile atlanır —
// yani GERÇEK SMS GİTMEZ (koddan doğrulandı). Sahte/gerçek bir binaya karşı complete
// çağırmayın — orada gerçek SMS gidebilir.

import http from 'k6/http';
import { check, sleep } from 'k6';
import { login, authHeaders, createLoadTestBuilding, uniqueName, BASE_URL } from './_helpers.js';

export const options = {
  vus: 3, // yazma ağırlıklı zincir, düşük VU yeterli
  duration: '30s',
};

export default function () {
  const session = login();
  if (!session) return;
  const headers = authHeaders(session.token);

  // Kendi LOADTEST_ binamızı oluştur (gerçek veriye dokunmamak için)
  const building = createLoadTestBuilding(headers);
  if (!building) return;

  sleep(0.5);

  // 1) Bakım planla — varsa kendi employee id'mize ata (start adımı için gerekli)
  const createPayload = JSON.stringify({
    building_id: building.id,
    maintenance_type: 'rutin_bakim',
    scheduled_date: new Date().toISOString().slice(0, 10),
    priority: 'normal',
    description: `${uniqueName()} - yük testi bakımı`,
    assigned_employee_id: session.employeeId,
  });

  const createRes = http.post(`${BASE_URL}/api/mobile/maintenance`, createPayload, { headers });
  const created = check(createRes, {
    'maintenance create 201': (r) => r.status === 201,
    "durum 'planli' ile başlıyor": (r) => {
      try {
        return JSON.parse(r.body).data.status === 'planli';
      } catch {
        return false;
      }
    },
  });

  if (!created) return;
  const maintenanceId = JSON.parse(createRes.body).data.id;

  sleep(0.5);

  // Employee kaydı yoksa (saf admin hesabı) start/complete'e giremeyiz — burada durur.
  if (!session.employeeId) {
    return;
  }

  // 2) İşi başlat — sadece atanan çalışan yapabilir
  const startRes = http.post(`${BASE_URL}/api/mobile/maintenance/${maintenanceId}/start`, null, { headers });
  const started = check(startRes, {
    'maintenance start 200': (r) => r.status === 200,
    "durum 'baslandi' oldu": (r) => {
      try {
        return JSON.parse(r.body).data.status === 'baslandi';
      } catch {
        return false;
      }
    },
  });

  if (!started) return;

  sleep(0.5);

  // 3) İşi tamamla → MaintenanceReport oluşmalı, onay akışı tetiklenmeli (ama SMS gitmemeli)
  const completePayload = JSON.stringify({
    work_description: 'Yük testi kapsamında otomatik oluşturulan tamamlama.',
    completion_status: 'tamamlandi',
    total_cost: 0,
  });

  const completeRes = http.post(
    `${BASE_URL}/api/mobile/maintenance/${maintenanceId}/complete`,
    completePayload,
    { headers }
  );

  check(completeRes, {
    'maintenance complete 200': (r) => r.status === 200,
    'rapor oluşmuş (report_id var)': (r) => {
      try {
        return !!JSON.parse(r.body).data.report_id;
      } catch {
        return false;
      }
    },
    "durum 'tamamlandi' oldu": (r) => {
      try {
        return JSON.parse(r.body).data.status === 'tamamlandi';
      } catch {
        return false;
      }
    },
    'gerçek SMS gitmedi (contact yok, beklenen davranış)': (r) => {
      try {
        return JSON.parse(r.body).data.approval_sms_sent === false;
      } catch {
        return false;
      }
    },
  });

  sleep(1);
}
