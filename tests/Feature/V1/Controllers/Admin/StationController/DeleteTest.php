<?php

namespace Tests\Feature\V1\Controllers\Admin\StationController;

use App\Models\Station;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.delete
     */
    public function test_super_admin_can_delete_stations()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $station = Station::factory()->create();

        $this->deleteJson("/api/v1/admin/stations/$station->id")
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'data' => ['sub_unit_id', 'name', 'municipality', 'code']
            ]);

        $this->assertNotNull($station->fresh()->deleted_at);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.station
     * @group controllers.admin.station.delete
     */
    public function test_only_super_admin_can_delete_stations()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $station = Station::factory()->create();

        $this->deleteJson("/api/v1/admin/stations/$station->id")
            ->assertForbidden();
    }
}
