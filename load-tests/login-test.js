// Modül: Giriş (auth). Sadece login akışını test eder.
// Çalıştırmak için: k6 run login-test.js
// (k6 kurulu değilse: brew install k6)

import { sleep } from 'k6';
import { login } from './_helpers.js';

export const options = {
  vus: 5,          // 5 eş zamanlı "sanal kullanıcı"
  duration: '30s',  // 30 saniye boyunca tekrar tekrar dener
};

export default function () {
  login();
  sleep(1); // gerçek kullanıcı gibi art arda deneme yapma
}
