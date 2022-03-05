<?php

namespace Tests\Feature\V1\Controllers\Admin\OfficeController;

use App\Enums\OfficeClassification;
use App\Enums\OfficeType;
use App\Models\Office;
use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Str;

class IndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_super_admin_can_list_offices()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $count = $this->faker()->numberBetween(1, 5);

        $offices = Office::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/offices')
            ->assertStatus(200)
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment(['name' => $offices->random()->name]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_super_admin_can_list_offices_filtered_by_status()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $status = $this->faker()->randomElement(['active', 'inactive']);

        $filteredOffices = Office::factory()
            ->count(3)
            ->create(['status' => $status]);

        $unfilteredOffices = Office::factory()
            ->count(3)
            ->create(['status' => $status === 'active' ? 'inactive' : 'active']);

        $this->getJson("/api/v1/admin/offices?filter[status]=$status")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredOffices->random()->name])
            ->assertJsonMissing(['name' => $unfilteredOffices->random()->name]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_admin_can_list_offices_filtered_by_type()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $type = OfficeType::getRandomValue();

        $otherTypes = collect(OfficeType::getValues())
            ->filter(function ($t) use ($type) {
                return $t !== $type;
            });

        $otherType = $otherTypes->random();

        $filteredOffices = Office::factory()
            ->$type()
            ->count(3)
            ->create();

        $unfilteredOffices = Office::factory()
            ->$otherType()
            ->count(3)
            ->create();

        $this->getJson("/api/v1/admin/offices?filter[type]=$type")
            ->assertSuccessful()
            ->assertJsonFragment(['type' => $filteredOffices->random()->type])
            ->assertJsonMissing(['type' => $unfilteredOffices->random()->type]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_admin_can_list_offices_filtered_by_classification()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $classification = OfficeClassification::getRandomValue();
        $classificationScope = Str::camel($classification);

        $otherClassification = collect(OfficeClassification::getValues())
            ->filter(function ($t) use ($classification) {
                return $t !== $classification;
            });

        $otherClassification = Str::camel($otherClassification->random());

        $filteredOffices = Office::factory()
            ->$classificationScope()
            ->count(3)
            ->create();

        $unfilteredOffices = Office::factory()
            ->$otherClassification()
            ->count(3)
            ->create();

        $this->getJson("/api/v1/admin/offices?filter[classification]=$classification")
            ->assertSuccessful()
            ->assertJsonFragment(['classification' => $filteredOffices->random()->classification])
            ->assertJsonMissing(['classification' => $unfilteredOffices->random()->classification]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_admin_can_list_offices_filtered_by_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();

        $filteredOffice = Office::factory()
            ->create(['unit_id' => $unit->id]);

        $unfilteredOffices = Office::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/offices?filter[unit_id]=$unit->id")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredOffice->name])
            ->assertJsonMissing(['name' => $unfilteredOffices->random()->name]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_admin_can_list_offices_filtered_by_sub_unit_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $subUnit = SubUnit::factory()->create();

        $filteredOffice = Office::factory()
            ->provincial()
            ->create([
                'unit_id' => $subUnit->unit_id,
                'sub_unit_id' => $subUnit->id
            ]);

        $unfilteredOffices = Office::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/offices?filter[sub_unit_id]=$subUnit->id")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredOffice->name])
            ->assertJsonMissing(['name' => $unfilteredOffices->random()->name]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_admin_can_list_offices_filtered_by_station_id()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $station = Station::factory()->create();

        $filteredOffice = Office::factory()
            ->municipal()
            ->create([
                'unit_id' => $station->subUnit->unit_id,
                'sub_unit_id' => $station->sub_unit_id,
                'station_id' => $station->id
            ]);

        $unfilteredOffices = Office::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/offices?filter[station_id]=$station->id")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredOffice->name])
            ->assertJsonMissing(['name' => $unfilteredOffices->random()->name]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_super_admin_can_list_offices_with_search()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $filteredOffice = Office::factory()->create();
        $unfilteredOffices = Office::factory()->count(3)->create();

        $this->getJson("/api/v1/admin/offices?filter[search]=$filteredOffice->name")
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $filteredOffice->name])
            ->assertJsonMissing(['name' => $unfilteredOffices->random()->name]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.index
     */
    public function test_non_admin_user_can_list_offices()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();
        $ppo = User::factory()->provincialPoliceOfficer()->create();
        $mpo = User::factory()->municipalPoliceOfficer()->create();
        $user = $this->faker->randomElement([$rpo, $ppo, $mpo]);

        Sanctum::actingAs($user, [], 'admins');

        $count = $this->faker()->numberBetween(1, 3);

        $assignedOffices = Office::factory()->count($count)->create();
        $unassignedOffices = Office::factory()->count($count)->create();

        $user->offices()->sync($assignedOffices->map(function ($office) {
            return $office->id;
        }));

        $this->getJson('/api/v1/admin/offices')
            ->assertStatus(200)
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment(['name' => $assignedOffices->random()->name])
            ->assertJsonMissing(['name' => $unassignedOffices->random()->name]);
    }
}
