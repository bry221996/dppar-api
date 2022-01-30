<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_users_can_forgot_password()
    {
        $user = User::factory()->create();
        $data = ['email' => $user->email];

        $this->postJson('/api/v1/admin/forgot-password', $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('password_resets', $data);
    }
}
