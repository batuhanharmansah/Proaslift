// Birleşik senaryo: gerçek bir iş gününü simüle eder — birden fazla modül aynı anda,
// gerçekçi oranlarda çalışır. Diğer tüm load-tests/*.js dosyaları tek tek doğrulandıktan
// SONRA bunu çalıştırın; buradaki amaç "hepsi birlikteyken sistem ne yapıyor" sorusuna
// cevap vermek, tek tek modül doğruluğu değil (o kısım diğer dosyalarda yapıldı).
//
// Çalıştırmak için: k6 run combined-scenario.js

import http from 'k6/http';
import { check, sleep } from 'k6';
import { login, authHeaders, createLoadTestBuilding, uniqueName, BASE_URL } from './_helpers.js';

export const options = {
  scenarios: {
    // Çalışanlar: en yoğun grup — sürekli dashboard/bakım/bildirim kontrolü,
    // ara sıra arıza bildirimi.
    calisanlar: {
      executor: 'constant-vus',
      exec: 'employeeFlow',
      vus: 8,
      duration: '1m',
    },
    // Adminler: daha seyrek ama daha ağır sorgular (finansal rapor, bina listesi).
    adminler: {
      executor: 'constant-vus',
      exec: 'adminFlow',
      vus: 2,
      duration: '1m',
    },
  },
};

// ==================== ÇALIŞAN AKIŞI (mobil) ====================
export function employeeFlow() {
  const session = login();
  if (!session) return;
  const headers = authHeaders(session.token);

  // Güne dashboard'a bakarak başlar
  http.get(`${BASE_URL}/api/mobile/dashboard/stats`, { headers });
  sleep(1);

  // Bugünün bakım listesine bakar
  const maintenanceRes = http.get(`${BASE_URL}/api/mobile/maintenance`, { headers });
  check(maintenanceRes, { '[çalışan] maintenance list 200': (r) => r.status === 200 });
  sleep(1);

  // Bildirimlerini kontrol eder
  http.get(`${BASE_URL}/api/mobile/notifications`, { headers });
  sleep(1);

  // %10 ihtimalle: sahada bir arıza görür, bildirir (gerçekte de sık olmayan bir olay)
  if (Math.random() < 0.1) {
    const building = createLoadTestBuilding(headers);
    if (building) {
      const issueRes = http.post(
        `${BASE_URL}/api/mobile/issues`,
        JSON.stringify({
          building_id: building.id,
          reported_by: uniqueName(),
          issue_type: 'mekanik_ariza',
          priority: 'orta',
          description: 'Kombine senaryo - saha arıza bildirimi.',
        }),
        { headers }
      );
      check(issueRes, { '[çalışan] issue create 200': (r) => r.status === 200 });
    }
  }

  sleep(2);
}

// ==================== ADMİN AKIŞI (web paneli) ====================
function extractCsrfToken(html) {
  const match = html.match(/name="_token"\s+value="([^"]+)"/);
  return match ? match[1] : null;
}

export function adminFlow() {
  const jar = http.cookieJar();

  const loginPageRes = http.get(`${BASE_URL}/login`, { jar });
  const token = extractCsrfToken(loginPageRes.body);
  if (!token) return;

  const loginRes = http.post(
    `${BASE_URL}/login`,
    { email: 'admin@ornekfirma.com', password: 'test-sifre', _token: token },
    { jar, redirects: 3 }
  );

  const loggedIn = check(loginRes, {
    "[admin] login dashboard'a yönlendirdi": (r) => r.url.includes('/dashboard'),
  });
  if (!loggedIn) return;

  sleep(1);

  http.get(`${BASE_URL}/dashboard`, { jar });
  sleep(1);

  const buildingsRes = http.get(`${BASE_URL}/binalar`, { jar });
  check(buildingsRes, { '[admin] buildings page 200': (r) => r.status === 200 });
  sleep(1);

  // En ağır sorgu — finansal rapor
  const reportRes = http.get(`${BASE_URL}/finansal/rapor`, { jar });
  check(reportRes, { '[admin] financial report 200': (r) => r.status === 200 });

  sleep(2);
}
