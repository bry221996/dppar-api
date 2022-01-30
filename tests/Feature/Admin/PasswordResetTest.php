<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_users_can_forgot_password()
    {
        Notification::fake();

        $user = User::factory()->create();
        $data = ['email' => $user->email];

        $this->postJson('/api/v1/admin/forgot-password', $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('password_resets', $data);

        Notification::assertSentTo($user, PasswordResetNotification::class);
    }

    /** @group admin */
    public function test_non_existing_email_can_not_forgot_password()
    {
        $this->postJson('/api/v1/admin/forgot-password', ['email' => $this->faker->email])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
