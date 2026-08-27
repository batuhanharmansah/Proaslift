<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asansör Bakım Firmaları İçin Yönetim Yazılımı | ProAsLift</title>
    <meta name="description" content="ProAsLift, asansör bakım firmaları için geliştirilmiş operasyon yönetim sistemidir. Binalar, bakım, rota planlama, depo, finans ve resmi belge süreçlerinizi tek panelden yönetin.">

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
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 1.35rem;
            font-weight: 800;
            color: #0b1a3a;
            text-decoration: none;
            letter-spacing: 0.02em;
        }

        .logo img {
            width: 34px;
            height: 34px;
            object-fit: contain;
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

        .hero-eyebrow {
            position: relative;
            z-index: 2;
            display: inline-block;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #1d4ed8;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            margin-bottom: 1.25rem;
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
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #1e293b, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-text p {
            font-size: 1.2rem;
            color: #64748b;
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
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
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }

        /* How it works (3 steps, right after hero) */
        .how-it-works {
            background: white;
            padding: 4rem 0;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 3.5rem;
            height: 3.5rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        .step h3 {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }

        .step p {
            color: #64748b;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Trust / KVKK section */
        .trust {
            background: #0b1a3a;
            color: white;
        }

        .trust .section-title,
        .trust .section-subtitle {
            color: white;
        }

        .trust .section-subtitle {
            color: #93a5c9;
        }

        .trust-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.75rem;
            margin-top: 3rem;
        }

        .trust-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 1rem;
            padding: 1.75rem;
        }

        .trust-card .trust-icon {
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }

        .trust-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
        }

        .trust-card p {
            color: #b6c4e3;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Problem vs Solution cards */
        .compare-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.75rem;
            margin-top: 3rem;
        }

        .compare-card {
            background: white;
            border-radius: 1rem;
            padding: 1.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06);
        }

        .compare-card .compare-label {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #dc2626;
            margin-bottom: 0.6rem;
        }

        .compare-card .old-way {
            color: #94a3b8;
            text-decoration: line-through;
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }

        .compare-card .new-way {
            color: #1e293b;
            font-size: 1.05rem;
            font-weight: 600;
            line-height: 1.5;
        }

        .compare-card .new-way::before {
            content: '✓ ';
            color: #10b981;
        }

        /* Features Grid (kept for future use) */
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

        /* Role-based flexibility */
        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.75rem;
            margin-top: 3rem;
        }

        .role-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        .role-card .role-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
            color: white;
        }

        .role-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .role-card p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* FAQ */
        .faq {
            background: #f8fafc;
        }

        .faq-list {
            max-width: 800px;
            margin: 3rem auto 0;
        }

        .faq-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .faq-item summary {
            cursor: pointer;
            list-style: none;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-item summary::-webkit-details-marker {
            display: none;
        }

        .faq-item summary::after {
            content: '+';
            font-size: 1.5rem;
            color: #2563eb;
            font-weight: 400;
            margin-left: 1rem;
            flex-shrink: 0;
        }

        .faq-item[open] summary::after {
            content: '−';
        }

        .faq-item .faq-answer {
            padding: 0 1.5rem 1.25rem;
            color: #64748b;
            line-height: 1.7;
        }

        /* CTA / Lead form Section */
        .cta {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
        }

        .cta-inner {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .cta h2 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta > .container > .cta-inner > div:first-child p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .lead-form {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        }

        .lead-form h3 {
            color: #1e293b;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        .lead-form label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
        }

        .lead-form .field {
            margin-bottom: 1rem;
        }

        .lead-form input,
        .lead-form select {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-family: inherit;
            color: #1e293b;
        }

        .lead-form input:focus,
        .lead-form select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .lead-form button {
            width: 100%;
            justify-content: center;
            margin-top: 0.5rem;
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
                font-size: 2.3rem;
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

            .cta-inner {
                grid-template-columns: 1fr;
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
                @php
                    $brandLogo = asset('brand/proaslift-logo.png');
                    $brandLogoFallback = asset('public/brand/proaslift-logo.png');
                @endphp
                <a href="#" class="logo">
                    <img src="{{ $brandLogo }}" onerror="this.onerror=null;this.src='{{ $brandLogoFallback }}';" alt="ProAsLift">
                    ProAsLift
                </a>
                <ul class="nav-links">
                    <li><a href="#nasil-calisir">Nasıl Çalışır</a></li>
                    <li><a href="#modules">Modüller</a></li>
                    <li><a href="#sss">SSS</a></li>
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
                    <span class="hero-eyebrow">Harmanşah Yazılım Güvencesiyle • Sektöre Özel Geliştirildi</span>
                    <h1>Kağıda, Excel'e ve Telefon Trafiğine Son Verin. Asansör Bakımını Tek Ekrandan Yönetin.</h1>
                    <p>Binalarınızı, personel rotanızı, depo stoklarınızı, resmi belgelerinizi (DTR, etiket) ve finansal süreçlerinizi tek bir web ve mobil panelde birleştirin. Operasyonel kaosu bitirin.</p>
                    <div class="hero-actions">
                        <a href="https://wa.me/905448946894?text=Merhaba%2C%20ProAsLift%20sistemi%20hakk%C4%B1nda%20demo%20talep%20etmek%20istiyorum." target="_blank" rel="noopener" class="btn btn-primary btn-large">Ücretsiz Demo Talep Et</a>
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-large">Hemen Başla</a>
                        <a href="tel:+905448946894" class="btn btn-secondary btn-large">📞 Bizi Arayın</a>
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
                                <h3>Rota Planlayıcı</h3>
                                <p>En verimli teknisyen rotası</p>
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

    <!-- Nasıl Çalışır (3 adım) -->
    <section class="how-it-works" id="nasil-calisir">
        <div class="container">
            <h2 class="section-title">3 Adımda Dijitalleşin</h2>
            <p class="section-subtitle">Karmaşık bir kurulum yok — binalarınızı ekleyin, bakımı planlayın, ekibiniz sahadan takip etsin.</p>

            <div class="steps">
                <div class="step animate-fade-in-up">
                    <div class="step-number">1</div>
                    <h3>Binalarınızı Ekleyin</h3>
                    <p>Bina künyelerini, asansör sayılarını ve adresleri sisteme saniyeler içinde kaydedin.</p>
                </div>

                <div class="step animate-fade-in-up">
                    <div class="step-number">2</div>
                    <h3>Bakım Planlayın / Atayın</h3>
                    <p>Rutin bakımları veya toplu planlamaları yapın, işi teknisyeninize tek tıkla atayın.</p>
                </div>

                <div class="step animate-fade-in-up">
                    <div class="step-number">3</div>
                    <h3>Sahadan Takip Edin</h3>
                    <p>Teknisyeniniz mobilde rotasını görsün, bakım tamamlandığı an ofiste her şey güncellensin.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sektörel Güven & KVKK -->
    <section class="section trust">
        <div class="container">
            <h2 class="section-title">Sektöre Özel, Güvenli Altyapı</h2>
            <p class="section-subtitle">Asansör bakım ve servis firmalarının operasyonel ihtiyaçlarına özel olarak tasarlanmıştır.</p>

            <div class="trust-grid">
                <div class="trust-card animate-fade-in-up">
                    <div class="trust-icon">🔒</div>
                    <h3>KVKK Uyumlu Altyapı</h3>
                    <p>Personel konum ve finansal verileriniz güvenle korunur.</p>
                </div>
                <div class="trust-card animate-fade-in-up">
                    <div class="trust-icon">📱</div>
                    <h3>Web & Mobil Senkronizasyon</h3>
                    <p>iOS ve Android uygulamalarıyla ofis ve saha her an iletişimde.</p>
                </div>
                <div class="trust-card animate-fade-in-up">
                    <div class="trust-icon">📋</div>
                    <h3>Mevzuata Uygunluk</h3>
                    <p>DTR, kurtarma formu ve etiket süreçleri yasal standartlara uygun şekilde dijitalleştirilir.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Problem vs Çözüm -->
    <section class="section" id="features">
        <div class="container">
            <h2 class="section-title">Tanıdık Geliyor mu?</h2>
            <p class="section-subtitle">Asansör firmaları bakım takibi, saha koordinasyonu ve belge süreçlerinde dağınıklık yaşıyor. Biz bu süreci tek ekranda topladık.</p>

            <div class="compare-grid">
                <div class="compare-card animate-fade-in-up">
                    <span class="compare-label">Bakım Takibi</span>
                    <div class="old-way">Excel'de unutulan tarihler, kaçırılan periyodik bakımlar</div>
                    <div class="new-way">Otomatik periyodik bakım planlama</div>
                </div>

                <div class="compare-card animate-fade-in-up">
                    <span class="compare-label">Saha & Rota</span>
                    <div class="old-way">Sahada kaybolan teknisyenler, verimsiz güzergahlar</div>
                    <div class="new-way">Akıllı Rota Planlayıcı ile en yakın adres sıralaması</div>
                </div>

                <div class="compare-card animate-fade-in-up">
                    <span class="compare-label">Belgeler & Etiketler</span>
                    <div class="old-way">Kaçan etiket süreleri, kağıt DTR formları</div>
                    <div class="new-way">Otomatik uyarı sistemi ve dijital DTR/kurtarma formu</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="section modules" id="modules">
        <div class="container">
            <h2 class="section-title">Modül Modül Ürün Turu</h2>
            <p class="section-subtitle">Asansör yönetim süreçlerinizin tamamını kapsayan modüller</p>

            <div class="modules-grid">
                <div class="module-card animate-fade-in-up">
                    <h3>🏢 Operasyon & Saha Yönetimi</h3>
                    <p>Bina künyeleri, personel kayıtları ve arıza bildirimleri tek yerde.</p>
                    <ul>
                        <li>Bina ve asansör kayıt sistemi</li>
                        <li>Personel ve teknisyen yönetimi</li>
                        <li>Arıza bildirimi — fotoğraflı kayıt, teknisyene anında iletim</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>🧭 Akıllı Rota Planlayıcı</h3>
                    <p>Google Maps entegrasyonu ile teknisyenin günü otomatik optimize edilir.</p>
                    <ul>
                        <li>GPS konumu veya depo adresinden başlangıç</li>
                        <li>En yakından en uzağa otomatik sıralama</li>
                        <li>Tek dokunuşla çoklu duraklı rota</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>📋 Belge ve Uygunluk</h3>
                    <p>Resmi belge ve süre takipleri otomatik uyarır.</p>
                    <ul>
                        <li>Durum Tespit Raporu (DTR) — dijital doldurma</li>
                        <li>Kurtarma formu kaydı</li>
                        <li>Etiket süresi takibi ve otomatik uyarı</li>
                    </ul>
                </div>

                <div class="module-card animate-fade-in-up">
                    <h3>📦 Depo ve Finansal Yönetim</h3>
                    <p>Stok ve muhasebe süreçleri tek panelde, role göre kısıtlı erişimle.</p>
                    <ul>
                        <li>Yedek parça / stok takibi</li>
                        <li>Gelir-gider, cari hesap, çek-senet takibi</li>
                        <li>Çalışan rolünde finansal veriler gizlenir</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Rol Bazlı Esneklik -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Kim Nasıl Kullanır?</h2>
            <p class="section-subtitle">Her kullanıcı sadece kendi işine yarayan ekranları görür.</p>

            <div class="role-grid">
                <div class="role-card animate-fade-in-up">
                    <div class="role-icon">👔</div>
                    <h3>Admin (Firma Sahibi)</h3>
                    <p>Tüm operasyon ve finans kontrolü masabaşında — bina, personel, bakım, depo ve finans tek panelde.</p>
                </div>
                <div class="role-card animate-fade-in-up">
                    <div class="role-icon">🛠️</div>
                    <h3>Teknisyen (Saha Personeli)</h3>
                    <p>Mobil uygulama üzerinden akıllı rota, atanan bakım işleri ve bakım kontrol listesi.</p>
                </div>
                <div class="role-card animate-fade-in-up">
                    <div class="role-icon">🏢</div>
                    <h3>Müşteri (Bina Yöneticisi)</h3>
                    <p>Kendi binasına özel, sınırlı ve şeffaf portal üzerinden bakım geçmişini ve belgeleri görür.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section faq" id="sss">
        <div class="container">
            <h2 class="section-title">Sıkça Sorulan Sorular</h2>
            <p class="section-subtitle">Aklınıza takılan bir şey mi var? Cevabı burada olabilir.</p>

            <div class="faq-list">
                <details class="faq-item animate-fade-in-up">
                    <summary>Mevcut Excel verilerimizi sisteme aktarabilir miyiz?</summary>
                    <div class="faq-answer">Evet, binalarınızı ve müşteri listelerinizi toplu olarak sisteme aktarmanıza yardımcı oluyoruz.</div>
                </details>
                <details class="faq-item animate-fade-in-up">
                    <summary>Kurulum ve geçiş süreci ne kadar sürer?</summary>
                    <div class="faq-answer">Sistem bulut tabanlı olduğu için ekstra sunucu kurulumu gerektirmez; birkaç saat içinde kullanmaya başlayabilirsiniz.</div>
                </details>
                <details class="faq-item animate-fade-in-up">
                    <summary>Teknisyenlerin uygulamayı öğrenmesi zor mu?</summary>
                    <div class="faq-answer">Mobil arayüz son derece sade ve anlaşılır tasarlandığı için teknisyenler ekstra eğitime ihtiyaç duymadan hemen kullanmaya başlayabilir.</div>
                </details>
                <details class="faq-item animate-fade-in-up">
                    <summary>Verilerimiz güvende mi?</summary>
                    <div class="faq-answer">Tüm verileriniz şifrelenmiş sunucularda saklanır ve KVKK standartlarına uygun şekilde işlenir.</div>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA / Lead Form Section -->
    <section class="section cta">
        <div class="container">
            <div class="cta-inner">
                <div>
                    <h2>Asansör Bakım İşinizi Dijitalleştirmeye Hazır Mısınız?</h2>
                    <p>Kağıt işlerine veda edin, ProAsLift ile operasyonunuzu bugünden modernize edin. Uzmanlarımız sistemi 15 dakikada anlatsın.</p>
                </div>
                <form class="lead-form" id="lead-form">
                    <h3>Ücretsiz Demo Talep Et</h3>
                    <div class="field">
                        <label for="lead-company">Firma Adı</label>
                        <input type="text" id="lead-company" required placeholder="Örn: Yılmaz Asansör">
                    </div>
                    <div class="field">
                        <label for="lead-name">Yetkili Ad Soyad</label>
                        <input type="text" id="lead-name" required placeholder="Adınız Soyadınız">
                    </div>
                    <div class="field">
                        <label for="lead-phone">Telefon Numarası</label>
                        <input type="tel" id="lead-phone" required placeholder="05XX XXX XX XX">
                    </div>
                    <div class="field">
                        <label for="lead-count">Yönetilen Yaklaşık Asansör Sayısı</label>
                        <select id="lead-count">
                            <option value="1-10">1-10</option>
                            <option value="11-50">11-50</option>
                            <option value="51-150">51-150</option>
                            <option value="150+">150+</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-large">Hemen Ücretsiz Demo Talep Et</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>ProAsLift</h3>
                    <p style="color: #94a3b8; margin-bottom: 1rem;">Asansör bakım firmaları için operasyon yönetim sistemi.</p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/harmansahyazilim/" target="_blank" rel="noopener">Instagram</a>
                        <a href="https://www.linkedin.com/company/harman%C5%9Fah-yaz%C4%B1l%C4%B1m/" target="_blank" rel="noopener">LinkedIn</a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>Ürün</h3>
                    <ul>
                        <li><a href="#nasil-calisir">Nasıl Çalışır</a></li>
                        <li><a href="#modules">Modüller</a></li>
                        <li><a href="#sss">SSS</a></li>
                        <li><a href="https://wa.me/905448946894?text=Merhaba%2C%20ProAsLift%20sistemi%20hakk%C4%B1nda%20demo%20talep%20etmek%20istiyorum." target="_blank" rel="noopener">Demo Talep Et</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Destek</h3>
                    <ul>
                        <li><a href="#sss">Sıkça Sorulan Sorular</a></li>
                        <li><a href="#contact">İletişim</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>İletişim</h3>
                    <ul>
                        <li>📧 <a href="mailto:info@harmansahyazilim.com">info@harmansahyazilim.com</a></li>
                        <li>📞 <a href="tel:+905448946894">0544 894 68 94</a></li>
                        <li>📍 Sarıköprü, Ömer Halisdemir Küme Evler Teknopark, Kapı No:31 Daire No:B33, 51300 Niğde Merkez/Niğde</li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Mobil Uygulama</h3>
                    <ul>
                        <li><a href="https://apps.apple.com/tr/app/proaslift/id6759846797?l=tr" target="_blank" rel="noopener"> App Store'da İndir</a></li>
                        <li><a href="https://play.google.com/store/apps/details?id=com.harmansah.asansor&pcampaignid=web_share" target="_blank" rel="noopener">▶ Google Play'de İndir</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} ProAsLift — Harmanşah Yazılım. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId.length <= 1) return;
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
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

        document.querySelectorAll('.feature-card, .module-card, .step, .trust-card, .compare-card, .role-card, .faq-item').forEach(el => {
            observer.observe(el);
        });

        // Lead form -> WhatsApp'a yönlendir (backend'e kayıt yapılmıyor)
        const leadForm = document.getElementById('lead-form');
        if (leadForm) {
            leadForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const company = document.getElementById('lead-company').value.trim();
                const name = document.getElementById('lead-name').value.trim();
                const phone = document.getElementById('lead-phone').value.trim();
                const count = document.getElementById('lead-count').value;

                const message = `Merhaba, ProAsLift demo talebim var.%0A%0AFirma: ${encodeURIComponent(company)}%0AYetkili: ${encodeURIComponent(name)}%0ATelefon: ${encodeURIComponent(phone)}%0AYönetilen asansör sayısı: ${encodeURIComponent(count)}`;

                window.open(`https://wa.me/905448946894?text=${message}`, '_blank', 'noopener');
            });
        }
    </script>
</body>
</html>
