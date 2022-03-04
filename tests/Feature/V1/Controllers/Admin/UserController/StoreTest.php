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

        $state = Str::camel(UserRole::REGIONAL_POLICE_OFFICER, UserRole::PROVINCIAL_POLICE_OFFICER, UserRole::MUNICIPAL_POLICE_OFFICER);

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

        $state = Str::camel(UserRole::REGIONAL_POLICE_OFFICER, UserRole::PROVINCIAL_POLICE_OFFICER, UserRole::MUNICIPAL_POLICE_OFFICER);
        $user = User::factory()->$state()->make();

        $office = Office::factory()->create([
            'type' => explode('_', $user->role)[0],
            'unit_id' => $user->unit_id,
            'sub_unit_id' => $user->sub_unit_id,
            'station_id' => $user->station_id,
        ]);

        $data = $user->toArray();
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
    public function test_super_admin_can_not_create_super_admin_user_with_classification()
    {
        Notification::fake();

        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $user = User::factory()->superAdmin()->make();
        $data = $user->toArray();
        $data['classifications'] = PersonnelClassification::getValues();

        $this->postJson('/api/v1/admin/users', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['classifications']);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.store
     */
    public function test_super_admin_can_not_create_super_admin_user_with_office()
    {
        Notification::fake();

        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $user = User::factory()->superAdmin()->make();
        $office = Office::factory()->create();

        $data = $user->toArray();
        $data['classifications'] = PersonnelClassification::getValues();
        $data['offices'] = [$office->id];

        $this->postJson('/api/v1/admin/users', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['offices']);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.store
     */
    public function test_super_admin_can_not_create_user_with_office_outside_the_selected_jurisdiction()
    {
        Notification::fake();

        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $state = Str::camel(UserRole::REGIONAL_POLICE_OFFICER, UserRole::PROVINCIAL_POLICE_OFFICER, UserRole::MUNICIPAL_POLICE_OFFICER);
        $user = User::factory()->$state()->make();
        $office = Office::factory()->create();

        $data = $user->toArray();
        $data['classifications'] = PersonnelClassification::getValues();
        $data['offices'] = [$office->id];

        $this->postJson('/api/v1/admin/users', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['offices.0']);
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
