<?php

namespace Tests\Feature\V1\Controllers\Admin\Reports\PersonnelAttendanceContoller;

use App\Models\Checkin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.repots.personnel_attendance
     * @group controllers.admin.repots.personnel_attendance.index
     */
    public function test_super_admin_can_list_personnel_attendance_report()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        Checkin::factory()->count(5)->create();

        $start_date = now()->subWeek()->format('Y-m-d');
        $end_date =  now()->format('Y-m-d');

        $this->getJson("/api/v1/admin/reports/personnel/attendance?start_date=$start_date&end_date=$end_date")
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'designation',
                        'first_name',
                        'last_name',
                        'middle_name',
                        'qualifier',
                        'checkins' => [
                            ['id', 'personnel_id', 'type', 'created_at']
                        ]
                    ]
                ]
            ]);
    }
}
