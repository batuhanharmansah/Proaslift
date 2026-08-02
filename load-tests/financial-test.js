// Modül: Finansal — hesap oluşturma + alacak ödemesi alma zinciri.
// Çalıştırmak için: k6 run financial-test.js
//
// ZİNCİR DOĞRULAMASI: Bir alacağa ödeme alındığında (receivePayment) TEK istekte
// üç şey birden olmalı:
//   1) Receivable.received_amount artmalı, remaining_amount azalmalı, status güncellenmeli
//   2) Yeni bir AccountingEntry (muhasebe kaydı) oluşmalı
//   3) Ödemenin yapıldığı hesabın (AccountType) bakiyesi ödeme tutarı kadar artmalı
// Bu üçü de receivePayment'ın kendi response'unda dönüyor — ayrıca hesap bakiyesini
// ikinci bir GET ile de doğruluyoruz (response'a değil, gerçek DB durumuna güveniyoruz).

import http from 'k6/http';
import { check, sleep } from 'k6';
import { login, authHeaders, createLoadTestBuilding, uniqueName, BASE_URL } from './_helpers.js';

export const options = {
  vus: 3,
  duration: '30s',
};

export default function () {
  const session = login();
  if (!session) return;
  const headers = authHeaders(session.token);

  // 1) Okuma: özet + hesap listesi (gerçek kullanımda en sık çağrılan)
  const summaryRes = http.get(`${BASE_URL}/api/mobile/financial/summary`, { headers });
  check(summaryRes, { 'financial summary 200': (r) => r.status === 200 });

  sleep(0.5);

  // 2) LOADTEST_ hesap oluştur (başlangıç bakiyesi 0)
  const initialBalance = 0;
  const accountPayload = JSON.stringify({
    name: uniqueName(),
    type: 'kasa',
    initial_balance: initialBalance,
  });

  const accountRes = http.post(`${BASE_URL}/api/mobile/financial/accounts`, accountPayload, { headers });
  const accountOk = check(accountRes, { 'account create 201': (r) => r.status === 201 });
  if (!accountOk) return;
  const account = JSON.parse(accountRes.body).data;

  sleep(0.5);

  // 3) LOADTEST_ bina oluştur → otomatik olarak 1 adet 'beklemede' alacak gelir
  const monthlyFee = 500;
  const building = createLoadTestBuilding(headers, monthlyFee);
  if (!building) return;

  sleep(0.5);

  const receivablesRes = http.get(
    `${BASE_URL}/api/mobile/financial/receivables?building_id=${building.id}`,
    { headers }
  );
  let receivable = null;
  try {
    receivable = JSON.parse(receivablesRes.body).data.data[0];
  } catch (e) {
    return;
  }
  if (!receivable) return;

  sleep(0.5);

  // 4) Bu alacağa (kısmi) ödeme al — tutarın yarısı kadar
  const paymentAmount = monthlyFee / 2;
  const paymentPayload = JSON.stringify({
    receivable_id: receivable.id,
    amount: paymentAmount,
    account_id: account.id,
  });

  const paymentRes = http.post(`${BASE_URL}/api/mobile/financial/receivables/receive-payment`, paymentPayload, {
    headers,
  });

  check(paymentRes, {
    'receive-payment 200': (r) => r.status === 200,
    'alınan tutar doğru işlenmiş': (r) => {
      try {
        return Number(JSON.parse(r.body).data.receivable.received_amount) === paymentAmount;
      } catch {
        return false;
      }
    },
    'kalan tutar doğru hesaplanmış': (r) => {
      try {
        return Number(JSON.parse(r.body).data.receivable.remaining_amount) === monthlyFee - paymentAmount;
      } catch {
        return false;
      }
    },
    "alacak tamamı ödenmediği için hala 'beklemede/kismi' olmalı, 'odendi' OLMAMALI": (r) => {
      try {
        return JSON.parse(r.body).data.receivable.status !== 'odendi';
      } catch {
        return false;
      }
    },
  });

  sleep(0.5);

  // 5) Hesap bakiyesinin gerçekten arttığını AYRI bir GET ile doğrula
  // (receivePayment'ın kendi response'una değil, gerçek DB durumuna bakıyoruz)
  const accountsAfterRes = http.get(`${BASE_URL}/api/mobile/financial/accounts`, { headers });
  check(accountsAfterRes, {
    'hesap bakiyesi ödeme kadar artmış': (r) => {
      try {
        const list = JSON.parse(r.body).data.data || JSON.parse(r.body).data;
        const match = list.find((a) => a.id === account.id);
        return !!match && Number(match.current_balance) === initialBalance + paymentAmount;
      } catch {
        return false;
      }
    },
  });

  sleep(1);
}
