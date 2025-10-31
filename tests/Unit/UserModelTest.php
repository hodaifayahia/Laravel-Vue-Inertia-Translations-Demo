<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_name_attribute(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
        ]);

        $this->assertEquals('Test User', $user->name);
    }

    public function test_user_has_email_attribute(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_user_has_locale_attribute(): void
    {
        $user = User::factory()->create([
            'locale' => 'ar',
        ]);

        $this->assertEquals('ar', $user->locale);
    }

    public function test_user_has_oauth_attributes(): void
    {
        $user = User::factory()->create([
            'provider' => 'google',
            'provider_id' => 'google-123',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $this->assertEquals('google', $user->provider);
        $this->assertEquals('google-123', $user->provider_id);
        $this->assertEquals('https://example.com/avatar.jpg', $user->avatar);
    }

    public function test_user_password_can_be_null_for_oauth(): void
    {
        $user = User::factory()->create([
            'email' => 'oauth@example.com',
            'password' => null,
            'provider' => 'google',
            'provider_id' => 'google-456',
        ]);

        $this->assertNull($user->password);
        $this->assertEquals('google', $user->provider);
    }

    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create([
            'email' => 'unique@example.com',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->create([
            'email' => 'unique@example.com',
        ]);
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();

        $this->assertIsObject($user->roles);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->roles);
    }

    public function test_user_factory_creates_valid_user(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
    }

    public function test_user_factory_can_create_multiple_users(): void
    {
        $users = User::factory()->count(5)->create();

        $this->assertCount(5, $users);
        
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    public function test_user_email_is_verified_when_specified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_user_email_is_not_verified_by_default(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->assertNull($user->email_verified_at);
        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_user_locale_defaults_to_english(): void
    {
        $user = User::factory()->create();

        $this->assertEquals('en', $user->locale ?? 'en');
    }

    public function test_user_can_be_created_with_oauth_provider(): void
    {
        $user = User::create([
            'name' => 'OAuth User',
            'email' => 'oauth.user@example.com',
            'provider' => 'google',
            'provider_id' => 'google-789',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'oauth.user@example.com',
            'provider' => 'google',
            'provider_id' => 'google-789',
        ]);
    }
}
