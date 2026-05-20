<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ElevatorLabel extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'label_color',
        'control_date',
        'follow_up_days',
        'due_date',
        'next_control_date',
        'status',
        'description',
        'correction_date',
        'source',
        'is_sealing_candidate',
        'inspector_name',
        'inspector_company',
        'inspector_license',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'control_date' => 'date',
        'due_date' => 'date',
        'next_control_date' => 'date',
        'correction_date' => 'date',
        'is_sealing_candidate' => 'boolean',
    ];

    // Etiket rengi kuralları
    const FOLLOW_UP_DAYS = [
        'yesil' => 365,
        'mavi' => 365,
        'sari' => 120,
        'kirmizi' => 60,
    ];

    const LABEL_COLORS = ['yesil', 'mavi', 'sari', 'kirmizi'];
    const STATUSES = ['aktif', 'tamamlandi', 'muhurlendi', 'iptal'];
    const SOURCES = ['periyodik_kontrol', 'takip_kontrol', 'manuel'];

    /**
     * Boot method - model events
     */
    protected static function boot()
    {
        parent::boot();

        // Yeni etiket oluşturulduğunda
        static::creating(function ($label) {
            // Takip süresini otomatik ata
            if (empty($label->follow_up_days)) {
                $label->follow_up_days = self::FOLLOW_UP_DAYS[$label->label_color] ?? 365;
            }

            // Son tarihi hesapla
            if ($label->control_date) {
                $label->next_control_date = Carbon::parse($label->control_date)
                    ->addDays($label->follow_up_days);
            }
        });

        // Etiket güncellendiğinde
        static::updating(function ($label) {
            // Kontrol tarihi veya takip süresi değiştiyse son tarihi yeniden hesapla
            if ($label->isDirty(['control_date', 'follow_up_days'])) {
                $label->next_control_date = Carbon::parse($label->control_date)
                    ->addDays($label->follow_up_days);
            }
        });

        // Yeni aktif etiket oluşturulduğunda eski aktif etiketi kapat
        static::created(function ($label) {
            if ($label->status === 'aktif') {
                DB::transaction(function () use ($label) {
                    self::where('building_id', $label->building_id)
                        ->where('id', '!=', $label->id)
                        ->where('status', 'aktif')
                        ->lockForUpdate()
                        ->update(['status' => 'tamamlandi']);
                });
            }
        });
    }

    /**
     * İlişkiler
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function notifications()
    {
        // elevator_label_id kolonu yok, building_id ile ilişki kur
        return $this->hasMany(ElevatorNotification::class, 'building_id', 'building_id');
    }

    public function escalations()
    {
        // elevator_label_id kolonu yok, building_id ile ilişki kur
        return $this->hasMany(ElevatorEscalation::class, 'building_id', 'building_id');
    }

    /**
     * Accessor ve Mutator metodları
     */
    public function getLabelColorTextAttribute()
    {
        $colors = [
            'yesil' => 'Yeşil (Uygun)',
            'mavi' => 'Mavi (Hafif Kusur)',
            'sari' => 'Sarı (Kusurlu)',
            'kirmizi' => 'Kırmızı (Güvensiz)',
        ];
        return $colors[$this->label_color] ?? $this->label_color;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'aktif' => 'Aktif',
            'tamamlandi' => 'Tamamlandı',
            'muhurlendi' => 'Mühürlendi',
            'iptal' => 'İptal',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getSourceTextAttribute()
    {
        $sources = [
            'periyodik_kontrol' => 'Periyodik Kontrol',
            'takip_kontrol' => 'Takip Kontrol',
            'manuel' => 'Manuel',
        ];
        return $sources[$this->source] ?? $this->source;
    }

    /**
     * Scope metodları
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByColor($query, $color)
    {
        return $query->where('label_color', $color);
    }

    public function scopeDueSoon($query, $days = 30)
    {
        return $query->where('next_control_date', '<=', now()->addDays($days))
                    ->where('status', 'aktif');
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_control_date', '<', now())
                    ->where('status', 'aktif');
    }

    public function scopeSealingCandidates($query)
    {
        return $query->where('label_color', 'kirmizi')
                    ->where('next_control_date', '<', now())
                    ->where('status', 'aktif');
    }

    /**
     * Yardımcı metodlar
     */
    public function getRemainingDays()
    {
        if (!$this->next_control_date) {
            return null;
        }

        return now('Europe/Istanbul')->diffInDays($this->next_control_date, false);
    }

    public function isOverdue()
    {
        return $this->next_control_date && $this->next_control_date < now('Europe/Istanbul');
    }

    public function isDueSoon($days = 30)
    {
        return $this->next_control_date && $this->next_control_date <= now('Europe/Istanbul')->addDays($days);
    }

    public function canBeSealed()
    {
        return $this->label_color === 'kirmizi' && $this->isOverdue();
    }

    public function getRiskLevel()
    {
        $remainingDays = $this->getRemainingDays();

        if ($remainingDays < 0) {
            return 'critical'; // Gecikmiş
        } elseif ($remainingDays <= 7) {
            return 'high'; // 7 gün kala
        } elseif ($remainingDays <= 30) {
            return 'medium'; // 30 gün kala
        } else {
            return 'low'; // Normal
        }
    }

    /**
     * Durum değiştirme metodları
     */
    public function markAsCompleted($description = null, $userId = null)
    {
        $this->update([
            'status' => 'tamamlandi',
            'correction_date' => now(),
            'description' => $description ?: $this->description,
            'updated_by' => $userId,
        ]);

        // Audit log
        AuditLog::create([
            'object_type' => 'ElevatorLabel',
            'object_id' => $this->id,
            'action' => 'status_changed',
            'description' => 'Etiket tamamlandı olarak işaretlendi',
            'user_id' => $userId,
            'old_values' => ['status' => 'aktif'],
            'new_values' => ['status' => 'tamamlandi'],
        ]);
    }

    public function markAsSealed($description = null, $userId = null)
    {
        $this->update([
            'status' => 'muhurlendi',
            // 'is_sealing_candidate' => true, // Bu kolon yok
            'description' => $description ?: $this->description,
            'updated_by' => $userId,
        ]);

        // Building'i de mühürle
        $this->building->update(['operational_status' => 'muhurlendi']);

        // Audit log
        AuditLog::create([
            'object_type' => 'ElevatorLabel',
            'object_id' => $this->id,
            'action' => 'sealed',
            'description' => 'Asansör mühürlendi',
            'user_id' => $userId,
            'old_values' => ['status' => $this->getOriginal('status')],
            'new_values' => ['status' => 'muhurlendi'],
        ]);
    }

    public function cancel($reason = null, $userId = null)
    {
        $this->update([
            'status' => 'iptal',
            'description' => $reason ?: $this->description,
            'updated_by' => $userId,
        ]);

        // Audit log
        AuditLog::create([
            'object_type' => 'ElevatorLabel',
            'object_id' => $this->id,
            'action' => 'cancelled',
            'description' => 'Etiket iptal edildi: ' . $reason,
            'user_id' => $userId,
            'old_values' => ['status' => $this->getOriginal('status')],
            'new_values' => ['status' => 'iptal'],
        ]);
    }
}
