<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hesap Silme Bilgilendirmesi - Harmansah Asansor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.7; }
        .header { background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; padding: 40px 24px 32px; text-align: center; }
        .header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 8px; }
        .header p { font-size: 0.95rem; opacity: 0.9; }
        .container { max-width: 860px; margin: 0 auto; padding: 40px 24px 80px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 8px rgba(0, 0, 0, 0.08); padding: 32px; margin-bottom: 24px; }
        h2 { font-size: 1.1rem; font-weight: 700; color: #0284c7; border-left: 4px solid #0284c7; padding-left: 12px; margin-bottom: 16px; }
        p, li { font-size: 0.95rem; color: #475569; margin-bottom: 10px; }
        ul, ol { padding-left: 20px; }
        li { margin-bottom: 8px; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; color: #0284c7; font-size: 0.88rem; font-weight: 500; text-decoration: none; margin-bottom: 28px; }
        .back-link:hover { text-decoration: underline; }
        .note { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; border-radius: 10px; padding: 16px; }
        .footer-note { text-align: center; font-size: 0.8rem; color: #94a3b8; margin-top: 40px; }
        @media(max-width:600px){ .header h1{font-size:1.3rem} .card{padding:20px} }
    </style>
</head>
<body>

<div class="header">
    <h1>Hesap Silme Bilgilendirmesi</h1>
    <p>Harmansah Asansor mobil uygulamasi icin hesap silme sureci</p>
</div>

<div class="container">
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="back-link">
        ← Geri Don
    </a>

    <div class="card">
        <h2>1. Hesabinizi nasil silebilirsiniz?</h2>
        <p>Hesabinizi uygulama icinden silebilirsiniz. Hesap silme islemi dogrulama amaciyla mevcut sifrenizin tekrar girilmesini gerektirir.</p>
        <ol>
            <li>Web panel veya ilgili hesap yonetimi ekraninda profil sayfasini acin.</li>
            <li><strong>Hesabi Sil</strong> alanini secin.</li>
            <li>Mevcut sifrenizi girerek islemi onaylayin.</li>
            <li>Onay sonrasinda hesabiniz kalici olarak silinir ve oturumunuz kapatilir.</li>
        </ol>
    </div>

    <div class="card">
        <h2>2. Uygulamaya erisemiyorsaniz</h2>
        <p>Uygulamaya veya hesabinuza erisemiyorsaniz hesap silme talebinizi e-posta ile de iletebilirsiniz.</p>
        <p><strong>Iletisim e-postasi:</strong> info@proaslift.com</p>
        <p>Talebinizde hesapla iliskili ad soyad, sirket bilgisi ve ulasilabilir e-posta adresinizi paylasmaniz sureci hizlandirir.</p>
    </div>

    <div class="card">
        <h2>3. Hangi veriler silinir?</h2>
        <ul>
            <li>Kullanici hesabi bilgileri</li>
            <li>Oturum ve cihaz token kayitlari</li>
            <li>Hesaba bagli erisim bilgileri ve aktif oturumlar</li>
        </ul>
    </div>

    <div class="card">
        <h2>4. Hangi veriler saklanabilir?</h2>
        <p>Bazi veriler yasal yukumlulukler, muhasebe ve denetim gereklilikleri nedeniyle belirli surelerle saklanabilir.</p>
        <ul>
            <li>Finansal kayitlar ve muhasebe verileri</li>
            <li>Bakim raporlari ve islem kayitlari</li>
            <li>Denetim loglari ve yasal olarak tutulmasi gereken kayitlar</li>
        </ul>
        <p>Veri saklama surecleri hakkinda ayrintili bilgi icin <a href="{{ route('kvkk') }}" style="color:#0284c7;">KVKK ve Gizlilik Politikasi</a> sayfasini inceleyebilirsiniz.</p>
    </div>

    <div class="card">
        <h2>5. Talep sonrasi surec</h2>
        <p>Silme talebiniz alindiktan sonra gerekli kontroller yapilir ve uygun olan veriler makul sure icinde silinir veya anonim hale getirilir.</p>
        <div class="note">
            Hesap silme talebinizle ilgili destek almak isterseniz <strong>info@proaslift.com</strong> adresine ulasabilirsiniz.
        </div>
    </div>

    <div class="footer-note">
        <p>Son guncelleme: Nisan 2026</p>
        <p>© 2026 Harmansah Yazilim</p>
    </div>
</div>

</body>
</html>
