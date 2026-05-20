<?php

namespace App\Support;

class PhoneNormalizer
{
    /**
     * Normalize Turkish mobile numbers to 905xxxxxxxxx format.
     */
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === null || $digits === '') {
            return null;
        }

        if (str_starts_with($digits, '90') && strlen($digits) === 12) {
            return $digits;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            return '9' . $digits;
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '5')) {
            return '90' . $digits;
        }

        if (strlen($digits) >= 10) {
            return $digits;
        }

        return null;
    }

    public static function mask(?string $normalizedPhone): string
    {
        if (!$normalizedPhone || strlen($normalizedPhone) < 6) {
            return '***';
        }

        return substr($normalizedPhone, 0, 4) . '***' . substr($normalizedPhone, -2);
    }
}
