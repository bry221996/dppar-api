<?php

namespace Tests\Feature\V1\Controllers\Admin\UnitController;

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
     * @group controllers.admin.unit
     * @group controllers.admin.unit.index
     */
    public function test_super_admin_can_list_units()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $count = $this->faker()->numberBetween(1, 5);

        $units = Unit::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/units')
            ->assertStatus(200)
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment(['region' => $units->random()->region]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.index
     */
    public function test_super_admin_can_list_units_filtered_by_status()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $status = $this->faker()->randomElement(['active', 'inactive']);

        $filteredUnits = Unit::factory()
            ->count(3)
            ->create(['status' => $status]);

        $unfilteredUnits = Unit::factory()
            ->count(3)
            ->create(['status' => $status === 'active' ? 'inactive' : 'active']);

        $this->getJson("/api/v1/admin/units?filter[status]=$status")
            ->assertSuccessful()
            ->assertJsonFragment(['region' => $filteredUnits->random()->region])
            ->assertJsonMissing(['region' => $unfilteredUnits->random()->region]);
    }


    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.index
     */
    public function test_super_admin_can_list_units_with_search()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $filteredUnit = Unit::factory()->create();
        $unfilteredUnits = Unit::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/units?filter[search]=$filteredUnit->region")
            ->assertSuccessful()
            ->assertJsonFragment(['region' => $filteredUnit->region])
            ->assertJsonMissing(['region' => $unfilteredUnits->random()->region]);

        $this->getJson("/api/v1/admin/units?filter[search]=$filteredUnit->name")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredUnit->name])
            ->assertJsonMissing(['name' => $unfilteredUnits->random()->name]);

        $this->getJson("/api/v1/admin/units?filter[search]=$filteredUnit->code")
            ->assertSuccessful()
            ->assertJsonFragment(['code' => $filteredUnit->code])
            ->assertJsonMissing(['code' => $unfilteredUnits->random()->code]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.index
     */
    public function test_only_super_admin_can_list_units()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->getJson('/api/v1/admin/units')
            ->assertForbidden();
    }
}
