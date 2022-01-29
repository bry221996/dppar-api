<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
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

}
