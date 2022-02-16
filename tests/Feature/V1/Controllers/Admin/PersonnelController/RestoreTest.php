<?php

namespace Tests\Feature\V1\Controllers\Admin\PersonnelController;

use App\Models\Personnel;
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
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.restore
     */
    public function test_super_admin_can_restore_personnels()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $personnel = Personnel::factory()->create();

        $this->postJson("/api/v1/admin/personnels/$personnel->id/restore")
            ->assertSuccessful()
            ->assertJsonStructure(['message', 'data']);

        $this->assertNull($personnel->fresh()->deleted_at);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.restore
     */
    public function test_only_super_admin_can_restore_personnels()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $personnel = Personnel::factory()->create();

        $this->postJson("/api/v1/admin/personnels/$personnel->id/restore")
            ->assertForbidden();
    }
}
