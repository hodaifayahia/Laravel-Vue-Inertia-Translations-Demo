<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProviderProfile extends Model
{
    protected $fillable = [
        'user_id',
        'specialization_id',
        'bio',
        'years_experience',
        'slot_duration',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'years_experience' => 'integer',
        'slot_duration' => 'integer',
    ];

    /**
     * Get the user that owns the provider profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specialization for this provider.
     */
    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    /**
     * Get the schedules for this provider.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ProviderSchedule::class);
    }

    /**
     * Get the appointments for this provider.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Scope a query to only include available providers.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
