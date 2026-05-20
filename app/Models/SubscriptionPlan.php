<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'max_buildings',
        'max_employees',
        'features',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    // İlişkiler
    public function companies()
    {
        return $this->hasMany(Company::class, 'subscription_plan', 'slug');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    // Helper metodlar
    public function isBasic()
    {
        return $this->slug === 'basic';
    }

    public function isProfessional()
    {
        return $this->slug === 'professional';
    }

    public function isEnterprise()
    {
        return $this->slug === 'enterprise';
    }

    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }
}
