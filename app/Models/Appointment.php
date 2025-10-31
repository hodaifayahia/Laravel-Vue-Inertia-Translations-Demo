<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'provider_profile_id',
        'user_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'reminders_sent',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'reminders_sent' => 'array',
    ];

    /**
     * Get the provider profile for this appointment.
     */
    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    /**
     * Get the user (patient) for this appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('appointment_date')
                    ->orderBy('start_time');
    }

    /**
     * Scope a query to get past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', now()->toDateString())
                    ->orderBy('appointment_date', 'desc')
                    ->orderBy('start_time', 'desc');
    }

    /**
     * Check if appointment needs a reminder.
     */
    public function needsReminder(string $type): bool
    {
        $reminders = $this->reminders_sent ?? [];
        return !in_array($type, $reminders);
    }

    /**
     * Mark a reminder as sent.
     */
    public function markReminderSent(string $type): void
    {
        $reminders = $this->reminders_sent ?? [];
        if (!in_array($type, $reminders)) {
            $reminders[] = $type;
            $this->update(['reminders_sent' => $reminders]);
        }
    }
}
