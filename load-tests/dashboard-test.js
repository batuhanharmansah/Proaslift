// Modül: Dashboard — tamamen okuma, en sık çağrılan endpoint grubu
// (çalışan uygulamayı her açtığında / ana sayfaya döndüğünde bu istekler atılır).
// Zincir/yan etki yok, sadece yanıt süresi ve doğru dönüşü test ediyoruz.
// Çalıştırmak için: k6 run dashboard-test.js

import http from 'k6/http';
import { check, sleep } from 'k6';
import { getSession, authHeaders, BASE_URL } from './_helpers.js';

export const options = {
  vus: 5,
  duration: '30s',
};

export default function () {
  const session = getSession();
  if (!session) return;
  const headers = authHeaders(session.token);

  const statsRes = http.get(`${BASE_URL}/api/mobile/dashboard/stats`, { headers });
  check(statsRes, { 'dashboard stats 200': (r) => r.status === 200 });

  sleep(0.5);

  const chartsRes = http.get(`${BASE_URL}/api/mobile/dashboard/charts`, { headers });
  check(chartsRes, { 'dashboard charts 200': (r) => r.status === 200 });

  sleep(0.5);

  const quickActionsRes = http.get(`${BASE_URL}/api/mobile/dashboard/quick-actions`, { headers });
  check(quickActionsRes, { 'quick actions 200': (r) => r.status === 200 });

  sleep(0.5);

  const stockRes = http.get(`${BASE_URL}/api/mobile/stock-warnings`, { headers });
  check(stockRes, { 'stock warnings 200': (r) => r.status === 200 });

  sleep(1);
}
