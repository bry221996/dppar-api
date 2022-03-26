<?php

namespace Tests\Feature\V1\Controllers\Admin\Reports\PersonnelAttendanceSummaryContoller;

use App\Enums\CheckInType;
use App\Models\Assignment;
use App\Models\Checkin;
use App\Models\Personnel;
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
     * @group controllers.admin.repots.personnel_attendance_summary
     * @group controllers.admin.repots.personnel_attendance_summary.index
     */
    public function test_super_admin_can_list_personnel_attendance_summary_report()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        Personnel::factory()->count(5)->create()->each(function ($personnel) {
            Assignment::factory()->create(['personnel_id' => $personnel->id]);
            Checkin::factory()->create(['personnel_id' => $personnel->id]);
        });

        $start_date = now()->subWeek()->format('Y-m-d');
        $end_date =  now()->format('Y-m-d');

        $this->getJson("/api/v1/admin/reports/personnel/attendance/summary?start_date=$start_date&end_date=$end_date")
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
                        'present_count',
                        'leave_count',
                        'off_duty_count',
                        'unaccounted_count',
                        'absent_count',
                        'assignment' => [
                            'personnel_id',
                            'unit_id',
                            'sub_unit_id',
                            'station_id',
                            'office_id',
                            'unit',
                            'sub_unit',
                            'station',
                            'office'
                        ]
                    ]
                ]
            ]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.repots.personnel_attendance_summary
     * @group controllers.admin.repots.personnel_attendance_summary.index
     */
    public function test_duplicate_checkin_with_same_type_will_count_as_one_on_personnel_attendance_summary_report()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $type = CheckInType::getRandomValue();

        $personnel = Personnel::factory()->create();
        Assignment::factory()->create(['personnel_id' => $personnel->id]);
        Checkin::factory()->$type()->count(2)->create(['personnel_id' => $personnel->id]);

        $start_date = now()->subWeek()->format('Y-m-d');
        $end_date =  now()->format('Y-m-d');

        $this->getJson("/api/v1/admin/reports/personnel/attendance/summary?start_date=$start_date&end_date=$end_date")
            ->assertSuccessful()
            ->assertJsonFragment([$type . "_count" => 1]);
    }
}
