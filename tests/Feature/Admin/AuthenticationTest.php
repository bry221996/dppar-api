<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
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
            'device' => $this->faker->uuid,
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
            'device' => $this->faker->uuid,
        ];

        $this->postJson('/api/v1/admin/login', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @group admin */
    public function test_inactive_users_can_not_login()
    {
        $user = User::factory()->inactive()->create();

        $data = [
            'email' => $user->email,
            'password' => 'password',
            'device' => $this->faker->uuid,
        ];

        $this->postJson('/api/v1/admin/login', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @group admin */
    public function test_users_can_not_login_with_empty_credentials()
    {
        $this->postJson('/api/v1/admin/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /** @group admin */
    public function test_authenticated_admin_can_get_profile()
    {
        Sanctum::actingAs( User::factory()->create(), [], 'admins');

        $this->getJson('/api/v1/admin/me')
            ->assertStatus(200);
    }

    /** @group admin */
    public function test_authenticated_admin_can_logout()
    {
        Sanctum::actingAs( User::factory()->create(), [], 'admins');

        $this->postJson('/api/v1/admin/logout')
            ->assertStatus(200);
    }
}
