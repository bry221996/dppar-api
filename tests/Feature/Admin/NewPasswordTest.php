<?php

namespace Tests\Feature\Admin;

use App\Models\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewPasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_users_can_reset_password()
    {
        $passwordReset = PasswordReset::factory()->create();

        $data = [
            'token' => $passwordReset->token,
            'email' => $passwordReset->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        $this->postJson('/api/v1/admin/reset-password', $data)
            ->assertStatus(200);
    }

    /** @group admin */
    public function test_users_can_not_reset_password_without_expired_token()
    {
        $passwordReset = PasswordReset::factory()->create([
            'expires_at' => now()->subHour()
        ]);

        $data = [
            'token' => $passwordReset->token,
            'email' => $passwordReset->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        $this->postJson('/api/v1/admin/reset-password', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    }
}
