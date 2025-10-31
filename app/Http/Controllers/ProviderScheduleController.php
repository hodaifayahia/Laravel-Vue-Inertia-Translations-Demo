<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\ProviderSchedule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProviderScheduleController extends Controller
{
    /**
     * Display the provider's schedule.
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('book-sys')) {
            abort(403, 'You do not have permission to manage schedules.');
        }

        $profile = $user->providerProfile;
        
        if (!$profile) {
            return redirect()->route('provider.profile.show')
                ->with('error', 'Please complete your provider profile first.');
        }

        $schedules = $profile->schedules()->orderBy('day_of_week')->get();

        return Inertia::render('Dashboard/Bookings/Provider/Schedule', [
            'schedules' => $schedules,
            'profile' => $profile,
        ]);
    }

    /**
     * Store or update schedules in bulk.
     */
    public function bulkUpdate(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('book-sys')) {
            abort(403, 'You do not have permission to manage schedules.');
        }

        $profile = $user->providerProfile;
        
        if (!$profile) {
            return redirect()->route('provider.profile.show')
                ->withErrors(['error' => 'Please complete your provider profile first.']);
        }

        $validated = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.is_available' => 'required|boolean',
        ]);

        // Delete existing schedules
        $profile->schedules()->delete();

        // Create new schedules
        foreach ($validated['schedules'] as $scheduleData) {
            if ($scheduleData['is_available']) {
                ProviderSchedule::create([
                    'provider_profile_id' => $profile->id,
                    'day_of_week' => $scheduleData['day_of_week'],
                    'start_time' => $scheduleData['start_time'],
                    'end_time' => $scheduleData['end_time'],
                    'is_available' => true,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Schedule updated successfully!');
    }

    /**
     * Get available time slots for a provider on a specific date.
     */
    public function availableSlots(Request $request, ProviderProfile $provider)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = \Carbon\Carbon::parse($validated['date']);
        $dayOfWeek = $date->dayOfWeek;

        // Get provider's schedule for this day
        $schedule = $provider->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$schedule) {
            return response()->json([
                'slots' => [],
                'message' => 'Provider is not available on this day.',
            ]);
        }

        // Get existing appointments for this date
        $existingAppointments = $provider->appointments()
            ->where('appointment_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['start_time', 'end_time']);

        // Generate time slots
        $slots = [];
        $slotDuration = $provider->slot_duration;
        $startTime = \Carbon\Carbon::parse($schedule->start_time);
        $endTime = \Carbon\Carbon::parse($schedule->end_time);

        $currentSlot = $startTime->copy();

        while ($currentSlot->lt($endTime)) {
            $slotEnd = $currentSlot->copy()->addMinutes($slotDuration);
            
            if ($slotEnd->lte($endTime)) {
                // Check if slot is available
                $isAvailable = true;
                
                foreach ($existingAppointments as $appointment) {
                    $appointmentStart = \Carbon\Carbon::parse($appointment->start_time);
                    $appointmentEnd = \Carbon\Carbon::parse($appointment->end_time);
                    
                    if ($currentSlot->lt($appointmentEnd) && $slotEnd->gt($appointmentStart)) {
                        $isAvailable = false;
                        break;
                    }
                }

                $slots[] = [
                    'start_time' => $currentSlot->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'is_available' => $isAvailable,
                ];
            }

            $currentSlot->addMinutes($slotDuration);
        }

        return response()->json([
            'slots' => $slots,
            'date' => $date->toDateString(),
            'day_name' => $date->format('l'),
        ]);
    }
}
