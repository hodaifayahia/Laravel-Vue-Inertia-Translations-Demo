<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_english(): void
    {
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_user_can_change_locale(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        // Update locale directly on model
        $user->locale = 'ar';
        $user->save();

        $this->assertEquals('ar', $user->locale);

        $user->locale = 'fr';
        $user->save();

        $this->assertEquals('fr', $user->locale);
    }

    public function test_locale_persists_across_sessions(): void
    {
        $user = User::factory()->create(['locale' => 'ar']);

        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertSuccessful();
        $this->assertEquals('ar', app()->getLocale());
    }

    public function test_guest_uses_default_locale(): void
    {
        $response = $this->get('/login');

        $response->assertSuccessful();
        // App locale might be 'en' or 'en_US' depending on configuration
        $this->assertStringContainsString('en', app()->getLocale());
    }

    public function test_translation_files_exist_for_all_locales(): void
    {
        $locales = ['en', 'ar', 'fr', 'lt'];
        
        foreach ($locales as $locale) {
            $this->assertFileExists(lang_path($locale . '/auth.php'));
            $this->assertFileExists(lang_path($locale . '/dashboard.php'));
        }
    }

    public function test_arabic_translations_are_loaded(): void
    {
        app()->setLocale('ar');

        $translation = __('auth.login');
        
        $this->assertNotEquals('auth.login', $translation);
        $this->assertIsString($translation);
    }

    public function test_french_translations_are_loaded(): void
    {
        app()->setLocale('fr');

        $translation = __('auth.login');
        
        $this->assertNotEquals('auth.login', $translation);
        $this->assertIsString($translation);
    }

    public function test_lithuanian_translations_are_loaded(): void
    {
        app()->setLocale('lt');

        $translation = __('auth.login');
        
        $this->assertNotEquals('auth.login', $translation);
        $this->assertIsString($translation);
    }

    public function test_oauth_translations_exist(): void
    {
        $locales = ['en', 'ar', 'fr', 'lt'];
        
        foreach ($locales as $locale) {
            app()->setLocale($locale);
            
            $this->assertNotEquals('auth.login_with_google', __('auth.login_with_google'));
            $this->assertNotEquals('auth.login_with_facebook', __('auth.login_with_facebook'));
            $this->assertNotEquals('auth.or', __('auth.or'));
        }
    }
}
