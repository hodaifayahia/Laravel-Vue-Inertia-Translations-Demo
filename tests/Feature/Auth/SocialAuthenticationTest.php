<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_social_auth_controller_exists(): void
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\Auth\SocialAuthController::class));
    }

    public function test_user_can_authenticate_with_google(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')
            ->andReturn('google-id-123');
        $socialiteUser->shouldReceive('getEmail')
            ->andReturn('test@example.com');
        $socialiteUser->shouldReceive('getName')
            ->andReturn('Test User');
        $socialiteUser->shouldReceive('getAvatar')
            ->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')
            ->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google', $user->provider);
        $this->assertEquals('google-id-123', $user->provider_id);
        $this->assertEquals('Test User', $user->name);
    }

    public function test_user_can_authenticate_with_facebook(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')
            ->andReturn('facebook-id-456');
        $socialiteUser->shouldReceive('getEmail')
            ->andReturn('facebook@example.com');
        $socialiteUser->shouldReceive('getName')
            ->andReturn('Facebook User');
        $socialiteUser->shouldReceive('getAvatar')
            ->andReturn('https://example.com/fb-avatar.jpg');

        Socialite::shouldReceive('driver->user')
            ->andReturn($socialiteUser);

        $response = $this->get('/auth/facebook/callback');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'facebook@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('facebook', $user->provider);
        $this->assertEquals('facebook-id-456', $user->provider_id);
    }

    public function test_existing_user_can_link_google_account(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')
            ->andReturn('google-id-789');
        $socialiteUser->shouldReceive('getEmail')
            ->andReturn('existing@example.com');
        $socialiteUser->shouldReceive('getName')
            ->andReturn('Existing User');
        $socialiteUser->shouldReceive('getAvatar')
            ->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')
            ->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $existingUser->refresh();
        $this->assertEquals('google', $existingUser->provider);
        $this->assertEquals('google-id-789', $existingUser->provider_id);
    }

    public function test_oauth_without_email_fails(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')
            ->andReturn('google-id-999');
        $socialiteUser->shouldReceive('getEmail')
            ->andReturn(null);
        $socialiteUser->shouldReceive('getName')
            ->andReturn('No Email User');
        $socialiteUser->shouldReceive('getAvatar')
            ->andReturn(null);

        Socialite::shouldReceive('driver->user')
            ->andReturn($socialiteUser);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_oauth_callback_handles_exceptions(): void
    {
        Socialite::shouldReceive('driver->user')
            ->andThrow(new \Exception('OAuth provider error'));

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_invalid_provider_returns_error(): void
    {
        $response = $this->get('/auth/invalid-provider/redirect');

        $response->assertNotFound();
    }
}
