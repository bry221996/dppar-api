<?php

namespace Tests\Feature\Admin;

use App\Models\Jurisdiction;
use App\Models\Personnel;
use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StationPersonnelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_provincial_municipal_officer_can_get_station_personnel_list()
    {
        $jurisdiction = Jurisdiction::factory()->create();
        $unitId = $jurisdiction->station->subUnit->unit->id;
        $subUnitId = $jurisdiction->station->subUnit->id;
        $stationId = $jurisdiction->station->id;

        $count = $this->faker()->numberBetween(1, 10);

        Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $mpo = User::factory()
            ->municipalPoliceOfficer()
            ->create([
                'unit_id' => $unitId,
                'sub_unit_id' => $subUnitId,
                'station_id' => $stationId
            ]);

        Sanctum::actingAs($mpo, [], 'admins');

        $this->getJson("/api/v1/admin/stations/$stationId/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count]);
    }

    /** @group admin */
    public function test_municipal_police_officer_can_not_get_station_personnel_list_of_other_station()
    {
        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($mpo, [], 'admins');

        $otherStation = Station::factory()->create();

        $this->getJson("/api/v1/admin/stations/$otherStation->id/personnels")
            ->assertStatus(403);
    }

    /** @group admin */
    public function test_only_municipal_police_officer_can_get_station_personnel_list()
    {
        $personnel = Personnel::factory()->create();
        $stationId = $personnel->jurisdiction->station->id;

        $superAdmin = User::factory()->superAdmin()->create();

        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$superAdmin, $rpo, $ppo]), [], 'admins');

        $this->getJson("/api/v1/admin/stations/$stationId/personnels")
            ->assertStatus(403);
    }
}
