<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'dashboard_widgets',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dashboard_widgets' => 'array',
    ];

    // İlişkiler
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function activeRole()
    {
        return $this->hasOne(UserRole::class)->where('is_active', true)->with('role');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(MobileDeviceToken::class);
    }

    // Role kontrol metodları
    public function hasRole($roleSlug)
    {
        return $this->userRoles()
            ->whereHas('role', function ($query) use ($roleSlug) {
                $query->where('slug', $roleSlug);
            })
            ->where('is_active', true)
            ->exists();
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }

    public function isCompanyAdmin()
    {
        return $this->hasRole('company_admin');
    }

    public function isEmployee()
    {
        return $this->hasRole('employee');
    }

    public function getCurrentRole()
    {
        return $this->activeRole?->role;
    }

    public function assignRole($roleSlug, $companyId = null)
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return false;
        }

        // Aynı firmada mevcut rolü deaktif et
        if ($companyId) {
            $this->userRoles()
                ->where('company_id', $companyId)
                ->update(['is_active' => false]);
        }

        // Yeni rol ata
        return $this->userRoles()->create([
            'role_id' => $role->id,
            'company_id' => $companyId,
            'is_active' => true,
        ]);
    }

    public function removeRole($roleSlug, $companyId = null)
    {
        $query = $this->userRoles()
            ->whereHas('role', function ($q) use ($roleSlug) {
                $q->where('slug', $roleSlug);
            });

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->update(['is_active' => false]);
    }

    // Company scope kontrolü
    public function canAccessCompany($companyId)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->company_id == $companyId;
    }
}
