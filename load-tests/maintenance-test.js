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
//
// TASARIM NOTU: Bina SADECE BİR KEZ, testin başında (setup()) oluşturulur ve tüm
// iterasyonlar onu paylaşır — her iterasyonda yeni bina açmak hem gerçekçi değil
// (gerçekte bakım işleri mevcut binalara açılır) hem de mobile-buildings-write
// throttle'ını (30/dk) gereksiz yere zorluyordu. Her iterasyon kendi YENİ bakım
// kaydını oluşturur (paylaşılan bir kaydı start/complete etmek ikinci iterasyonda
// "durum uygun değil" hatası verir, bu yüzden maintenance paylaşılamaz, sadece bina).

import http from 'k6/http';
import { check, sleep } from 'k6';
import { login, authHeaders, createLoadTestBuilding, uniqueName, BASE_URL } from './_helpers.js';

export const options = {
  vus: 1, // yazma ağırlıklı zincir, doğruluk odaklı — throughput değil
  duration: '30s',
};

export function setup() {
  const session = login();
  if (!session) return null;
  const headers = authHeaders(session.token);

  const building = createLoadTestBuilding(headers);
  if (!building) return null;

  return { token: session.token, employeeId: session.employeeId, buildingId: building.id };
}

export default function (data) {
  if (!data) return;
  const headers = authHeaders(data.token);

  // 1) Bakım planla — varsa kendi employee id'mize ata (start adımı için gerekli)
  const createPayload = JSON.stringify({
    building_id: data.buildingId,
    maintenance_type: 'rutin_bakim',
    scheduled_date: new Date().toISOString().slice(0, 10),
    priority: 'normal',
    description: `${uniqueName()} - yük testi bakımı`,
    assigned_employee_id: data.employeeId,
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

  sleep(1);

  // Employee kaydı yoksa (saf admin hesabı) start/complete'e giremeyiz — burada durur.
  if (!data.employeeId) {
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

  sleep(1);

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

  sleep(2);
}
