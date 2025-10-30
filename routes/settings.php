<?php

use App\Http\Controllers\Settings\CustomizationController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SetLocaleController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    Route::get('settings/customization', [CustomizationController::class, 'edit'])
        ->name('customization.edit');
    Route::post('settings/customization/welcome', [CustomizationController::class, 'updateWelcome'])
        ->name('customization.welcome.update');
    Route::post('settings/customization/theme', [CustomizationController::class, 'updateTheme'])
        ->name('customization.theme.update');
    Route::post('settings/customization/branding', [CustomizationController::class, 'updateBranding'])
        ->name('customization.branding.update');
    Route::post('settings/customization/reset', [CustomizationController::class, 'reset'])
        ->name('customization.reset');

    Route::put('settings/locale', SetLocaleController::class);
});
