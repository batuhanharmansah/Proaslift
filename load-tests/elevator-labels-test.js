// Modül: Etiket Takibi (Elevator Labels) — SADECE OKUMA.
// Çalıştırmak için: k6 run elevator-labels-test.js
//
// BİLİNÇLİ SINIRLAMA: 'complete'/'seal'/'cancel' işlemleri buraya dahil edilmedi.
// Etiketler günlük 'elevator:monitor' cron'u tarafından GERÇEK binalar için
// üretiliyor — LOADTEST_ binamız için otomatik etiket oluşmuyor, dolayısıyla
// yazma testi yapmak için ya gerçek bir etiketi değiştirmemiz (riskli, gerçek
// veriyi bozar) ya da günlerce beklememiz gerekir. Bu yüzden bu modülü kasıtlı
// olarak salt-okunur tuttum.

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

  const listRes = http.get(`${BASE_URL}/api/mobile/elevator-labels`, { headers });
  check(listRes, { 'elevator-labels list 200': (r) => r.status === 200 });

  sleep(0.5);

  const statsRes = http.get(`${BASE_URL}/api/mobile/elevator-labels/stats`, { headers });
  check(statsRes, { 'elevator-labels stats 200': (r) => r.status === 200 });

  sleep(0.5);

  try {
    const labels = JSON.parse(listRes.body).data.data;
    if (labels && labels.length > 0) {
      const detailRes = http.get(`${BASE_URL}/api/mobile/elevator-labels/${labels[0].id}`, { headers });
      check(detailRes, { 'elevator-label detail 200': (r) => r.status === 200 });
    }
  } catch (e) {
    // liste boşsa detay adımını atla
  }

  sleep(1);
}
