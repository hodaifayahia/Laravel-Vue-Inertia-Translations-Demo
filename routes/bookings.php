<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProviderProfileController;
use App\Http\Controllers\ProviderScheduleController;
use App\Http\Controllers\SpecializationController;
use Illuminate\Support\Facades\Route;

// Booking routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Specializations (Admin only)
    Route::middleware('permission:manage bookings')->group(function () {
        Route::get('/specializations', [SpecializationController::class, 'index'])->name('specializations.index');
        Route::post('/specializations', [SpecializationController::class, 'store'])->name('specializations.store');
        Route::put('/specializations/{specialization}', [SpecializationController::class, 'update'])->name('specializations.update');
        Route::delete('/specializations/{specialization}', [SpecializationController::class, 'destroy'])->name('specializations.destroy');
    });

    // Active specializations (for booking)
    Route::get('/specializations/active', [SpecializationController::class, 'active'])->name('specializations.active');

    // Provider Profile (for users with book-sys permission)
    Route::middleware('permission:book-sys')->group(function () {
        Route::get('/provider/profile', [ProviderProfileController::class, 'show'])->name('provider.profile.show');
        Route::post('/provider/profile', [ProviderProfileController::class, 'store'])->name('provider.profile.store');
        
        // Provider Schedule
        Route::get('/provider/schedule', [ProviderScheduleController::class, 'index'])->name('provider.schedule.index');
        Route::post('/provider/schedule/bulk', [ProviderScheduleController::class, 'bulkUpdate'])->name('provider.schedule.bulk');
    });

    // Providers list (for booking)
    Route::get('/providers/{specialization}', [ProviderProfileController::class, 'bySpecialization'])->name('providers.by-specialization');
    
    // Provider details page (public)
    Route::get('/providers/{provider}/details', [ProviderProfileController::class, 'details'])->name('providers.details');
    
    // Available time slots
    Route::get('/providers/{provider}/slots', [ProviderScheduleController::class, 'availableSlots'])->name('provider.slots');

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    
    // Booking (for users with can-book permission)
    Route::middleware('permission:can-book')->group(function () {
        Route::get('/book', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    });

    // Provider actions
    Route::middleware('permission:book-sys')->group(function () {
        Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    });

    // Admin views
    Route::middleware('permission:manage bookings')->group(function () {
        Route::get('/providers', [ProviderProfileController::class, 'index'])->name('providers.index');
    });
});
