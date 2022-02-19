<?php

namespace Tests\Feature\V1\Controllers\Admin\UnitController;

use App\Models\Unit;
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
     * @group controllers.admin.unit
     * @group controllers.admin.unit.store
     */
    public function test_super_admin_can_create_sub_units()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = Unit::factory()->make();

        $this->postJson('/api/v1/admin/units', $data->toArray())
            ->assertSuccessful()
            ->assertJsonFragment(['region' => $data->region])
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'region', 'status', 'code']
            ]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.store
     */
    public function test_super_admin_can_not_create_sub_units_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $this->postJson('/api/v1/admin/units', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'region', 'status', 'code']);
    }


    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.store
     */
    public function test_only_super_admin_can_create_sub_units()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->postJson('/api/v1/admin/units', [])
            ->assertForbidden();
    }
}
