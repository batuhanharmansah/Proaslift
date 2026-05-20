<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kullanım Şartları – Harmanşah Yazılım</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.7; }
        .header { background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; padding: 40px 24px 32px; text-align: center; }
        .header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 8px; }
        .header p  { font-size: 0.95rem; opacity: 0.85; }
        .container { max-width: 860px; margin: 0 auto; padding: 40px 24px 80px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.08); padding: 32px; margin-bottom: 24px; }
        h2 { font-size: 1.1rem; font-weight: 700; color: #0284c7; border-left: 4px solid #0284c7; padding-left: 12px; margin-bottom: 16px; }
        p, li { font-size: 0.9rem; color: #475569; margin-bottom: 8px; }
        ul { padding-left: 20px; }
        li { list-style: disc; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; color: #0284c7; font-size: 0.88rem; font-weight: 500; text-decoration: none; margin-bottom: 28px; }
        .back-link:hover { text-decoration: underline; }
        .footer-note { text-align: center; font-size: 0.8rem; color: #94a3b8; margin-top: 40px; }
        @media(max-width:600px){ .header h1{font-size:1.3rem} .card{padding:20px} }
    </style>
</head>
<body>

<div class="header">
    <h1>Kullanım Şartları</h1>
    <p>Son güncelleme: Mart 2026 &nbsp;|&nbsp; Harmanşah Yazılım</p>
</div>

<div class="container">
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="back-link">
        ← Geri Dön
    </a>

    <div class="card">
        <h2>1. Hizmet Kapsamı</h2>
        <p>Bu platform, asansör bakım ve yönetim operasyonlarının dijital olarak yönetilmesi amacıyla sunulan bir yazılım hizmetidir (SaaS). Platforma erişim, Harmanşah Yazılım ile yapılmış abonelik sözleşmesi çerçevesinde sağlanır.</p>
    </div>

    <div class="card">
        <h2>2. Kullanıcı Yükümlülükleri</h2>
        <ul>
            <li>Hesap bilgilerinizi (şifre, token) üçüncü kişilerle paylaşmayınız.</li>
            <li>Platformu yalnızca yasal ve sözleşme kapsamındaki amaçlarla kullanınız.</li>
            <li>Sisteme zarar verecek, güvenliği tehdit edecek ya da hizmetin sürekliliğini bozacak eylemlerden kaçınınız.</li>
            <li>Platforma girilen bilgilerin doğruluğundan kullanıcı sorumludur.</li>
        </ul>
    </div>

    <div class="card">
        <h2>3. Fikri Mülkiyet</h2>
        <p>Platform üzerindeki tüm yazılım, tasarım, arayüz ve içerikler Harmanşah Yazılım'a aittir. Abonelik, yalnızca kullanım hakkı vermekte olup mülkiyet hakkı devretmemektedir. İzinsiz kopyalama, dağıtım ve tersine mühendislik yasaktır.</p>
    </div>

    <div class="card">
        <h2>4. Veri Güvenliği ve Gizlilik</h2>
        <p>Kişisel verilerinizin işlenmesine ilişkin ayrıntılı bilgi için <a href="{{ route('kvkk') }}" style="color:#0284c7">KVKK Aydınlatma Metni & Gizlilik Politikası</a> sayfamızı inceleyiniz.</p>
    </div>

    <div class="card">
        <h2>5. Hizmet Sürekliliği ve Sorumluluk Sınırı</h2>
        <p>Harmanşah Yazılım, bakım, güncelleme veya öngörülemeyen teknik aksaklıklar nedeniyle hizmet kesintisi yaşanabileceğini kabul etmekte; mümkün olan en kısa sürede kesintileri gidermeyi taahhüt etmektedir. Doğrudan olmayan zararlar için sorumluluk kabul edilmez.</p>
    </div>

    <div class="card">
        <h2>6. Fesih</h2>
        <p>Abonelik sözleşmesinin sona ermesi veya ihlali halinde hesap erişimi durdurulabilir. Verilerinizin silinmesi için fesih tarihinden itibaren 30 gün içinde talepte bulunabilirsiniz.</p>
    </div>

    <div class="card">
        <h2>7. Değişiklikler</h2>
        <p>Bu şartlar önceden bildirimde bulunmak kaydıyla güncellenebilir. Değişiklikler yayınlandıktan sonra platforma giriş yapılması, yeni şartların kabul edildiği anlamına gelir.</p>
    </div>

    <div class="card">
        <h2>8. Uygulanacak Hukuk</h2>
        <p>Bu şartlar Türkiye Cumhuriyeti hukukuna tabidir. Uyuşmazlıklarda Türkiye mahkemeleri yetkilidir.</p>
    </div>

    <div class="footer-note">
        <p>© 2026 Harmanşah Yazılım – <a href="{{ route('kvkk') }}" style="color:#0284c7">KVKK & Gizlilik</a></p>
    </div>
</div>

</body>
</html>
