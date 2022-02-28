<?php

namespace Tests\Feature\V1\Controllers\Personnel\CheckinController;

use App\Enums\CheckInType;
use App\Models\Checkin;
use App\Models\Personnel;
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
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.index
     */
    public function test_personnel_can_list_checkins()
    {
        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');
        Checkin::factory()->count(3)->create(['personnel_id' => $personnel->id]);

        $this->getJson('/api/v1/personnel/checkins')
            ->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    [
                        'id',
                        'personnel_id',
                        'image',
                        'type',
                        'is_accounted',
                        'latitude',
                        'longitude',
                        'remarks',
                        'admin_remarks',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
    }

    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.index
     */
    public function test_personnel_can_list_checkins_filtered_by_type()
    {
        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        $type = CheckInType::getRandomValue();

        $otherTypes = collect(CheckInType::getValues())
            ->filter(function ($t) use ($type) {
                return $t !== $type;
            });

        $otherType = Str::camel($otherTypes->random());
        $typeScope = Str::camel($type);

        $filteredCheckins = Checkin::factory()
            ->$typeScope()
            ->count(3)
            ->create(['personnel_id' => $personnel->id]);

        $unfilteredCheckins = Checkin::factory()
            ->$otherType()
            ->count(3)
            ->create(['personnel_id' => $personnel->id]);


        $this->getJson("/api/v1/personnel/checkins?filter[type]=$type")
            ->assertSuccessful()
            ->assertJsonFragment(['type' => $filteredCheckins->random()->type])
            ->assertJsonMissing(['type' => $unfilteredCheckins->random()->type]);
    }

    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.index
     */
    public function test_personnel_can_list_checkins_with_search()
    {
        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        $filteredCheckin = Checkin::factory()->create(['personnel_id' => $personnel->id]);
        $filteredCheckin->update(['created_at' => now()->subDay()]);

        $unfilteredCheckins = Checkin::factory()->count(3)->create(['personnel_id' => $personnel->id])
            ->each(function ($checkin) {
                $checkin->update(['created_at' => now()->subWeek()]);
            });

        $searchDate = $filteredCheckin->created_at->format('Y-m-d');

        $this->getJson("/api/v1/personnel/checkins?filter[search]=$searchDate")
            ->assertSuccessful()
            ->assertJsonFragment(['created_at' => $filteredCheckin->created_at->format('Y-m-d H:i:s')])
            ->assertJsonMissing(['created_at' => $unfilteredCheckins->random()->created_at->format('Y-m-d H:i:s')]);

        $this->getJson("/api/v1/personnel/checkins?filter[search]=$filteredCheckin->remarks")
            ->assertSuccessful()
            ->assertJsonFragment(['remarks' => $filteredCheckin->remarks])
            ->assertJsonMissing(['remarks' => $unfilteredCheckins->random()->remarks]);
    }

    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.index
     */
    public function test_guest_can_not_list_checkins()
    {
        $this->getJson('/api/v1/personnel/checkins')
            ->assertStatus(401);
    }
}
