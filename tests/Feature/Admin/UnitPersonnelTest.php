<?php

namespace Tests\Feature\Admin;

use App\Models\Jurisdiction;
use App\Models\Personnel;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UnitPersonnelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_regional_police_officer_can_get_unit_personnel_list()
    {
        $jurisdiction = Jurisdiction::factory()->create();
        $unitId = $jurisdiction->station->subUnit->unit->id;

        $count = $this->faker()->numberBetween(1, 10);

        Personnel::factory()
            ->count($count)
            ->create(['jurisdiction_id' => $jurisdiction->id]);

        $rpo = User::factory()
            ->regionalPoliceOfficer()
            ->create(['unit_id' => $unitId]);

        Sanctum::actingAs($rpo, [], 'admins');

        $this->getJson("/api/v1/admin/units/$unitId/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count]);
    }

    /** @group admin */
    public function test_regional_police_officer_can_not_get_unit_personnel_list_of_other_unit()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        Sanctum::actingAs($rpo, [], 'admins');

        $otherUnit = Unit::factory()->create();

        $this->getJson("/api/v1/admin/units/$otherUnit->id/personnels")
            ->assertStatus(403);
    }

    /** @group admin */
    public function test_only_regional_police_officer_can_get_unit_personnel_list()
    {
        $personnel = Personnel::factory()->create();
        $unitId = $personnel->jurisdiction->station->subUnit->unit->id;

        $rpo = User::factory()->superAdmin()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->getJson("/api/v1/admin/units/$unitId/personnels")
            ->assertStatus(403);
    }
}
