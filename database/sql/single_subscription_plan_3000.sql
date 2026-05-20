-- ============================================================
-- Tek abonelik planı: 3000 TL/ay (proaslift.com şirket oluşturma)
-- Çalıştırmadan önce: uygun veritabanını seçin (USE db_adi;)
-- ============================================================

-- Mevcut planları temizle (sadece 1 plan kalacak)
DELETE FROM subscription_plans;

-- Tek plan: 3000 TL, slug = 'basic' (companies.subscription_plan enum: basic, orta, super)
INSERT INTO subscription_plans (
    name,
    slug,
    price,
    max_buildings,
    max_employees,
    features,
    description,
    is_active,
    sort_order,
    created_at,
    updated_at
) VALUES (
    'Standart Abonelik',
    'basic',
    3000.00,
    999,
    50,
    '["dashboard","building_management","maintenance","reports","support"]',
    'Aylık 3000 TL - Asansör bakım yönetim paneli',
    1,
    1,
    NOW(),
    NOW()
);

-- Kontrol (isteğe bağlı)
-- SELECT id, name, slug, price, is_active FROM subscription_plans;
