<?php

namespace Tests\Feature\V1\Controllers\Admin\UserController;

use App\Enums\PersonnelClassification;
use App\Enums\UserRole;
use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.update
     */
    public function test_super_admin_can_update_user()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $state = Str::camel(UserRole::REGIONAL_POLICE_OFFICER, UserRole::PROVINCIAL_POLICE_OFFICER, UserRole::MUNICIPAL_POLICE_OFFICER);

        $user = User::factory()->$state()->create();
        $data = User::factory()->$state()->make();

        $data['classifications'] = [PersonnelClassification::REGULAR, PersonnelClassification::FLEXIBLE_TIME];

        $this->putJson("/api/v1/admin/users/$user->id", $data->toArray())
            ->assertSuccessful();

        $this->assertEquals($user->classifications()->count(), count($data['classifications']));

        $this->assertDatabaseHas('users', ['email' => $data->email]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.update
     */
    public function test_super_admin_can_update_user_with_office()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $state = Str::camel(UserRole::REGIONAL_POLICE_OFFICER, UserRole::PROVINCIAL_POLICE_OFFICER, UserRole::MUNICIPAL_POLICE_OFFICER);

        $user = User::factory()->$state()->create();
        $data = User::factory()->$state()->make();

        $data['classifications'] = [PersonnelClassification::REGULAR, PersonnelClassification::FLEXIBLE_TIME];
        $data['offices'] = [Office::factory()->create()->id];

        $this->putJson("/api/v1/admin/users/$user->id", $data->toArray())
            ->assertSuccessful();

        $this->assertEquals($user->classifications()->count(), count($data['classifications']));
        $this->assertEquals($user->offices()->count(), count($data['offices']));

        $this->assertDatabaseHas('users', ['email' => $data->email]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.update
     */
    public function test_super_admin_can_not_update_user_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $this->putJson("/api/v1/admin/users/$superAdmin->id", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'name', 'role', 'status']);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.update
     */
    public function test_only_super_admin_can_update_user()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->putJson("/api/v1/admin/users/$rpo->id", [])
            ->assertForbidden();
    }
}
