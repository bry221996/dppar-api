<?php

namespace Tests\Feature\V1\Controllers\Admin\StationController;

use App\Models\Station;
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
     * @group controllers.admin.station
     * @group controllers.admin.station.store
     */
    public function test_super_admin_can_create_stations()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = Station::factory()->make();

        $this->postJson('/api/v1/admin/stations', $data->toArray())
            ->assertSuccessful()
            ->assertJsonFragment(['municipality' => $data->municipality])
            ->assertJsonStructure([
                'message',
                'data' => ['sub_unit_id', 'name', 'municipality', 'code']
            ]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.store
     */
    public function test_super_admin_can_not_create_stations_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $this->postJson('/api/v1/admin/stations', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sub_unit_id', 'name', 'municipality', 'code']);
    }


    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.store
     */
    public function test_only_super_admin_can_create_stations()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->postJson('/api/v1/admin/stations', [])
            ->assertForbidden();
    }
}
