<?php

namespace Tests\Feature\Auth;

use App\Http\Services\SocialAuthService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_social_provider()
    {
        // Test for each social provider
        foreach (array_keys(SocialAuthService::SOCIAL_PROVIDERS) as $provider) {
            $response = $this->postJson('/api/login/social', [
                'provider' => $provider,
                'token' => 'valid_social_token_12345678901234567890',
                'extra' => ['name' => 'Test User', 'email' => 'social@example.com'],
            ]);

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'token',
                        'user' => [
                            'uuid',
                        ]
                    ]
                ]);

            // Check that a user was created with the social token
            $this->assertDatabaseHas('users', [
                SocialAuthService::SOCIAL_PROVIDERS[$provider]['dbField'] => 'valid_social_token_12345678901234567890',
            ]);

            // Clean up for next iteration
            User::where(SocialAuthService::SOCIAL_PROVIDERS[$provider]['dbField'], 'valid_social_token_12345678901234567890')->delete();
        }
    }

    public function test_existing_user_can_login_with_social_provider()
    {
        // Create a user with a social token
        $user = User::factory()->create([
            'google_token' => 'existing_social_token_12345678901234567890',
        ]);

        $response = $this->postJson('/api/login/social', [
            'provider' => 'google',
            'token' => 'existing_social_token_12345678901234567890',
            'extra' => ['name' => 'Test User', 'email' => 'social@example.com'],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => [
                        'uuid',
                    ]
                ]
            ]);
    }

    public function test_social_login_validation_errors()
    {
        $response = $this->postJson('/api/login/social', [
            'provider' => 'invalid_provider',
            'token' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['provider', 'token']);
    }

    public function test_social_login_requires_provider()
    {
        $response = $this->postJson('/api/login/social', [
            'token' => 'valid_social_token_12345678901234567890',
            'extra' => ['name' => 'Test User', 'email' => 'social@example.com'],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['provider']);
    }

    public function test_social_login_requires_token()
    {
        $response = $this->postJson('/api/login/social', [
            'provider' => 'google',
            'extra' => ['name' => 'Test User', 'email' => 'social@example.com'],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    }
}
