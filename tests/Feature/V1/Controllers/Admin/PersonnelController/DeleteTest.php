<?php

namespace Tests\Feature\V1\Controllers\Admin\PersonnelController;

use App\Models\Personnel;
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
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.delete
     */
    public function test_super_admin_can_delete_personnels()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $personnel = Personnel::factory()->create();

        $this->deleteJson("/api/v1/admin/personnels/$personnel->id")
            ->assertSuccessful()
            ->assertJsonStructure(['message', 'data']);

        $this->assertNotNull($personnel->fresh()->deleted_at);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.delete
     */
    public function test_only_super_admin_can_delete_personnels()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $personnel = Personnel::factory()->create();

        $this->deleteJson("/api/v1/admin/personnels/$personnel->id")
            ->assertForbidden();
    }
}
