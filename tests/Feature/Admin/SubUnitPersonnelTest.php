<?php

namespace Tests\Feature\Admin;

use App\Models\Jurisdiction;
use App\Models\Personnel;
use App\Models\SubUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SubUnitPersonnelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_provincial_police_officer_can_get_sub_unit_personnel_list()
    {
        $jurisdiction = Jurisdiction::factory()->create();
        $unitId = $jurisdiction->station->subUnit->unit->id;
        $subUnitId = $jurisdiction->station->subUnit->id;

        $count = $this->faker()->numberBetween(1, 10);

        Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $ppo = User::factory()
            ->provincialPoliceOfficer()
            ->create(['unit_id' => $unitId, 'sub_unit_id' => $subUnitId]);

        Sanctum::actingAs($ppo, [], 'admins');

        $this->getJson("/api/v1/admin/sub-units/$subUnitId/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count]);
    }

    /** @group admin */
    public function test_provincial_police_officer_can_not_get_sub_unit_personnel_list_of_other_sub_unit()
    {
        $ppo = User::factory()->provincialPoliceOfficer()->create();

        Sanctum::actingAs($ppo, [], 'admins');

        $otherSubUnit = SubUnit::factory()->create();

        $this->getJson("/api/v1/admin/sub-units/$otherSubUnit->id/personnels")
            ->assertStatus(403);
    }

    /** @group admin */
    public function test_only_provincial_police_officer_can_get_sub_unit_personnel_list()
    {
        $personnel = Personnel::factory()->create();
        $subUnitId = $personnel->jurisdiction->station->subUnit->id;

        $rpo = User::factory()->superAdmin()->create();

        $ppo = User::factory()->regionalPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->getJson("/api/v1/admin/sub-units/$subUnitId/personnels")
            ->assertStatus(403);
    }
}
