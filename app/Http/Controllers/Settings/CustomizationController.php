<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class CustomizationController extends Controller
{
    /**
     * Show the customization settings form.
     */
    public function edit(): Response
    {
        $settings = [
            'welcome_title' => Cache::get('customization.welcome_title', 'Welcome to Laravel'),
            'welcome_subtitle' => Cache::get('customization.welcome_subtitle', 'Get started with your application'),
            'welcome_description' => Cache::get('customization.welcome_description', 'Build something amazing with Laravel, Vue, and Inertia.'),
            'show_welcome_page' => Cache::get('customization.show_welcome_page', true),
            'primary_color' => Cache::get('customization.primary_color', '#3b82f6'),
            'secondary_color' => Cache::get('customization.secondary_color', '#8b5cf6'),
            'accent_color' => Cache::get('customization.accent_color', '#10b981'),
            'logo_text' => Cache::get('customization.logo_text', 'Laravel'),
            'favicon_url' => Cache::get('customization.favicon_url', ''),
        ];

        return Inertia::render('settings/Customization', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update welcome page settings.
     */
    public function updateWelcome(Request $request)
    {
        $validated = $request->validate([
            'welcome_title' => 'required|string|max:255',
            'welcome_subtitle' => 'required|string|max:255',
            'welcome_description' => 'required|string|max:1000',
            'show_welcome_page' => 'required|boolean',
        ]);

        Cache::put('customization.welcome_title', $validated['welcome_title']);
        Cache::put('customization.welcome_subtitle', $validated['welcome_subtitle']);
        Cache::put('customization.welcome_description', $validated['welcome_description']);
        Cache::put('customization.show_welcome_page', $validated['show_welcome_page']);

        return back()->with('success', 'Welcome page settings updated successfully.');
    }

    /**
     * Update theme color settings.
     */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        Cache::put('customization.primary_color', $validated['primary_color']);
        Cache::put('customization.secondary_color', $validated['secondary_color']);
        Cache::put('customization.accent_color', $validated['accent_color']);

        return back()->with('success', 'Theme colors updated successfully.');
    }

    /**
     * Update branding settings.
     */
    public function updateBranding(Request $request)
    {
        $validated = $request->validate([
            'logo_text' => 'required|string|max:255',
            'favicon_url' => 'nullable|url|max:500',
        ]);

        Cache::put('customization.logo_text', $validated['logo_text']);
        Cache::put('customization.favicon_url', $validated['favicon_url'] ?? '');

        return back()->with('success', 'Branding settings updated successfully.');
    }

    /**
     * Reset all customization settings to defaults.
     */
    public function reset()
    {
        $keys = [
            'customization.welcome_title',
            'customization.welcome_subtitle',
            'customization.welcome_description',
            'customization.show_welcome_page',
            'customization.primary_color',
            'customization.secondary_color',
            'customization.accent_color',
            'customization.logo_text',
            'customization.favicon_url',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        return back()->with('success', 'All customization settings have been reset to defaults.');
    }
}
