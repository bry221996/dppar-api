<?php

namespace Tests\Feature\V1\Controllers\Admin\SubUnitController;

use App\Models\SubUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.update
     */
    public function test_super_admin_can_update_sub_units()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $sub_unit = SubUnit::factory()->create();
        $data = SubUnit::factory()->make();

        $this->putJson("/api/v1/admin/sub-units/$sub_unit->id", $data->toArray())
            ->assertSuccessful()
            ->assertJsonFragment(['province' => $data->province])
            ->assertJsonStructure([
                'message',
                'data' => ['unit_id', 'name', 'province', 'type', 'latitude', 'longitude']
            ]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.update
     */
    public function test_super_admin_can_not_update_sub_units_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $sub_unit = SubUnit::factory()->create();

        $this->putJson("/api/v1/admin/sub-units/$sub_unit->id", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['unit_id', 'name', 'province', 'type', 'latitude', 'longitude']);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.update
     */
    public function test_only_super_admin_can_update_sub_units()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $sub_unit = SubUnit::factory()->create();

        $this->putJson("/api/v1/admin/sub-units/$sub_unit->id", [])
            ->assertForbidden();
    }
}
