<?php

namespace Tests\Feature\V1\Controllers\Admin\UserController;

use App\Enums\UserRole;
use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.index
     */
    public function test_super_admin_can_list_users()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $count = $this->faker()->numberBetween(1, 5);

        $users = User::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/users')
            ->assertSuccessful()
            ->assertJsonCount($count + 1, 'data')
            ->assertJsonFragment(['email' => $users->random()->email]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.index
     */
    public function test_super_admin_can_list_users_with_search()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $filteredUser = User::factory()->create();
        $unfilteredUsers = User::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/users?filter[search]=$filteredUser->email")
            ->assertSuccessful()
            ->assertJsonFragment(['email' => $filteredUser->email])
            ->assertJsonMissing(['email' => $unfilteredUsers->random()->email]);


        $this->getJson("/api/v1/admin/users?filter[search]=$filteredUser->name")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredUser->name])
            ->assertJsonMissing(['name' => $unfilteredUsers->random()->name]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.index
     */
    public function test_super_admin_can_list_users_filtered_by_role()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $role = UserRole::getRandomValue();

        $otherRoles = collect(UserRole::getValues())
            ->filter(function ($r) use ($role) {
                return $r !== $role;
            });

        $filteredUsers = User::factory()
            ->count(3)
            ->create(['role' => $role]);

        $unfilteredUsers = User::factory()
            ->count(3)
            ->create(['role' => $otherRoles->random()]);

        $this->getJson("/api/v1/admin/users?filter[role]=$role")
            ->assertSuccessful()
            ->assertJsonFragment(['email' => $filteredUsers->random()->email])
            ->assertJsonMissing(['email' => $unfilteredUsers->random()->email]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.index
     */
    public function test_super_admin_can_list_users_filtered_by_status()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $status = $this->faker()->randomElement(['active', 'inactive']);

        $filteredUsers = User::factory()
            ->count(3)
            ->create(['status' => $status]);

        $unfilteredUsers = User::factory()
            ->count(3)
            ->create(['status' => $status === 'active' ? 'inactive' : 'active']);

        $this->getJson("/api/v1/admin/users?filter[status]=$status")
            ->assertSuccessful()
            ->assertJsonFragment(['email' => $filteredUsers->random()->email])
            ->assertJsonMissing(['email' => $unfilteredUsers->random()->email]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.index
     */
    public function test_super_admin_can_list_users_filtered_by_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();

        $filteredUsers = User::factory()
            ->count(3)
            ->create(['unit_id' => $unit->id]);

        $unfilteredUsers = User::factory()
            ->count(3)
            ->create();

        $this->getJson("/api/v1/admin/users?filter[unit_id]=$unit->id")
            ->assertSuccessful()
            ->assertJsonFragment(['email' => $filteredUsers->random()->email])
            ->assertJsonMissing(['email' => $unfilteredUsers->random()->email]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.index
     */
    public function test_super_admin_can_list_users_filtered_by_sub_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $subUnit = SubUnit::factory()->create();

        $filteredUsers = User::factory()
            ->count(3)
            ->create(['sub_unit_id' => $subUnit->id]);

        $unfilteredUsers = User::factory()
            ->count(3)
            ->create();

        $this->getJson("/api/v1/admin/users?filter[sub_unit_id]=$subUnit->id")
            ->assertSuccessful()
            ->assertJsonFragment(['email' => $filteredUsers->random()->email])
            ->assertJsonMissing(['email' => $unfilteredUsers->random()->email]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.index
     */
    public function test_super_admin_can_list_users_filtered_by_station_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $station = Station::factory()->create();

        $filteredUsers = User::factory()
            ->count(3)
            ->create(['station_id' => $station->id]);

        $unfilteredUsers = User::factory()
            ->count(3)
            ->create();

        $this->getJson("/api/v1/admin/users?filter[station_id]=$station->id")
            ->assertSuccessful()
            ->assertJsonFragment(['email' => $filteredUsers->random()->email])
            ->assertJsonMissing(['email' => $unfilteredUsers->random()->email]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.user
     * @group controllers.admin.user.index
     */
    public function test_only_super_admin_can_list_users()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->getJson('/api/v1/admin/users')
            ->assertForbidden();
    }
}
