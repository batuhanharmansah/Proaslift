<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KVKK Aydınlatma Metni & Gizlilik Politikası – Harmanşah Yazılım</title>
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
        h3 { font-size: 0.95rem; font-weight: 600; color: #334155; margin: 20px 0 8px; }
        p, li { font-size: 0.9rem; color: #475569; margin-bottom: 8px; }
        ul { padding-left: 20px; }
        li { list-style: disc; }
        table { width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-top: 12px; }
        th { background: #0ea5e9; color: #fff; padding: 10px 12px; text-align: left; font-weight: 600; }
        td { padding: 9px 12px; border-bottom: 1px solid #e2e8f0; color: #475569; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 600; }
        .badge-red    { background: #fee2e2; color: #b91c1c; }
        .badge-yellow { background: #fef9c3; color: #92400e; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; color: #0284c7; font-size: 0.88rem; font-weight: 500; text-decoration: none; margin-bottom: 28px; }
        .back-link:hover { text-decoration: underline; }
        .footer-note { text-align: center; font-size: 0.8rem; color: #94a3b8; margin-top: 40px; }
        @media(max-width:600px){ .header h1{font-size:1.3rem} .card{padding:20px} }
    </style>
</head>
<body>

<div class="header">
    <h1>KVKK Aydınlatma Metni & Gizlilik Politikası</h1>
    <p>Son güncelleme: Mart 2026 &nbsp;|&nbsp; Harmanşah Yazılım</p>
</div>

<div class="container">
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="back-link">
        ← Geri Dön
    </a>

    <!-- 1. VERİ SORUMLUSU -->
    <div class="card">
        <h2>1. Veri Sorumlusu</h2>
        <p>6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamında veri sorumlusu sıfatıyla kişisel verileriniz <strong>Harmanşah Yazılım</strong> tarafından aşağıda açıklanan amaçlar ve yöntemler çerçevesinde işlenmektedir.</p>
        <p><strong>İletişim:</strong> info@proaslift.com</p>
    </div>

    <!-- 2. İŞLENEN KİŞİSEL VERİLER -->
    <div class="card">
        <h2>2. İşlenen Kişisel Veriler ve Amaçları</h2>
        <table>
            <thead>
                <tr>
                    <th>Veri Kategorisi</th>
                    <th>Örnekler</th>
                    <th>İşleme Amacı</th>
                    <th>Hukuki Dayanak</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Kimlik / İletişim</strong></td>
                    <td>Ad, soyad, e-posta, telefon, adres</td>
                    <td>Hesap yönetimi, personel kaydı, iletişim</td>
                    <td>Sözleşmenin ifası (md. 5/2-c)</td>
                </tr>
                <tr>
                    <td><strong>Konum Verisi</strong></td>
                    <td>Enlem, boylam, doğruluk değeri</td>
                    <td>Bakım görevi sırasında konum takibi ve varış doğrulaması</td>
                    <td>Meşru menfaat (md. 5/2-f) + Açık rıza (md. 5/1)</td>
                </tr>
                <tr>
                    <td><strong>Güvenlik / Kimlik Doğrulama</strong></td>
                    <td>Token, IP adresi, user-agent</td>
                    <td>Oturum güvenliği, denetim kaydı</td>
                    <td>Meşru menfaat (md. 5/2-f)</td>
                </tr>
                <tr>
                    <td><strong>İş / İşlem Kayıtları</strong></td>
                    <td>Bakım raporu, malzeme kullanımı, maliyet</td>
                    <td>Hizmet sunumu, raporlama</td>
                    <td>Sözleşmenin ifası (md. 5/2-c)</td>
                </tr>
                <tr>
                    <td><strong>Finansal Veriler</strong></td>
                    <td>Gelir/gider, alacak/borç kayıtları</td>
                    <td>Muhasebe ve finansal raporlama</td>
                    <td>Yasal yükümlülük (md. 5/2-ç)</td>
                </tr>
                <tr>
                    <td><strong>Bildirim / Push Token</strong></td>
                    <td>Cihaz bildirimleri</td>
                    <td>Bakım ve ödeme hatırlatmaları</td>
                    <td>Açık rıza (md. 5/1)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 3. KİŞİSEL VERİ AKTARIMI -->
    <div class="card">
        <h2>3. Kişisel Veri Aktarımı</h2>
        <p>Kişisel verileriniz yurt içinde barındırılan sunucularda saklanmaktadır. Aşağıdaki durumlar dışında üçüncü taraflarla paylaşılmamaktadır:</p>
        <ul>
            <li>Yasal zorunluluk kapsamında yetkili kamu kurumları ile (mahkeme, savcılık vb.)</li>
            <li>Altyapı sağlayıcıları (hosting, veritabanı) — gizlilik sözleşmesi çerçevesinde</li>
        </ul>
    </div>

    <!-- 4. VERİ SAKLAMA SÜRELERİ -->
    <div class="card">
        <h2>4. Veri Saklama Süreleri</h2>
        <table>
            <thead>
                <tr><th>Veri Türü</th><th>Süre</th></tr>
            </thead>
            <tbody>
                <tr><td>Kullanıcı hesabı ve personel bilgileri</td><td>Hesap silinene kadar + 10 yıl (TTK)</td></tr>
                <tr><td>Konum geçmişi</td><td>1 yıl</td></tr>
                <tr><td>Denetim / Erişim logları</td><td>2 yıl</td></tr>
                <tr><td>Finansal kayıtlar</td><td>10 yıl (VUK)</td></tr>
                <tr><td>Bakım raporları</td><td>5 yıl</td></tr>
            </tbody>
        </table>
    </div>

    <!-- 5. KONUM VERİSİ -->
    <div class="card">
        <h2>5. Konum Verisi Hakkında Özel Bildirim</h2>
        <p>Mobil uygulama, yalnızca aşağıdaki koşullarda konum verisi toplar:</p>
        <ul>
            <li><strong>Yalnızca aktif bakım görevi sırasında</strong> — görev ekranı açıkken</li>
            <li><strong>Yalnızca ön planda (foreground)</strong> — uygulama arka planda çalışırken konum alınmaz</li>
            <li><strong>Görev tamamlandığında otomatik olarak durur</strong></li>
            <li><strong>Görev atanmamışken hiç başlamaz</strong></li>
        </ul>
        <p>Konum verisi; personelin bakım yerinde olduğunun doğrulanması, varış/ayrılış kaydı ve iş güvenliği amacıyla kullanılmaktadır.</p>
        <p>İşletim sistemi izin diyaloğu aracılığıyla verdiğiniz konuma erişim iznini istediğiniz zaman cihaz ayarlarından iptal edebilirsiniz.</p>
    </div>

    <!-- 6. HAKLAR -->
    <div class="card">
        <h2>6. Kişisel Veri Sahiplerinin Hakları (KVKK md. 11)</h2>
        <ul>
            <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
            <li>İşlenmişse buna ilişkin bilgi talep etme</li>
            <li>İşlenme amacını ve amaca uygun kullanılıp kullanılmadığını öğrenme</li>
            <li>Yurt içinde veya yurt dışında aktarıldığı üçüncü kişileri bilme</li>
            <li>Eksik veya yanlış işlenmişse düzeltilmesini isteme</li>
            <li>KVKK'nın 7. maddesinde öngörülen koşullar çerçevesinde silinmesini / yok edilmesini isteme</li>
            <li>İşlenen verilerin münhasıran otomatik sistemler vasıtasıyla analiz edilmesi suretiyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme</li>
            <li>Hukuka aykırı olarak işlenmesi sebebiyle zarara uğramanız halinde zararın giderilmesini talep etme</li>
        </ul>
        <p>Haklarınızı kullanmak için <strong>info@proaslift.com</strong> adresine e-posta gönderebilirsiniz. Talepler 30 gün içinde yanıtlanacaktır.</p>
    </div>

    <!-- 7. GİZLİLİK POLİTİKASI -->
    <div class="card">
        <h2>7. Gizlilik Politikası</h2>
        <h3>Güvenlik Önlemleri</h3>
        <ul>
            <li>Tüm veriler HTTPS (TLS 1.2+) üzerinden şifreli iletilir</li>
            <li>Şifreler bcrypt ile hash'lenerek saklanır; düz metin saklanmaz</li>
            <li>Oturum token'ları mobil cihazda Keychain/KeyStore ile şifreli saklanır</li>
            <li>API erişimi rate limiting ile korunmaktadır</li>
            <li>Denetim (audit) logları tüm kritik işlemler için tutulur</li>
        </ul>
        <h3>Çerezler (Cookies)</h3>
        <p>Web uygulaması yalnızca oturum yönetimi için zorunlu çerezler kullanmaktadır. Üçüncü taraf izleme çerezi kullanılmamaktadır.</p>
        <h3>Değişiklikler</h3>
        <p>Bu metin güncellenmesi halinde uygulama içinde bildirim yapılacaktır. Güncel metin her zaman bu sayfadan erişilebilir durumdadır.</p>
    </div>

    <div class="footer-note">
        <p>Bu metin 6698 sayılı KVKK kapsamında hazırlanmıştır.</p>
        <p>© 2026 Harmanşah Yazılım – <a href="{{ route('kullanim-sartlari') }}" style="color:#0284c7">Kullanım Şartları</a></p>
    </div>
</div>

</body>
</html>
