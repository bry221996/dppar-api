<?php

namespace Tests\Feature\V1\Controllers\Admin\UserController;

use App\Enums\PersonnelClassification;
use App\Enums\UserRole;
use App\Models\Office;
use App\Models\User;
use App\Notifications\CredentialNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;
use Tests\TestCase;


class StoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.store
     */
    public function test_super_admin_can_create_user()
    {
        Notification::fake();

        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $state = Str::camel($this->faker()->randomElement(UserRole::getValues()));

        $data = User::factory()->$state()->make()->toArray();
        $data['classifications'] = [PersonnelClassification::REGULAR, PersonnelClassification::FLEXIBLE_TIME];

        $this->postJson('/api/v1/admin/users', $data)
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => $data['email']]);

        $user = User::where('email', $data['email'])->first();

        $this->assertEquals($user->classifications()->count(), count($data['classifications']));

        Notification::assertSentTo($user, CredentialNotification::class);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.store
     */
    public function test_super_admin_can_create_user_with_office()
    {
        Notification::fake();

        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $state = Str::camel($this->faker()->randomElement(UserRole::getValues()));
        $office = Office::factory()->create();

        $data = User::factory()->$state()->make()->toArray();
        $data['classifications'] = [PersonnelClassification::REGULAR, PersonnelClassification::FLEXIBLE_TIME];
        $data['offices'] = [$office->id];

        $this->postJson('/api/v1/admin/users', $data)
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => $data['email']]);

        $user = User::where('email', $data['email'])->first();

        $this->assertEquals($user->classifications()->count(), count($data['classifications']));
        $this->assertEquals($user->offices()->count(), count($data['offices']));

        Notification::assertSentTo($user, CredentialNotification::class);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.store
     */
    public function test_super_admin_can_not_create_user_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $this->postJson('/api/v1/admin/users', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'name', 'role', 'status']);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.store
     */
    public function test_only_super_admin_can_create_user()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->postJson('/api/v1/admin/users', [])
            ->assertForbidden();
    }
}
