<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Display appointments for the authenticated user.
     */
    public function index()
    {
        $user = auth()->user();

        // Check if user is a provider
        $isProvider = $user->hasPermissionTo('book-sys') && $user->providerProfile;

        if ($isProvider) {
            // Provider view: show appointments with their patients
            $appointments = Appointment::where('provider_profile_id', $user->providerProfile->id)
                ->with(['user:id,name,email,avatar'])
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->paginate(20);
        } else {
            // Patient view: show their appointments with providers
            $appointments = Appointment::where('user_id', $user->id)
                ->with(['providerProfile.user:id,name,email,avatar', 'providerProfile.specialization'])
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->paginate(20);
        }

        return Inertia::render('Dashboard/Bookings/Appointments/Index', [
            'appointments' => $appointments,
            'isProvider' => $isProvider,
        ]);
    }

    /**
     * Show the booking form.
     */
    public function create()
    {
        if (!auth()->user()->hasPermissionTo('can-book')) {
            abort(403, 'You do not have permission to book appointments.');
        }

        return Inertia::render('Dashboard/Bookings/Book');
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('can-book')) {
            abort(403, 'You do not have permission to book appointments.');
        }

        $validated = $request->validate([
            'provider_profile_id' => 'required|exists:provider_profiles,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if provider is available
        $provider = ProviderProfile::findOrFail($validated['provider_profile_id']);
        
        if (!$provider->is_available) {
            return redirect()->back()->withErrors([
                'error' => 'This provider is currently unavailable.'
            ]);
        }

        // Check for conflicting appointments
        $date = \Carbon\Carbon::parse($validated['appointment_date']);
        $dayOfWeek = $date->dayOfWeek;

        $existingAppointment = Appointment::where('provider_profile_id', $provider->id)
            ->where('appointment_date', $validated['appointment_date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_time', '<=', $validated['start_time'])
                          ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->exists();

        if ($existingAppointment) {
            return redirect()->back()->withErrors([
                'error' => 'This time slot is already booked.'
            ]);
        }

        $appointment = Appointment::create([
            'provider_profile_id' => $validated['provider_profile_id'],
            'user_id' => auth()->id(),
            'appointment_date' => $validated['appointment_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment booked successfully!');
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $user = auth()->user();

        // Only provider can update status
        if (!$user->providerProfile || $appointment->provider_profile_id !== $user->providerProfile->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed,no_show',
        ]);

        $appointment->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Appointment status updated successfully!');
    }

    /**
     * Cancel appointment (by patient).
     */
    public function cancel(Appointment $appointment)
    {
        $user = auth()->user();

        // Only the patient can cancel their own appointment
        if ($appointment->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Can only cancel if status is pending or confirmed
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->back()->withErrors([
                'error' => 'Cannot cancel this appointment.'
            ]);
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Appointment cancelled successfully!');
    }

    /**
     * Show appointment details.
     */
    public function show(Appointment $appointment)
    {
        $user = auth()->user();

        // User can only view their own appointments or appointments they are providing
        if ($appointment->user_id !== $user->id && 
            (!$user->providerProfile || $appointment->provider_profile_id !== $user->providerProfile->id)) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->load([
            'user:id,name,email,avatar',
            'providerProfile.user:id,name,email,avatar',
            'providerProfile.specialization'
        ]);

        return Inertia::render('Dashboard/Bookings/Appointments/Show', [
            'appointment' => $appointment,
            'isProvider' => $user->providerProfile && $appointment->provider_profile_id === $user->providerProfile->id,
        ]);
    }
}
