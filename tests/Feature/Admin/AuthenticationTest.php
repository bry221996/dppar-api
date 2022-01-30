<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_users_can_login_with_email_and_password()
    {
        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $this->postJson('/api/v1/admin/login', $data)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token'
            ]);
    }

    /** @group admin */
    public function test_users_can_not_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
            'password' => 'invalid-password',
        ];

        $this->postJson('/api/v1/admin/login', $data)
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    /** @group admin */
    public function test_users_can_not_login_with_empty_credentials()
    {
        $this->postJson('/api/v1/admin/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
