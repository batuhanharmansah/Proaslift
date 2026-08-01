<?php

namespace App\Support;

/**
 * Sistem sağlığı olaylarını (system_events) mesaj/tip/kaynağa bakarak
 * anlamlı kategorilere ayırır. Hem yeni olaylar kaydedilirken (SystemEvent::log)
 * hem de geriye dönük olarak (system-events:categorize komutu) kullanılır.
 *
 * Hiçbir kural eşleşmezse 'uncategorized' döner — hata sessizce kaybolmaz,
 * "Kategorisiz" altında görünür ve kuralları genişletmek için sinyal olur.
 */
class SystemEventCategorizer
{
    public const LABELS = [
        'database' => 'Veritabanı',
        'auth' => 'Kimlik Doğrulama / Yetkilendirme',
        'validation' => 'Doğrulama Hatası',
        'not_found' => 'Bulunamadı (404)',
        'external_service' => 'Harici Servis (SMS/E-posta/Harita)',
        'file_storage' => 'Dosya / Depolama',
        'rate_limit' => 'Hız Sınırlama (429)',
        'queue' => 'Kuyruk İşi',
        'mobile' => 'Mobil Uygulama',
        'php_error' => 'PHP / Kod Hatası',
        'uncategorized' => 'Kategorisiz',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const KEYWORD_RULES = [
        'database' => [
            'sqlstate', 'pdoexception', 'querybuilder', 'query exception', 'connection refused',
            'access denied for user', "unknown column", "doesn't exist", 'sqlstate[hy000]',
            'deadlock', 'lock wait timeout', 'too many connections', 'database "',
        ],
        'auth' => [
            'unauthenticated', 'authenticationexception', 'csrf', 'tokenmismatch',
            'yetkiniz yok', 'unauthorized', 'oturum süresi', 'invalid credentials',
        ],
        'validation' => [
            'validationexception', 'the given data was invalid', 'doğrulama hatası',
        ],
        'not_found' => [
            'modelnotfoundexception', 'notfoundhttpexception', 'no query results for model',
            'kaynak bulunamadı',
        ],
        'external_service' => [
            'curl error', 'guzzlehttp', 'connection timed out', 'twilio', 'netgsm',
            'geocod', 'google maps', 'smtp', 'swift_transportexception', 'mail could not be sent',
            'pusher', 'firebase', 'expo push', 'ssl certificate problem',
        ],
        'file_storage' => [
            'failed to open stream', 'no such file or directory', 'permission denied',
            'disk full', 'storage/framework', 'unable to write',
        ],
        'php_error' => [
            'typeerror', 'errorexception', 'argumentcounterror', 'undefined array key',
            'undefined variable', 'call to a member function', 'attempt to read property',
            'division by zero', 'parseerror',
        ],
    ];

    public static function categorize(string $type, string $source, string $message, ?string $stackTrace = null): string
    {
        if ($type === 'throttle_blocked') {
            return 'rate_limit';
        }

        if (in_array($type, ['queue_failed', 'queue_failed_import'], true)) {
            return 'queue';
        }

        if ($source === 'mobile') {
            return 'mobile';
        }

        $haystack = mb_strtolower($message . ' ' . mb_substr((string) $stackTrace, 0, 500));

        foreach (self::KEYWORD_RULES as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($haystack, $keyword)) {
                    return $category;
                }
            }
        }

        return 'uncategorized';
    }

    public static function label(?string $category): string
    {
        return self::LABELS[$category] ?? self::LABELS['uncategorized'];
    }
}
