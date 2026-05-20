<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'subscription_plan',
        'subscription_status',
        'subscription_start',
        'subscription_end',
        'monthly_fee',
        'max_buildings',
        'max_employees',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'subscription_start' => 'date',
        'subscription_end' => 'date',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }
        });

        // Firma oluşturulduktan sonra otomatik admin kullanıcı oluştur
        static::created(function ($company) {
            $company->createDefaultAdminUser();
        });
    }

    /**
     * Varsayılan admin kullanıcısını oluştur
     */
    public function createDefaultAdminUser()
    {
        // Firma adından admin adı oluştur
        $adminName = $this->name . ' Admin';

        // Firma email'inden admin email'i oluştur
        $adminEmail = 'admin@' . parse_url($this->email, PHP_URL_HOST);
        if (!$adminEmail || $adminEmail === 'admin@') {
            $adminEmail = 'admin@' . Str::slug($this->name) . '.com';
        }

        // Güvenli şifre oluştur
        $adminPassword = $this->generateSecurePassword();

        // Admin kullanıcısını oluştur
        $admin = User::create([
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
            'company_id' => $this->id,
            'email_verified_at' => now(),
        ]);

        // Admin rolü ata
        $admin->assignRole('company_admin', $this->id);

        // Admin bilgilerini session'a kaydet (sadece oluşturan kişi görebilsin)
        session([
            'new_company_admin_info' => [
                'company_name' => $this->name,
                'admin_email' => $adminEmail,
                'admin_password' => $adminPassword,
                'created_at' => now()
            ]
        ]);

        return $admin;
    }

    /**
     * Güvenli şifre oluştur
     */
    private function generateSecurePassword()
    {
        $length = 12;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';

        // En az bir büyük harf, bir küçük harf, bir rakam ve bir özel karakter içermeli
        $password .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[rand(0, 25)]; // Büyük harf
        $password .= 'abcdefghijklmnopqrstuvwxyz'[rand(0, 25)]; // Küçük harf
        $password .= '0123456789'[rand(0, 9)]; // Rakam
        $password .= '!@#$%^&*'[rand(0, 7)]; // Özel karakter

        // Kalan karakterleri rastgele doldur
        for ($i = 4; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }

        // Karakterleri karıştır
        return str_shuffle($password);
    }

    /**
     * Yeni oluşturulan admin bilgilerini al
     */
    public static function getNewAdminInfo()
    {
        return session('new_company_admin_info');
    }

    /**
     * Admin bilgilerini session'dan temizle
     */
    public static function clearNewAdminInfo()
    {
        session()->forget('new_company_admin_info');
    }

    // İlişkiler
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function buildings()
    {
        return $this->hasMany(Building::class);
    }

    public function maintenanceSchedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function accountingEntries()
    {
        return $this->hasMany(AccountingEntry::class);
    }

    // Payment tablosu mevcut olmadığı için comment out edildi
    // public function payments()
    // {
    //     return $this->hasMany(Payment::class);
    // }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    // Accessor'lar
    public function getSubscriptionPlanLabelAttribute()
    {
        return match($this->subscription_plan) {
            'basic' => 'Basic',
            'orta' => 'Orta',
            'super' => 'Süper',
            default => 'Bilinmiyor'
        };
    }

    public function getSubscriptionStatusLabelAttribute()
    {
        return match($this->subscription_status) {
            'active' => 'Aktif',
            'suspended' => 'Askıya Alınmış',
            'cancelled' => 'İptal Edilmiş',
            'trial' => 'Deneme Süresi',
            default => 'Bilinmiyor'
        };
    }

    public function getSubscriptionRemainingDaysAttribute()
    {
        if (!$this->subscription_end) {
            return 0;
        }

        return max(0, now()->diffInDays($this->subscription_end, false));
    }

    public function getIsSubscriptionExpiredAttribute()
    {
        return $this->subscription_end && $this->subscription_end->isPast();
    }

    public function getIsSubscriptionExpiringSoonAttribute()
    {
        return $this->subscription_end &&
               $this->subscription_end->isFuture() &&
               $this->subscription_end->diffInDays(now(), false) <= 7;
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeExpired($query)
    {
        return $query->where('subscription_end', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('subscription_end', '<=', now()->addDays($days))
                    ->where('subscription_end', '>', now());
    }

    // Metodlar
    public function canAddBuilding()
    {
        return $this->buildings()->count() < $this->max_buildings;
    }

    public function canAddEmployee()
    {
        return $this->employees()->count() < $this->max_employees;
    }

    public function suspend()
    {
        $this->update(['is_active' => false]);
    }

    public function activate()
    {
        $this->update(['is_active' => true]);
    }
}
