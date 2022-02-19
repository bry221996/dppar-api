<?php

namespace Tests\Feature\V1\Controllers\Admin\UnitController;

use App\Models\Unit;
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
     * @group controllers.admin.unit
     * @group controllers.admin.unit.update
     */
    public function test_super_admin_can_update_units()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();
        $data = Unit::factory()->make();

        $this->putJson("/api/v1/admin/units/$unit->id", $data->toArray())
            ->assertSuccessful()
            ->assertJsonFragment(['region' => $data->region])
            ->assertJsonStructure([
                'message',
                'data' => ['name', 'region', 'status', 'code']
            ]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.update
     */
    public function test_super_admin_can_not_update_units_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();

        $this->putJson("/api/v1/admin/units/$unit->id", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'region', 'status', 'code']);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.update
     */
    public function test_only_super_admin_can_update_units()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $unit = Unit::factory()->create();

        $this->putJson("/api/v1/admin/units/$unit->id", [])
            ->assertForbidden();
    }
}
