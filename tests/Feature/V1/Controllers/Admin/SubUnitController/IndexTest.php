<?php

namespace Tests\Feature\V1\Controllers\Admin\SubUnitController;

use App\Enums\SubUnitType;
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
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.index
     */
    public function test_super_admin_can_list_sub_units()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $count = $this->faker()->numberBetween(1, 5);

        $subUnits = SubUnit::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/sub-units')
            ->assertStatus(200)
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment(['province' => $subUnits->random()->province]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.index
     */
    public function test_regional_police_officer_can_list_sub_units()
    {
        $regionalPoliceOfficer = User::factory()->regionalPoliceOfficer()->create();
        Sanctum::actingAs($regionalPoliceOfficer, [], 'admins');

        $filteredSubUnit = SubUnit::factory()
            ->create(['unit_id' => $regionalPoliceOfficer->unit_id]);

        $unfilteredSubUnits = SubUnit::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/sub-units")
            ->assertSuccessful()
            ->assertJsonFragment(['province' => $filteredSubUnit->province])
            ->assertJsonMissing(['province' => $unfilteredSubUnits->random()->province]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.index
     */
    public function test_ppo_and_mpo_can_not_list_sub_units()
    {
        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$ppo, $mpo]), [], 'admins');

        $this->getJson('/api/v1/admin/sub-units')
            ->assertForbidden();
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.index
     */
    public function test_super_admin_can_list_sub_units_filtered_by_status()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $status = $this->faker()->randomElement(['active', 'inactive']);

        $filteredSubUnits = SubUnit::factory()
            ->count(3)
            ->create(['status' => $status]);

        $unfilteredSubUnits = SubUnit::factory()
            ->count(3)
            ->create(['status' => $status === 'active' ? 'inactive' : 'active']);

        $this->getJson("/api/v1/admin/sub-units?filter[status]=$status")
            ->assertSuccessful()
            ->assertJsonFragment(['status' => $filteredSubUnits->random()->status])
            ->assertJsonMissing(['status' => $unfilteredSubUnits->random()->status]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.index
     */
    public function test_super_admin_can_list_sub_units_filtered_by_type()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $type = $this->faker()->randomElement([SubUnitType::PROVINCIAL, SubUnitType::CITY]);

        $filteredSubUnits = SubUnit::factory()
            ->count(3)
            ->create(['type' => $type]);

        $unfilteredSubUnits = SubUnit::factory()
            ->count(3)
            ->create(['type' => $type === SubUnitType::PROVINCIAL ? SubUnitType::CITY : SubUnitType::PROVINCIAL]);

        $this->getJson("/api/v1/admin/sub-units?filter[type]=$type")
            ->assertSuccessful()
            ->assertJsonFragment(['type' => $filteredSubUnits->random()->type])
            ->assertJsonMissing(['type' => $unfilteredSubUnits->random()->type]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.index
     */
    public function test_super_admin_can_list_sub_units_filtered_by_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $unit = Unit::factory()->create();

        $filteredSubUnit = SubUnit::factory()
            ->create(['unit_id' => $unit->id]);

        $unfilteredSubUnits = SubUnit::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/sub-units?filter[unit_id]=$unit->id")
            ->assertSuccessful()
            ->assertJsonFragment(['province' => $filteredSubUnit->province])
            ->assertJsonMissing(['province' => $unfilteredSubUnits->random()->province]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.index
     */
    public function test_super_admin_can_list_sub_units_with_search()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $filteredSubUnit = SubUnit::factory()->create();
        $unfilteredSubUnits = SubUnit::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/sub-units?filter[search]=$filteredSubUnit->province")
            ->assertSuccessful()
            ->assertJsonFragment(['province' => $filteredSubUnit->province])
            ->assertJsonMissing(['province' => $unfilteredSubUnits->random()->province]);

        $this->getJson("/api/v1/admin/sub-units?filter[search]=$filteredSubUnit->name")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredSubUnit->name])
            ->assertJsonMissing(['name' => $unfilteredSubUnits->random()->name]);

        $this->getJson("/api/v1/admin/sub-units?filter[search]=$filteredSubUnit->code")
            ->assertSuccessful()
            ->assertJsonFragment(['code' => $filteredSubUnit->code])
            ->assertJsonMissing(['code' => $unfilteredSubUnits->random()->code]);
    }
}
