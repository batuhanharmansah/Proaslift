// Modül: Web Paneli (company_admin) — session + CSRF tabanlı, mobil API'den FARKLI
// bir kimlik doğrulama akışı kullanır. k6 bunu da simüle edebiliyor, sadece bir
// adım fazla: önce login sayfasından CSRF token'ı çekip, session cookie'siyle
// birlikte /login'e POST atıyoruz.
// Çalıştırmak için: k6 run web-admin-test.js

import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: 5,
  duration: '30s',
};

const BASE_URL = 'https://proaslift.com'; // gerçek adresinizle değiştirin
const EMAIL = 'admin@ornekfirma.com';       // web paneli test hesabı (company_admin)
const PASSWORD = 'test-sifre';

function extractCsrfToken(html) {
  const match = html.match(/name="_token"\s+value="([^"]+)"/);
  return match ? match[1] : null;
}

function webLogin(jar) {
  const loginPageRes = http.get(`${BASE_URL}/login`, { jar });
  const token = extractCsrfToken(loginPageRes.body);
  if (!token) return false;

  const loginRes = http.post(
    `${BASE_URL}/login`,
    { email: EMAIL, password: PASSWORD, _token: token },
    { jar, redirects: 3 }
  );

  // Başarılı girişte dashboard'a yönlendirilir (302 sonrası 200); form sayfasına
  // geri dönerse (hatalı bilgi) yine 200 olabilir, bu yüzden URL'e bakıyoruz.
  return check(loginRes, {
    'web login dashboard\'a yönlendirdi': (r) => r.url.includes('/dashboard'),
  });
}

export default function () {
  const jar = http.cookieJar();

  const loggedIn = webLogin(jar);
  if (!loggedIn) return;

  sleep(0.5);

  const dashboardRes = http.get(`${BASE_URL}/dashboard`, { jar });
  check(dashboardRes, { 'dashboard 200': (r) => r.status === 200 });

  sleep(1);

  const buildingsRes = http.get(`${BASE_URL}/binalar`, { jar });
  check(buildingsRes, { 'buildings page 200': (r) => r.status === 200 });

  sleep(1);

  // Finansal rapor sayfası — en ağır sorgulardan biri olduğu tahmin ediliyor
  const reportRes = http.get(`${BASE_URL}/finansal/rapor`, { jar });
  check(reportRes, { 'financial report 200': (r) => r.status === 200 });

  sleep(1);
}
