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

class UpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.checkin
     * @group controllers.admin.checkin.update
     */
    public function test_user_can_tag_checkin_as_absent()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $checkins = Checkin::factory()->unaccounted()->count(10)->create();

        $data = [
            'ids' => $checkins->map(function ($checkin) {
                return $checkin->id;
            })->toArray(),
            'type' => CheckInType::ABSENT,
            'sub_type' => $this->faker()->randomElement(CheckInType::getSubType(CheckInType::ABSENT)),
            'remarks' => $this->faker()->sentence
        ];

        $this->putJson('/api/v1/admin/checkins', $data)->assertSuccessful();

        $this->assertEquals($checkins->random()->fresh()->type, $data['type']);
        $this->assertEquals($checkins->random()->fresh()->sub_type, $data['sub_type']);
        $this->assertEquals($checkins->random()->fresh()->admin_remarks, $data['remarks']);
    }
}
