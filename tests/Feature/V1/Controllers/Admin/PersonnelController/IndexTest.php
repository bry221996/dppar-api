<?php

namespace Tests\Feature\V1\Controllers\Admin\PersonnelController;

use App\Enums\PersonnelClassification;
use App\Models\Assignment;
use App\Models\Office;
use App\Models\Personnel;
use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_super_admin_can_get_personnel_list()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();

        $count = $this->faker()->numberBetween(1, 3);

        Personnel::factory()
            ->count($count)
            ->create()
            ->each(function ($personnel) use ($unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $unit->id
                ]);
            });

        $this->getJson('/api/v1/admin/personnels')
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_super_admin_can_get_personnel_list_by_status()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $status = $this->faker()->randomElement(['active', 'inactive']);

        $unit = Unit::factory()->create();

        $count = $this->faker()->numberBetween(1, 3);

        $filteredPersonnels = Personnel::factory()
            ->count($count)
            ->create(['status' => $status])
            ->each(function ($personnel) use ($unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $unit->id
                ]);
            });

        $unfilteredPersonnels = Personnel::factory()
            ->count($count)
            ->create(['status' => $status === 'active' ? 'inactive' : 'active'])
            ->each(function ($personnel) use ($unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $unit->id
                ]);
            });

        $this->getJson("/api/v1/admin/personnels?filter[status]=$status")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $filteredPersonnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $unfilteredPersonnels->random()->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     * @dataProvider searchablePropertyProvider
     */
    public function test_super_admin_can_get_personnel_list_with_search($searchableProperty)
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $searchedPersonnel = Personnel::factory()->create();
        $personnel = Personnel::factory()->create();
        $search = $searchedPersonnel->$searchableProperty;

        $this->getJson("/api/v1/admin/personnels?filter[search]=$search")
            ->assertStatus(200)
            ->assertJsonFragment(['personnel_id' => $searchedPersonnel->personnel_id])
            ->assertJsonMissing(['personnel_id' => $personnel->personnel_id]);
    }

    public function searchablePropertyProvider()
    {
        return [
            ['badge_no'],
            ['first_name'],
            ['last_name'],
            ['middle_name'],
            ['personnel_id'],
        ];
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_super_admin_can_filter_personnel_list_by_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();

        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create()
            ->each(function ($personnel) use ($unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $unit->id
                ]);
            });

        $otherPersonnel = Personnel::factory()->create();
        Assignment::factory()->create(['personnel_id' => $otherPersonnel->id]);

        $this->getJson("/api/v1/admin/personnels?filter[unit_id]=$unit->id")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_admin_can_filter_personnel_list_by_sub_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $sub_unit = SubUnit::factory()->create();
        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create()
            ->each(function ($personnel) use ($sub_unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $sub_unit->unit_id,
                    'sub_unit_id' => $sub_unit->id,
                ]);
            });

        $otherPersonnel = Personnel::factory()->create();
        Assignment::factory()->create(['personnel_id' => $otherPersonnel->id]);

        $this->getJson("/api/v1/admin/personnels?filter[sub_unit_id]=$sub_unit->id")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_admin_can_filter_personnel_list_by_station_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $station = Station::factory()->create();
        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create()
            ->each(function ($personnel) use ($station) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $station->subUnit->unit_id,
                    'sub_unit_id' => $station->sub_unit_id,
                    'station_id' => $station->id,
                ]);
            });

        $otherPersonnel = Personnel::factory()->create();

        $this->getJson("/api/v1/admin/personnels?filter[station_id]=$station->id")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_regional_police_officer_list_personnel()
    {
        $unit = Unit::factory()->create();

        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create(['classification_id' => PersonnelClassification::REGULAR])
            ->each(function ($personnel) use ($unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $unit->id
                ]);
            });

        $flexiblePersonnels = Personnel::factory()
            ->count($count)
            ->create(['classification_id' => PersonnelClassification::FLEXIBLE_TIME])
            ->each(function ($personnel) use ($unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $unit->id
                ]);
            });

        $rpo = User::factory()->regionalPoliceOfficer()->create(['unit_id' => $unit->id]);
        $rpo->classifications()->sync([PersonnelClassification::REGULAR]);
        Sanctum::actingAs($rpo, [], 'admins');

        $otherPersonnel = Personnel::factory()->create();

        $this->getJson("/api/v1/admin/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id])
            ->assertJsonMissing(['personnel_id' => $flexiblePersonnels->random()->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_regional_police_officer_list_office_personnel()
    {
        $unit = Unit::factory()->create();
        $office = Office::factory()->regional()->create(['unit_id' => $unit->id]);

        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create(['classification_id' => PersonnelClassification::REGULAR])
            ->each(function ($personnel) use ($office) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $office->unit_id,
                    'office_id' => $office->id
                ]);
            });

        $nonOfficePersonnels = Personnel::factory()
            ->count($count)
            ->create(['classification_id' => PersonnelClassification::REGULAR])
            ->each(function ($personnel) use ($unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $unit->id
                ]);
            });

        $rpo = User::factory()->regionalPoliceOfficer()->create(['unit_id' => $unit->id]);
        $rpo->classifications()->sync([PersonnelClassification::REGULAR]);
        $rpo->offices()->sync([$office->id]);

        Sanctum::actingAs($rpo, [], 'admins');

        $this->getJson("/api/v1/admin/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $nonOfficePersonnels->random()->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_provincial_police_officer_list_personnel()
    {
        $sub_unit = SubUnit::factory()->create();
        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create()
            ->each(function ($personnel) use ($sub_unit) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $sub_unit->unit_id,
                    'sub_unit_id' => $sub_unit->id,
                ]);
            });

        $otherPersonnel = Personnel::factory()->create();
        Assignment::factory()->create(['personnel_id' => $otherPersonnel->id]);

        $ppo = User::factory()->provincialPoliceOfficer()
            ->create([
                'unit_id' => $sub_unit->unit_id,
                'sub_unit_id' => $sub_unit->id,
            ]);
        Sanctum::actingAs($ppo, [], 'admins');

        $this->getJson("/api/v1/admin/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.index
     */
    public function test_municipal_police_officer_list_personnel()
    {

        $station = Station::factory()->create();
        $count = $this->faker()->numberBetween(1, 3);

        $personnels = Personnel::factory()
            ->count($count)
            ->create()
            ->each(function ($personnel) use ($station) {
                Assignment::factory()->create([
                    'personnel_id' => $personnel->id,
                    'unit_id' => $station->subUnit->unit_id,
                    'sub_unit_id' => $station->sub_unit_id,
                    'station_id' => $station->id,
                ]);
            });

        $otherPersonnel = Personnel::factory()->create();
        Assignment::factory()->create(['personnel_id' => $otherPersonnel->id]);

        $mpo = User::factory()->municipalPoliceOfficer()
            ->create([
                'unit_id' => $station->subUnit->unit_id,
                'sub_unit_id' => $station->sub_unit_id,
                'station_id' => $station->id,
            ]);

        Sanctum::actingAs($mpo, [], 'admins');

        $this->getJson("/api/v1/admin/personnels")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $personnels->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }
}
