<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the provider profiles for this specialization.
     */
    public function providerProfiles(): HasMany
    {
        return $this->hasMany(ProviderProfile::class);
    }

    /**
     * Get active provider profiles for this specialization.
     */
    public function activeProviders(): HasMany
    {
        return $this->hasMany(ProviderProfile::class)->where('is_available', true);
    }

    /**
     * Scope a query to only include active specializations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
