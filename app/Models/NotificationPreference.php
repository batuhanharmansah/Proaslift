<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NotificationPreference extends Model
{
    protected $fillable = ['company_id', 'event_key', 'channel', 'is_enabled'];

    protected $casts = ['is_enabled' => 'boolean'];

    public const EVENTS = [
        'maintenance_scheduled' => 'Yeni bakım planlandığında (firma yetkilisine)',
        'maintenance_assigned' => 'Bakım işi atandığında (çalışana)',
        'maintenance_reminder' => 'Bakım öncesi hatırlatma (24 saat önce)',
        'maintenance_completed' => 'Bakım tamamlandığında',
        'issue_reported' => 'Arıza bildirildiğinde',
        'payment_due_soon' => 'Ödeme vadesi yaklaştığında',
        'payment_overdue' => 'Ödeme vadesi geçtiğinde',
        'payment_received' => 'Tahsilat alındığında',
        'payable_due_soon' => 'Ödenecek borç vadesi yaklaştığında',
    ];

    public const CHANNELS = ['push', 'sms'];

    /**
     * Tercih kaydı yoksa varsayılan olarak AÇIK kabul edilir — firma hiçbir şey
     * ayarlamadıysa mevcut (bu özellik eklenmeden önceki) davranış korunur.
     */
    public static function isEnabled(int $companyId, string $eventKey, string $channel): bool
    {
        $cacheKey = "notif_pref_{$companyId}_{$eventKey}_{$channel}";

        return Cache::remember($cacheKey, 300, function () use ($companyId, $eventKey, $channel) {
            $pref = static::where('company_id', $companyId)
                ->where('event_key', $eventKey)
                ->where('channel', $channel)
                ->first();

            return $pref ? $pref->is_enabled : true;
        });
    }

    protected static function booted(): void
    {
        static::saved(function (NotificationPreference $pref) {
            Cache::forget("notif_pref_{$pref->company_id}_{$pref->event_key}_{$pref->channel}");
        });
    }
}
