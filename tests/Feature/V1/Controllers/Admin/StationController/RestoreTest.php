<?php

namespace Tests\Feature\V1\Controllers\Admin\StationController;

use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RestoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.restore
     */
    public function test_super_admin_can_restore_stations()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $station = Station::factory()->create();

        $this->postJson("/api/v1/admin/stations/$station->id/restore")
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'data' => ['sub_unit_id', 'name', 'municipality', 'code']
            ]);

        $this->assertNull($station->fresh()->deleted_at);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.restore
     */
    public function test_only_super_admin_can_restore_stations()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $station = Station::factory()->create();

        $this->postJson("/api/v1/admin/stations/$station->id/restore")
            ->assertForbidden();
    }
}
