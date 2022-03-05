<?php

namespace Tests\Feature\V1\Controllers\Admin\Checkin;

use App\Enums\CheckInType;
use App\Models\Checkin;
use App\Models\Personnel;
use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
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
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_super_admin_can_get_checkin_list()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $count = $this->faker()->numberBetween(1, 3);

        $checkins = Checkin::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/checkins')
            ->assertSuccessful()
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['id' => $checkins->random()->id]);
    }


    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_super_admin_can_filter_checkin_list_by_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();
        $personnel = Personnel::factory()->create();
        $personnel->assignments()->create(['unit_id' => $unit->id]);

        $count = $this->faker()->numberBetween(1, 3);
        $checkins = Checkin::factory()
            ->count($count)
            ->create(['personnel_id' => $personnel->id]);

        $otherCheckin = Checkin::factory()->create();

        $this->getJson("/api/v1/admin/checkins?filter[unit_id]=$unit->id")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $checkins->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherCheckin->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_super_admin_can_filter_checkin_list_by_sub_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();
        $subUnit = SubUnit::factory()->create(['unit_id' => $unit->id]);
        $personnel = Personnel::factory()->create();
        $personnel->assignments()->create(['unit_id' => $unit->id, 'sub_unit_id' => $subUnit->id]);

        $count = $this->faker()->numberBetween(1, 3);
        $checkins = Checkin::factory()
            ->count($count)
            ->create(['personnel_id' => $personnel->id]);

        $otherCheckin = Checkin::factory()->create();

        $this->getJson("/api/v1/admin/checkins?filter[sub_unit_id]=$subUnit->id")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $checkins->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherCheckin->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_super_admin_can_filter_checkin_list_by_station_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();

        $subUnit = SubUnit::factory()
            ->create(['unit_id' => $unit->id]);

        $station = Station::factory()
            ->create(['sub_unit_id' => $subUnit->id]);

        $personnel = Personnel::factory()->create();
        $personnel->assignments()
            ->create([
                'unit_id' => $unit->id,
                'sub_unit_id' => $subUnit->id,
                'station_id' => $station->id
            ]);

        $count = $this->faker()->numberBetween(1, 3);
        $checkins = Checkin::factory()
            ->count($count)
            ->create(['personnel_id' => $personnel->id]);

        $otherCheckin = Checkin::factory()->create();

        $this->getJson("/api/v1/admin/checkins?filter[station_id]=$station->id")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $checkins->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherCheckin->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_super_admin_can_filter_checkin_list_by_type()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $type = CheckInType::getRandomValue();

        $count = $this->faker()->numberBetween(1, 3);

        $filteredCheckins = Checkin::factory()
            ->count($count)
            ->create(['type' => $type]);

        $otherCheckin = Checkin::factory()->create();

        $this->getJson("/api/v1/admin/checkins?filter[type]=$type")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $filteredCheckins->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherCheckin->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_admin_can_list_checkins_with_start_date_and_end_date()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $startDate = Carbon::now()->subWeek()->format('Y-m-d');
        $endDate = Carbon::now()->addWeek()->format('Y-m-d');

        $count = $this->faker()->numberBetween(1, 3);

        $filteredCheckins = Checkin::factory()->count($count)->create();

        $unfilteredCheckins = Checkin::factory()->count($count)->create(['created_at' => now()->subMonth()]);

        $this->getJson("/api/v1/admin/checkins?filter[start_date]=$startDate&filter[end_date]=$endDate")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $filteredCheckins->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $unfilteredCheckins->random()->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     * @dataProvider searchablePersonnelPropertyProvider
     */
    public function test_admin_can_get_checkin_list_with_searched_personnel($searchableProperty)
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $searchedPersonnel = Personnel::factory()->create();
        $otherPersonnel = Personnel::factory()->create();

        $count = $this->faker()->numberBetween(1, 3);
        Checkin::factory()->count($count)->create(['personnel_id' => $searchedPersonnel->id]);
        Checkin::factory()->count($count)->create(['personnel_id' => $otherPersonnel->id]);
        $search = $searchedPersonnel->$searchableProperty;

        $this->getJson("/api/v1/admin/checkins?filter[personnel]=$search")
            ->assertSuccessful()
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $searchedPersonnel->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherPersonnel->personnel_id]);
    }

    public function searchablePersonnelPropertyProvider()
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
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_regional_police_officer_can_get_checkin_list()
    {
        $unit = Unit::factory()->create();
        $personnel = Personnel::factory()->create();
        $personnel->assignments()->create(['unit_id' => $unit->id]);

        $count = $this->faker()->numberBetween(1, 3);
        $checkins = Checkin::factory()
            ->count($count)
            ->create(['personnel_id' => $personnel->id]);

        $rpo = User::factory()->regionalPoliceOfficer()->create(['unit_id' => $unit->id]);
        Sanctum::actingAs($rpo, [], 'admins');

        $otherCheckin = Checkin::factory()->create();

        $this->getJson("/api/v1/admin/checkins")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $checkins->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherCheckin->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_provincial_police_officer_can_get_checkin_list()
    {
        $unit = Unit::factory()->create();
        $subUnit = SubUnit::factory()->create(['unit_id' => $unit->id]);
        $personnel = Personnel::factory()->create();
        $personnel->assignments()->create(['unit_id' => $unit->id, 'sub_unit_id' => $subUnit->id]);

        $count = $this->faker()->numberBetween(1, 3);
        $checkins = Checkin::factory()
            ->count($count)
            ->create(['personnel_id' => $personnel->id]);

        $otherCheckin = Checkin::factory()->create();

        $ppo = User::factory()->provincialPoliceOfficer()
            ->create([
                'unit_id' => $unit->id,
                'sub_unit_id' => $subUnit->id,
            ]);

        Sanctum::actingAs($ppo, [], 'admins');

        $this->getJson("/api/v1/admin/checkins")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $checkins->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherCheckin->personnel_id]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.index
     */
    public function test_municipal_police_officer_can_get_checkin_list()
    {
        $unit = Unit::factory()->create();

        $subUnit = SubUnit::factory()
            ->create(['unit_id' => $unit->id]);

        $station = Station::factory()
            ->create(['sub_unit_id' => $subUnit->id]);

        $personnel = Personnel::factory()->create();
        $personnel->assignments()
            ->create([
                'unit_id' => $unit->id,
                'sub_unit_id' => $subUnit->id,
                'station_id' => $station->id
            ]);

        $count = $this->faker()->numberBetween(1, 3);
        $checkins = Checkin::factory()
            ->count($count)
            ->create(['personnel_id' => $personnel->id]);

        $otherCheckin = Checkin::factory()->create();

        $mpo = User::factory()->municipalPoliceOfficer()
            ->create([
                'unit_id' => $unit->id,
                'sub_unit_id' => $subUnit->id,
                'station_id' => $station->id,
            ]);

        Sanctum::actingAs($mpo, [], 'admins');

        $this->getJson("/api/v1/admin/checkins")
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count])
            ->assertJsonFragment(['personnel_id' => $checkins->random()->personnel_id])
            ->assertJsonMissing(['personnel_id' => $otherCheckin->personnel_id]);
    }
}
