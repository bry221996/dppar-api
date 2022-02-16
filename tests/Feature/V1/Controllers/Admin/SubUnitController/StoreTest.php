<?php

namespace Tests\Feature\V1\Controllers\Admin\SubUnitController;

use App\Models\SubUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.store
     */
    public function test_super_admin_can_create_sub_units()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = SubUnit::factory()->make();

        $this->postJson('/api/v1/admin/sub-units', $data->toArray())
            ->assertSuccessful()
            ->assertJsonFragment(['province' => $data->province])
            ->assertJsonStructure([
                'message',
                'data' => ['unit_id', 'name', 'province', 'type', 'code']
            ]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.store
     */
    public function test_super_admin_can_not_create_sub_units_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $this->postJson('/api/v1/admin/sub-units', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['unit_id', 'name', 'province', 'type', 'code']);
    }


    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.store
     */
    public function test_only_super_admin_can_create_sub_units()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->postJson('/api/v1/admin/sub-units', [])
            ->assertForbidden();
    }
}
