// Modül: Bildirimler — çoğunlukla okuma. Var olan bir bildirim varsa okundu
// işaretleme de test edilir (bu yıkıcı değil, gerçek kullanıcı davranışıyla aynı).
// Çalıştırmak için: k6 run notifications-test.js

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

  const listRes = http.get(`${BASE_URL}/api/mobile/notifications`, { headers });
  check(listRes, { 'notifications list 200': (r) => r.status === 200 });

  sleep(0.5);

  const unreadRes = http.get(`${BASE_URL}/api/mobile/notifications/unread-count`, { headers });
  const unreadOk = check(unreadRes, {
    'unread-count 200': (r) => r.status === 200,
    'total_unread sayısal': (r) => {
      try {
        return typeof JSON.parse(r.body).data.total_unread === 'number';
      } catch {
        return false;
      }
    },
  });

  sleep(0.5);

  // Listede bir bildirim varsa, okundu işaretleyip unread sayacının düştüğünü doğrula
  try {
    const notifications = JSON.parse(listRes.body).data.data;
    if (notifications && notifications.length > 0 && !notifications[0].is_read) {
      const beforeCount = JSON.parse(unreadRes.body).data.total_unread;

      const markRes = http.post(`${BASE_URL}/api/mobile/notifications/${notifications[0].id}/read`, null, {
        headers,
      });
      check(markRes, { 'mark as read 200': (r) => r.status === 200 });

      sleep(0.3);

      const afterRes = http.get(`${BASE_URL}/api/mobile/notifications/unread-count`, { headers });
      check(afterRes, {
        'okundu işaretleyince unread sayısı azalmış': (r) => {
          try {
            return JSON.parse(r.body).data.total_unread === beforeCount - 1;
          } catch {
            return false;
          }
        },
      });
    }
  } catch (e) {
    // liste boşsa bu adımı atla
  }

  sleep(1);
}
