<?php

namespace Tests\Feature\V1\Controllers\Admin\Reports\PersonnelAttendanceContoller;

use App\Models\Checkin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.repots.personnel_attendance
     * @group controllers.admin.repots.personnel_attendance.export
     */
    public function test_super_admin_can_export_personnel_attendance_report()
    {
        Queue::fake();

        $superAdmin = User::factory()->superAdmin()->create(['email' => 'bryan.mulingbayan96@gmail.com']);
        Sanctum::actingAs($superAdmin, [], 'admins');

        Checkin::factory()->count(5)->create();

        $start_date = now()->subWeek()->format('Y-m-d');
        $end_date =  now()->format('Y-m-d');

        $this->getJson("/api/v1/admin/reports/personnel/attendance/export?start_date=$start_date&end_date=$end_date")
            ->assertSuccessful()
            ->dump();
    }
}
