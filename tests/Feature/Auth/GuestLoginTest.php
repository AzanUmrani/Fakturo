<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_login()
    {
        $response = $this->postJson('/api/login/guest');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => [
                        'uuid',
                    ]
                ]
            ]);

        // Verify a user was created
        $this->assertDatabaseCount('users', 1);
    }

    public function test_guest_login_creates_user_without_email()
    {
        $response = $this->postJson('/api/login/guest');

        $response->assertStatus(200);

        // Get the user ID from the response
        $userId = json_decode($response->getContent(), true)['data']['user']['id'] ?? null;

        if ($userId) {
            $this->assertDatabaseHas('users', [
                'id' => $userId,
                'email' => null,
            ]);
        } else {
            // Alternative check if ID is not in the response
            $this->assertDatabaseHas('users', [
                'email' => null,
            ]);
        }
    }

    public function test_guest_login_returns_sanctum_token()
    {
        $response = $this->postJson('/api/login/guest');

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);

        // Check that the token exists and is a non-empty string
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('token', $responseData['data']);
        $this->assertIsString($responseData['data']['token']);
        $this->assertNotEmpty($responseData['data']['token']);
    }
}
