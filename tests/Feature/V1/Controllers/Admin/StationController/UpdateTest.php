<?php

namespace Tests\Feature\V1\Controllers\Admin\StationController;

use App\Models\Station;
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
     * @group controllers.admin.station
     * @group controllers.admin.station.update
     */
    public function test_super_admin_can_update_stations()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $station = Station::factory()->create();
        $data = Station::factory()->make();

        $this->putJson("/api/v1/admin/stations/$station->id", $data->toArray())
            ->assertSuccessful()
            ->assertJsonFragment(['municipality' => $data->municipality])
            ->assertJsonStructure([
                'message',
                'data' =>  ['sub_unit_id', 'name', 'municipality', 'code']
            ]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.update
     */
    public function test_super_admin_can_not_update_stations_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $station = Station::factory()->create();

        $this->putJson("/api/v1/admin/stations/$station->id", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sub_unit_id', 'name', 'municipality', 'code']);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.update
     */
    public function test_only_super_admin_can_update_stations()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $station = Station::factory()->create();

        $this->putJson("/api/v1/admin/stations/$station->id", [])
            ->assertForbidden();
    }
}
