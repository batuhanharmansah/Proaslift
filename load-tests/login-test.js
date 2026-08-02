// Modül: Giriş (auth). Bilerek HER ÇAĞRIDA yeniden login olur — diğer tüm
// modüllerin aksine (onlar getSession() ile bir kez login olup token'ı önbelleğe
// alıyor), bu dosyanın amacı login endpoint'inin KENDİSİNİ test etmek.
// Çalıştırmak için: k6 run login-test.js
// (k6 kurulu değilse: brew install k6)
//
// ÖNEMLİ — GERÇEKÇİ HIZ: mobile-auth-login throttle limiti dakikada 20 istek.
// Gerçek bir kullanıcı günde birkaç kez login olur, saniyede birkaç kez DEĞİL.
// Bu yüzden burada VU'ları sürekli döngüde koşturmak yerine, dakikada sabit bir
// GİRİŞ SAYISI (arrival rate) simüle ediyoruz — throttle limitinin altında,
// gerçekçi bir yoğunlukta. Limiti bilerek test etmek isterseniz 'rate' değerini
// 20'nin üzerine çıkarın (örn. 30) — o zaman bir kısmının 429 alması BEKLENİR,
// bu bir bug değildir.

import { login } from './_helpers.js';

export const options = {
  scenarios: {
    gercekci_giris_hizi: {
      executor: 'constant-arrival-rate',
      rate: 15,              // dakikada 15 login denemesi (20 limitinin altında)
      timeUnit: '1m',
      duration: '30s',
      preAllocatedVUs: 5,
      maxVUs: 10,
    },
  },
};

export default function () {
  login();
}
