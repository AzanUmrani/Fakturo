<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_user_can_request_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'test@example.com',
            'language' => 'en',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'reset_id',
            ]);

        // Check that a reset token was created
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_cannot_request_reset_with_invalid_email()
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'nonexistent@example.com',
            'language' => 'en',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_reset_password_with_valid_code()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('old-password'),
        ]);

        // Create a password reset token
        $resetId = 'test-reset-id';
        $code = '123456';

        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => $code,
            'reset_id' => $resetId,
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/forgot-password/reset', [
            'email' => 'test@example.com',
            'reset_id' => $resetId,
            'code' => $code,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password has been reset successfully',
            ]);

        // Check that the token was deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_cannot_reset_password_with_invalid_code()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('old-password'),
        ]);

        // Create a password reset token
        $resetId = 'test-reset-id';
        $code = '123456';

        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => $code,
            'reset_id' => $resetId,
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/forgot-password/reset', [
            'email' => 'test@example.com',
            'reset_id' => $resetId,
            'code' => 'wrong-code',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid reset code or expired',
            ]);
    }

    public function test_user_cannot_reset_password_with_expired_code()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('old-password'),
        ]);

        // Create an expired password reset token
        $resetId = 'test-reset-id';
        $code = '123456';

        DB::table('password_reset_tokens')->insert([
            'email' => 'test@example.com',
            'token' => $code,
            'reset_id' => $resetId,
            'created_at' => now()->subHours(2), // Expired (older than 1 hour)
        ]);

        $response = $this->postJson('/api/forgot-password/reset', [
            'email' => 'test@example.com',
            'reset_id' => $resetId,
            'code' => $code,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid reset code or expired',
            ]);
    }

    public function test_password_reset_validation_errors()
    {
        $response = $this->postJson('/api/forgot-password/reset', [
            'email' => 'test@example.com',
            'reset_id' => 'reset-id',
            'code' => '12345', // Too short
            'password' => 'new',  // Too short
            'password_confirmation' => 'different', // Doesn't match
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'password']);
    }
}
