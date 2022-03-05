<?php

namespace Tests\Feature\V1\Controllers\Admin\StationController;

use App\Models\Station;
use App\Models\SubUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.index
     */
    public function test_super_admin_can_list_stations()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $count = $this->faker()->numberBetween(1, 5);

        $station = Station::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/stations')
            ->assertStatus(200)
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment(['municipality' => $station->random()->municipality]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.index
     */
    public function test_regional_police_officer_can_list_stations()
    {
        $regionalPoliceOfficer = User::factory()->regionalPoliceOfficer()->create();
        Sanctum::actingAs($regionalPoliceOfficer, [], 'admins');


        $subUnit = SubUnit::factory()->create(['unit_id' => $regionalPoliceOfficer->unit_id]);

        $filteredStation = Station::factory()
            ->create(['sub_unit_id' => $subUnit->id]);

        $unfilteredStations = Station::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/stations")
            ->assertSuccessful()
            ->assertJsonFragment(['code' => $filteredStation->code])
            ->assertJsonMissing(['code' => $unfilteredStations->random()->code]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.index
     */
    public function test_provincial_police_officer_can_list_stations()
    {
        $subUnit = SubUnit::factory()->create();

        $provincialPoliceOfficer = User::factory()->provincialPoliceOfficer()->create([
            'unit_id' => $subUnit->unit_id,
            'sub_unit_id' => $subUnit->id
        ]);

        Sanctum::actingAs($provincialPoliceOfficer, [], 'admins');

        $filteredStation = Station::factory()
            ->create(['sub_unit_id' => $subUnit->id]);

        $unfilteredStations = Station::factory()->count(3)->create();
        $otherSubUnit = SubUnit::factory()->create(['unit_id' => $subUnit->unit_id]);
        $otherStation = Station::factory()->create(['sub_unit_id' => $otherSubUnit->id]);

        $this->getJson("/api/v1/admin/stations")
            ->assertSuccessful()
            ->assertJsonFragment(['code' => $filteredStation->code])
            ->assertJsonMissing(['code' => $unfilteredStations->random()->code])
            ->assertJsonMissing(['code' => $otherStation->code]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.index
     */
    public function test_municipal_officer_can_list_stations()
    {
        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($mpo, [], 'admins');

        $this->getJson('/api/v1/admin/stations')
            ->assertForbidden();
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.index
     */
    public function test_super_admin_can_list_stations_filtered_by_status()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $status = $this->faker()->randomElement(['active', 'inactive']);

        $filteredStations = Station::factory()
            ->count(3)
            ->create(['status' => $status]);

        $unfilteredStations = Station::factory()
            ->count(3)
            ->create(['status' => $status === 'active' ? 'inactive' : 'active']);

        $this->getJson("/api/v1/admin/stations?filter[status]=$status")
            ->assertSuccessful()
            ->assertJsonFragment(['code' => $filteredStations->random()->code])
            ->assertJsonMissing(['code' => $unfilteredStations->random()->code]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.index
     */
    public function test_super_admin_can_list_stations_filtered_by_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $subUnit = SubUnit::factory()->create();

        $filteredStation = Station::factory()
            ->create(['sub_unit_id' => $subUnit->id]);

        $unfilteredStations = Station::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/stations?filter[unit_id]=$subUnit->unit_id")
            ->assertSuccessful()
            ->assertJsonFragment(['code' => $filteredStation->code])
            ->assertJsonMissing(['code' => $unfilteredStations->random()->code]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.index
     */
    public function test_super_admin_can_list_stations_filtered_by_sub_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $subUnit = SubUnit::factory()->create();

        $filteredStation = Station::factory()
            ->create(['sub_unit_id' => $subUnit->id]);

        $unfilteredStations = Station::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/stations?filter[sub_unit_id]=$subUnit->id")
            ->assertSuccessful()
            ->assertJsonFragment(['code' => $filteredStation->code])
            ->assertJsonMissing(['code' => $unfilteredStations->random()->code]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.index
     */
    public function test_super_admin_can_list_stations_with_search()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $filteredStation = Station::factory()->create();
        $unfilteredStations = Station::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/stations?filter[search]=$filteredStation->municipality")
            ->assertSuccessful()
            ->assertJsonFragment(['municipality' => $filteredStation->municipality])
            ->assertJsonMissing(['municipality' => $unfilteredStations->random()->municipality]);

        $this->getJson("/api/v1/admin/stations?filter[search]=$filteredStation->name")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredStation->name])
            ->assertJsonMissing(['name' => $unfilteredStations->random()->name]);

        $this->getJson("/api/v1/admin/stations?filter[search]=$filteredStation->code")
            ->assertSuccessful()
            ->assertJsonFragment(['code' => $filteredStation->code])
            ->assertJsonMissing(['code' => $unfilteredStations->random()->code]);
    }
}
