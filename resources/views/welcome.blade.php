<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asansör Firmaları için Dijital Dönüşüm Çözümü</title>
    <meta name="description" content="Asansör firmaları için kapsamlı dijital dönüşüm çözümü. Bakım, müşteri, bina ve finans süreçlerinizi tek panelden yönetin.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background-color: #ffffff;
            scroll-behavior: smooth;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e5e7eb;
            z-index: 1000;
            padding: 1rem 0;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #2563eb;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(37, 99, 235, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        /* Hero Section */
        .hero {
            padding: 8rem 0 4rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23e2e8f0" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #1e293b, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-text p {
            font-size: 1.25rem;
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .hero-visual {
            position: relative;
            height: 500px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.25);
            overflow: hidden;
        }

        .dashboard-mockup {
            width: 90%;
            height: 90%;
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .mockup-header {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .mockup-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .mockup-dot:nth-child(1) { background: #ef4444; }
        .mockup-dot:nth-child(2) { background: #f59e0b; }
        .mockup-dot:nth-child(3) { background: #10b981; }

        .mockup-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            height: 60%;
        }

        .mockup-card {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        .mockup-card h3 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .mockup-card p {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .mockup-chart {
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            height: 30px;
            background: linear-gradient(90deg, #2563eb, #10b981);
            border-radius: 4px;
            opacity: 0.3;
        }

        /* Section Styles */
        .section {
            padding: 5rem 0;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.125rem;
            color: #64748b;
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Problem Section */
        .problem {
            background: #f8fafc;
        }

        .problem-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .problem h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 1rem;
        }

        .problem p {
            font-size: 1.25rem;
            color: #64748b;
            line-height: 1.7;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* Modules Section */
        .modules {
            background: #f8fafc;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .module-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #2563eb;
            transition: all 0.3s;
        }

        .module-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
        }

        .module-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .module-card p {
            color: #64748b;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .module-card ul {
            list-style: none;
            margin-top: 1rem;
        }

        .module-card li {
            padding: 0.5rem 0;
            color: #64748b;
            position: relative;
            padding-left: 1.5rem;
        }

        .module-card li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
        }

        /* How It Works */
        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 4rem;
            height: 4rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        .step h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }

        .step p {
            color: #64748b;
            line-height: 1.6;
        }

        /* Pricing */
        .pricing {
            background: #f8fafc;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .pricing-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            position: relative;
            transition: all 0.3s;
        }

        .pricing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.featured {
            border: 2px solid #2563eb;
            transform: scale(1.05);
        }

        .pricing-card.featured::before {
            content: 'Popüler';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: #2563eb;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .pricing-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }

        .pricing-card .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 1rem;
        }

        .pricing-card ul {
            list-style: none;
            margin-bottom: 2rem;
        }

        .pricing-card li {
            padding: 0.5rem 0;
            color: #64748b;
            position: relative;
            padding-left: 1.5rem;
        }

        .pricing-card li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
        }

        /* Testimonials */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .testimonial-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
        }

        .testimonial-card p {
            font-style: italic;
            color: #64748b;
            margin-bottom: 1rem;
            line-height: 1.6;
            font-size: 1.1rem;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 3rem;
            height: 3rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .author-info h4 {
            font-weight: 600;
            color: #1e293b;
        }

        .author-info p {
            font-size: 0.875rem;
            color: #64748b;
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta .btn {
            background: white;
            color: #2563eb;
            font-size: 1.125rem;
            padding: 1rem 2rem;
        }

        /* Footer */
        .footer {
            background: #1e293b;
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section li {
            margin-bottom: 0.5rem;
        }

        .footer-section a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #334155;
            padding-top: 1rem;
            text-align: center;
            color: #94a3b8;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            color: #94a3b8;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .nav-links {
                display: none;
            }

            .hero-actions {
                flex-direction: column;
            }

            .btn {
                text-align: center;
            }

            .section-title {
                font-size: 2rem;
            }

            .problem h2 {
                font-size: 2rem;
            }

            .cta h2 {
                font-size: 2rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="#" class="logo">Asansör Otomasyon</a>
                <ul class="nav-links">
                    <li><a href="#features">Özellikler</a></li>
                    <li><a href="#modules">Modüller</a></li>
                    <li><a href="#contact">İletişim</a></li>
                </ul>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary">Giriş Yap</a>
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text animate-fade-in-up">
                    <h1>Asansör Firmaları için Dijital Dönüşüm Çözümü</h1>
                    <p>Bakım, müşteri, bina ve finans süreçlerinizi tek panelden yönetin. Kağıt, Excel ve telefon trafiğine son verin.</p>
                    <div class="hero-actions">
                        <a href="https://wa.me/905455865551?text=Merhaba%2C%20Asans%C3%B6r%20Otomasyon%20sistemi%20hakk%C4%B1nda%20demo%20talep%20etmek%20istiyorum." target="_blank" rel="noopener" class="btn btn-primary btn-large">Demo Talep Et</a>
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-large">Hemen Başla</a>
                    </div>
                </div>
                <div class="hero-visual animate-fade-in-up">
                    <div class="dashboard-mockup">
                        <div class="mockup-header">
                            <div class="mockup-dot"></div>
                            <div class="mockup-dot"></div>
                            <div class="mockup-dot"></div>
                        </div>
                        <div class="mockup-content">
                            <div class="mockup-card">
                                <h3>Bakım Takibi</h3>
                                <p>Gerçek zamanlı bakım durumu</p>
                                <div class="mockup-chart"></div>
                            </div>
                            <div class="mockup-card">
                                <h3>Arıza Bildirimi</h3>
                                <p>Anında müdahale sistemi</p>
                                <div class="mockup-chart"></div>
                            </div>
                            <div class="mockup-card">
                                <h3>Müşteri Yönetimi</h3>
                                <p>CRM ve iletişim</p>
                                <div class="mockup-chart"></div>
                            </div>
                            <div class="mockup-card">
                                <h3>Finans Takibi</h3>
                                <p>Gelir-gider analizi</p>
                                <div class="mockup-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Problem Section -->
    <section class="section problem">
        <div class="container">
            <div class="problem-content animate-fade-in-up">
                <h2>Kağıt, Excel, telefon trafiği ile uğraşmaya son!</h2>
                <p>Asansör firmaları; bakım takibi, müşteri ilişkileri, arıza bildirimleri ve muhasebe süreçlerinde dağınıklık yaşıyor. Biz bu süreci tek ekranda topladık.</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section" id="features">
        <div class="container">
            <h2 class="section-title">Öne Çıkan Özellikler</h2>
            <p class="section-subtitle">Asansör yönetim süreçlerinizi dijitalleştiren kapsamlı çözümler</p>

            <div class="features-grid">
                <div class="feature-card animate-fade-in-up">
                    <div class="feature-icon">✅</div>
                    <h3>Gerçek Zamanlı Bakım Takibi</h3>
                    <p>Asansör bakım süreçlerini anlık olarak takip edin, otomatik hatırlatmalar alın.</p>
                </div>

                <div class="feature-card animate-fade-in-up">
                    <div class="feature-icon">⚠️</div>
                    <h3>Arıza Bildirim & Müdahale Sistemi</h3>
                    <p>Anında arıza bildirimi, otomatik teknisyen atama ve müdahale takibi.</p>
                </div>

                <div class="feature-card animate-fade-in-up">
                    <div class="feature-icon">🏢</div>
                    <h3>Müşteri & Bina Yönetimi</h3>
                    <p>Kapsamlı müşteri ve bina bilgileri, sözleşme takibi ve iletişim geçmişi.</p>
                </div>

                <div class="feature-card animate-fade-in-up">
                    <div class="feature-icon">💰</div>
                    <h3>Gelir-Gider & Kasa Takibi</h3>
                    <p>Otomatik muhasebe entegrasyonu, fatura kesme ve ödeme takibi.</p>
                </div>

                <div class="feature-card animate-fade-in-up">
                    <div class="feature-icon">📱</div>
                    <h3>Mobil Uygulama Desteği</h3>
                    <p>Teknisyen ve müşteri mobil uygulamaları ile her yerden erişim.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="section modules" id="modules">
        <div class="container">
            <h2 class="section-title">Kapsamlı Modüller</h2>
            <p class="section-subtitle">Asansör yönetim süreçlerinizi kapsayan 7 ana modül</p>

            <div class="modules-grid">
                <div class="module-card animate-fade-in-up">
                    <h3>🏢 Bina Yönetimi Modülü</h3>
                    <p>Bina ve asansör bilgilerini kaydetme, konum, teknik bilgiler, servis periyotları</p>
                    <ul>
                        <li>Bina ve asansör kayıt sistemi</li>
                        <li>Konum, teknik bilgiler, servis periyotları</li>
                        <li>Sözleşme / garanti süreleri</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>🔧 Bakım & Arıza Takip Modülü</h3>
                    <p>Asansör bakım planı oluşturma, otomatik bakım zamanı hatırlatmaları</p>
                    <ul>
                        <li>Asansör bakım planı oluşturma</li>
                        <li>Otomatik bakım zamanı hatırlatmaları</li>
                        <li>Arıza bildirimi (müşteri → sistem → teknisyen)</li>
                        <li>Fotoğraf & video yükleyerek arıza kaydı</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>👥 Çalışan & Görev Yönetimi Modülü</h3>
                    <p>Teknisyenlere görev atama, canlı konum / iş durumu takibi</p>
                    <ul>
                        <li>Teknisyenlere görev atama</li>
                        <li>Canlı konum / iş durumu takibi</li>
                        <li>Çalışma raporları, performans ölçümü</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>📞 Müşteri Yönetimi (CRM) Modülü</h3>
                    <p>Müşteri bilgileri, iletişim geçmişi, toplu SMS / mail bilgilendirme</p>
                    <ul>
                        <li>Müşteri bilgileri, iletişim geçmişi</li>
                        <li>Toplu SMS / mail bilgilendirme</li>
                        <li>Arıza ve bakım geçmişi raporu</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>💰 Finans & Muhasebe Modülü</h3>
                    <p>Gelir-gider kaydı, kasa / banka entegrasyonu, fatura kesme & ödeme takibi</p>
                    <ul>
                        <li>Gelir-gider kaydı</li>
                        <li>Kasa / banka entegrasyonu</li>
                        <li>Fatura kesme & ödeme takibi</li>
                        <li>Aylık / yıllık finans raporları</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>📱 Mobil Uygulama (Müşteri & Teknisyen)</h3>
                    <p>Müşteri ve teknisyen mobil uygulamaları</p>
                    <ul>
                        <li>Müşteri Uygulaması: Arıza bildirimi, bakım geçmişi, ödeme görüntüleme</li>
                        <li>Teknisyen Uygulaması: Görev listesi, arıza çözüm kaydı, fotoğraf ekleme</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>📊 Yönetici Paneli (Admin Dashboard)</h3>
                    <p>Tüm modülleri tek panelden yönetme, canlı bildirimler</p>
                    <ul>
                        <li>Tüm modülleri tek panelden yönetme</li>
                        <li>Canlı bildirimler</li>
                        <li>Detaylı raporlama & istatistikler</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Nasıl Çalışır?</h2>
            <p class="section-subtitle">5 basit adımda dijital dönüşümünüzü tamamlayın</p>

            <div class="steps">
                <div class="step animate-fade-in-up">
                    <div class="step-number">1</div>
                    <h3>Müşteri veya bina sisteme eklenir</h3>
                    <p>Müşteri ve bina bilgilerini sisteme kaydedin</p>
                </div>

                <div class="step animate-fade-in-up">
                    <div class="step-number">2</div>
                    <h3>Bakım ve arıza kayıtları oluşturulur</h3>
                    <p>Bakım planları ve arıza bildirimlerini sisteme kaydedin</p>
                </div>

                <div class="step animate-fade-in-up">
                    <div class="step-number">3</div>
                    <h3>Teknisyen mobil uygulama ile görev alır</h3>
                    <p>Mobil uygulama üzerinden görevler otomatik olarak atanır</p>
                </div>

                <div class="step animate-fade-in-up">
                    <div class="step-number">4</div>
                    <h3>Müdahale sonrası rapor sisteme düşer</h3>
                    <p>Teknisyen müdahale sonrası raporu sisteme kaydeder</p>
                </div>

                <div class="step animate-fade-in-up">
                    <div class="step-number">5</div>
                    <h3>Finansal işlemler otomatik işlenir</h3>
                    <p>Fatura kesme ve ödeme işlemleri otomatik gerçekleşir</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Referanslar & Yorumlar</h2>
            <p class="section-subtitle">Başarılı dijital dönüşüm hikayeleri</p>

            <div class="testimonials-grid">
                <div class="testimonial-card animate-fade-in-up">
                    <p>"Bu sistem sayesinde bakım süreçlerimiz %70 daha verimli hale geldi. Artık kağıt işleri ile uğraşmıyoruz."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">A</div>
                        <div class="author-info">
                            <h4>Ahmet Yılmaz</h4>
                            <p>Yılmaz Asansör - Genel Müdür</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card animate-fade-in-up">
                    <p>"Müşteri memnuniyetimiz arttı. Arıza bildirimleri anında geliyor ve hızlı müdahale ediyoruz."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">M</div>
                        <div class="author-info">
                            <h4>Mehmet Kaya</h4>
                            <p>Kaya Asansör - Teknik Müdür</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card animate-fade-in-up">
                    <p>"Finansal süreçlerimiz tamamen otomatikleşti. Fatura kesme ve ödeme takibi artık çok kolay."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">F</div>
                        <div class="author-info">
                            <h4>Fatma Demir</h4>
                            <p>Demir Asansör - Muhasebe Müdürü</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta">
        <div class="container">
            <h2>Bugün Dijitalleşin, Yarın Kazanın</h2>
            <p>Asansör yönetim süreçlerinizi dijitalleştirin ve rekabette öne geçin.</p>
            <a href="https://wa.me/905455865551?text=Merhaba%2C%20Asans%C3%B6r%20Otomasyon%20sistemi%20hakk%C4%B1nda%20demo%20talep%20etmek%20istiyorum." target="_blank" rel="noopener" class="btn">Demo Talep Et</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Asansör Otomasyon</h3>
                    <p style="color: #94a3b8; margin-bottom: 1rem;">Asansör firmaları için kapsamlı dijital dönüşüm çözümü.</p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/harmansahyazilim/" target="_blank" rel="noopener">Instagram</a>
                        <a href="https://www.linkedin.com/company/harman%C5%9Fah-yaz%C4%B1l%C4%B1m/" target="_blank" rel="noopener">LinkedIn</a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>Ürün</h3>
                    <ul>
                        <li><a href="#features">Özellikler</a></li>
                        <li><a href="#modules">Modüller</a></li>
                        <li><a href="https://wa.me/905455865551?text=Merhaba%2C%20Asans%C3%B6r%20Otomasyon%20sistemi%20hakk%C4%B1nda%20demo%20talep%20etmek%20istiyorum." target="_blank" rel="noopener">Demo Talep Et</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Destek</h3>
                    <ul>
                        <li><a href="#">Yardım Merkezi</a></li>
                        <li><a href="#">Dokümantasyon</a></li>
                        <li><a href="#">API Referansı</a></li>
                        <li><a href="#">İletişim</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>İletişim</h3>
                    <ul>
                        <li>📧 <a href="mailto:info@harmansahyazilim.com">info@harmansahyazilim.com</a></li>
                        <li>📞 <a href="tel:+905455865551">0545 586 55 51</a></li>
                        <li>📍 Sarıköprü, Ömer Halisdemir Küme Evler Teknopark, Kapı No:31 Daire No:B33, 51300 Niğde Merkez/Niğde</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Asansör Otomasyon. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all cards and sections
        document.querySelectorAll('.feature-card, .module-card, .step, .testimonial-card').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
