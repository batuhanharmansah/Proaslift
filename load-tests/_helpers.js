// Ortak yardımcılar: tüm load-tests/*.js dosyaları bunu import eder.
// Kendi başına çalıştırılmaz.

import http from 'k6/http';
import { check } from 'k6';

// Gerçek şifreyi hiçbir zaman dosyaya yazmayın/commit etmeyin — ortam değişkeni olarak verin:
//   k6 run -e LOADTEST_EMAIL=admin@test.com -e LOADTEST_PASSWORD='Test123.' login-test.js
export const BASE_URL = __ENV.LOADTEST_BASE_URL || 'https://proaslift.com';
export const EMAIL = __ENV.LOADTEST_EMAIL || 'admin@test.com';
export const PASSWORD = __ENV.LOADTEST_PASSWORD || '';

// LOADTEST_ önekiyle oluşturulan her şey, "Sistem Sağlığı" sayfasındaki
// "Yük Testi Verisini Temizle" butonuyla (sadece binalar için) temizlenebilir.
export const LOADTEST_PREFIX = 'LOADTEST_';

export function uniqueName() {
  return `${LOADTEST_PREFIX}${Date.now()}_${__VU}_${__ITER}`;
}

export function login() {
  const res = http.post(
    `${BASE_URL}/api/mobile/auth/login`,
    JSON.stringify({ email: EMAIL, password: PASSWORD }),
    { headers: { 'Content-Type': 'application/json' } }
  );

  const ok = check(res, { 'login 200': (r) => r.status === 200 });
  if (!ok) {
    return null;
  }

  const body = JSON.parse(res.body);
  return {
    token: body.data.token,
    userId: body.data.user.id,
    employeeId: body.data.user.employee ? body.data.user.employee.id : null,
    isEmployee: body.data.is_employee,
  };
}

export function authHeaders(token) {
  return {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${token}`,
  };
}

// Test verisi olarak kullanılacak, sözleşmesi 3 ay olan bir LOADTEST_ bina oluşturur.
// Dönen değer: { id, monthly_fee, contract_start_date, contract_end_date } ya da null (başarısızsa).
export function createLoadTestBuilding(headers, monthlyFee = 500) {
  const payload = JSON.stringify({
    name: uniqueName(),
    address: 'Test Mahallesi, Test Sokak No:1',
    district: 'Test İlçe',
    city: 'İstanbul',
    floor_count: 5,
    elevator_count: 1,
    elevator_type: 'yolcu',
    contract_type: 'bakim',
    monthly_fee: monthlyFee,
    contract_start_date: '2026-01-01',
    contract_end_date: '2026-04-01', // 3 ay
  });

  const res = http.post(`${BASE_URL}/api/mobile/buildings`, payload, { headers });
  const ok = check(res, { 'building create 200/201': (r) => r.status === 200 || r.status === 201 });
  if (!ok) {
    return null;
  }

  return JSON.parse(res.body).data;
}
