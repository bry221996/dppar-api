<?php

namespace Tests\Feature\V1\Controllers\Admin\UnitController;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.index
     */
    public function test_super_admin_can_list_units()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $count = $this->faker()->numberBetween(1, 5);

        $units = Unit::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/units')
            ->assertStatus(200)
            ->assertJsonCount($count, 'data')
            ->assertJsonFragment(['region' => $units->random()->region]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.unit
     * @group controllers.admin.unit.index
     */
    public function test_only_super_admin_can_list_units()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->getJson('/api/v1/admin/units')
            ->assertForbidden();
    }
}
