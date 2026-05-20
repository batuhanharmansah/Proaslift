<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Türkiye saat diliminde tarih formatla
     */
    public static function formatTurkish($date, $format = 'd.m.Y H:i')
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->setTimezone('Europe/Istanbul')->format($format);
    }

    /**
     * Sadece tarih formatla
     */
    public static function formatDateTurkish($date, $format = 'd.m.Y')
    {
        if (!$date) {
            return '-';
        }

        return Carbon::parse($date)->setTimezone('Europe/Istanbul')->format($format);
    }

    /**
     * Türkiye saat diliminde şu anki zaman
     */
    public static function nowTurkish()
    {
        return Carbon::now('Europe/Istanbul');
    }

    /**
     * Türkiye saat diliminde bugün
     */
    public static function todayTurkish()
    {
        return Carbon::today('Europe/Istanbul');
    }

    /**
     * İnsan dostu format (X gün önce, vb.)
     */
    public static function diffForHumansTurkish($date)
    {
        if (!$date) {
            return '-';
        }

        Carbon::setLocale('tr');
        return Carbon::parse($date)->setTimezone('Europe/Istanbul')->diffForHumans();
    }
}
