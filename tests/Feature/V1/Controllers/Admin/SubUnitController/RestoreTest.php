<?php

namespace Tests\Feature\V1\Controllers\Admin\SubUnitController;

use App\Models\SubUnit;
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
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.restore
     */
    public function test_super_admin_can_restore_sub_units()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $sub_unit = SubUnit::factory()->create();

        $this->postJson("/api/v1/admin/sub-units/$sub_unit->id/restore")
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'data' => ['unit_id', 'name', 'province', 'type', 'latitude', 'longitude']
            ]);

        $this->assertNull($sub_unit->fresh()->deleted_at);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.sub-unit
     * @group controllers.admin.sub-unit.restore
     */
    public function test_only_super_admin_can_restore_sub_units()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $sub_unit = SubUnit::factory()->create();

        $this->postJson("/api/v1/admin/sub-units/$sub_unit->id/restore")
            ->assertForbidden();
    }
}
