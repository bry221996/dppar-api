<?php

namespace Tests\Feature\Admin;

use App\Models\Jurisdiction;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PersonnelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_admin_can_get_personnel_list()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $count = $this->faker()->numberBetween(1, 10);

        Personnel::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/personnels')
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count]);
    }

    public function test_admin_can_filter_personnel_list_by_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $jurisdiction = Jurisdiction::factory()->create();
        $unitId = $jurisdiction->station->subUnit->unit->id;
        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $otherPersonnel = Personnel::factory()->create();

        $this->getJson("/api/v1/admin/personnels?unit_id=$unitId")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    public function test_admin_can_filter_personnel_list_by_sub_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $jurisdiction = Jurisdiction::factory()->create();
        $subUnitId = $jurisdiction->station->subUnit->id;
        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $otherPersonnel = Personnel::factory()->create();

        $this->getJson("/api/v1/admin/personnels?sub_unit_id=$subUnitId")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    public function test_admin_can_filter_personnel_list_by_station_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $jurisdiction = Jurisdiction::factory()->create();
        $stationId = $jurisdiction->station->id;
        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $otherPersonnel = Personnel::factory()->create();

        $this->getJson("/api/v1/admin/personnels?station_id=$stationId")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    public function test_regional_police_officer_list_personnel()
    {
        $jurisdiction = Jurisdiction::factory()->create();
        $unitId = $jurisdiction->station->subUnit->unit->id;

        $rpo = User::factory()->regionalPoliceOfficer()->create(['unit_id' => $unitId]);
        Sanctum::actingAs($rpo, [], 'admins');

        $unitId = $jurisdiction->station->subUnit->unit->id;
        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $otherPersonnel = Personnel::factory()->create();

        $this->getJson("/api/v1/admin/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    public function test_provincial_police_officer_list_personnel()
    {
        $jurisdiction = Jurisdiction::factory()->create();
        $unitId = $jurisdiction->station->subUnit->unit->id;
        $subUnitId = $jurisdiction->station->subUnit->id;

        $ppo = User::factory()->provincialPoliceOfficer()
            ->create([
                'unit_id' => $unitId,
                'sub_unit_id' => $subUnitId
            ]);
        Sanctum::actingAs($ppo, [], 'admins');

        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $otherPersonnel = Personnel::factory()->create();

        $this->getJson("/api/v1/admin/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    public function test_municipal_police_officer_list_personnel()
    {
        $jurisdiction = Jurisdiction::factory()->create();
        $unitId = $jurisdiction->station->subUnit->unit_id;
        $subUnitId = $jurisdiction->station->sub_unit_id;
        $stationId = $jurisdiction->station_id;

        $mpo = User::factory()->municipalPoliceOfficer()
            ->create([
                'unit_id' => $unitId,
                'sub_unit_id' => $subUnitId,
                'station_id' => $stationId
            ]);

        Sanctum::actingAs($mpo, [], 'admins');

        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $otherPersonnel = Personnel::factory()->create();

        $this->getJson("/api/v1/admin/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }
}
