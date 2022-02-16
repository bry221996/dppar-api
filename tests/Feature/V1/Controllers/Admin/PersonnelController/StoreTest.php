<?php

namespace Tests\Feature\V1\Controllers\Admin\PersonnelController;

use App\Models\Assignment;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.store
     */
    public function test_super_admin_can_create_personnel()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = Personnel::factory()->make()->toArray();
        $data['assignment'] = Assignment::factory()->make()->toArray();

        $this->postJson('/api/v1/admin/personnels', $data)
            ->assertSuccessful()
            ->dump();
        // ->assertJsonFragment(['personnel_id' => $data['personnel_id']]);
        // ->assertJsonStructure([
        //     'message',
        //     'data' => ['unit_id', 'name', 'province', 'type', 'latitude', 'longitude']
        // ]);
    }

    // /**
    //  * @group controllers
    //  * @group controllers.admin
    //  * @group controllers.admin.personnel
    //  * @group controllers.admin.personnel.store
    //  */
    // public function test_super_admin_can_not_create_sub_units_with_empty_data()
    // {
    //     $superAdmin = User::factory()->superAdmin()->create();
    //     Sanctum::actingAs($superAdmin, [], 'admins');

    //     $this->postJson('/api/v1/admin/personnels', [])
    //         ->assertStatus(422)
    //         ->assertJsonValidationErrors(['unit_id', 'name', 'province', 'type', 'latitude', 'longitude']);
    // }


    // /**
    //  * @group controllers
    //  * @group controllers.admin
    //  * @group controllers.admin.personnel
    //  * @group controllers.admin.personnel.store
    //  */
    // public function test_only_super_admin_can_create_sub_units()
    // {
    //     $rpo = User::factory()->regionalPoliceOfficer()->create();

    //     $ppo = User::factory()->provincialPoliceOfficer()->create();

    //     $mpo = User::factory()->municipalPoliceOfficer()->create();

    //     Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

    //     $this->postJson('/api/v1/admin/sub-units', [])
    //         ->assertForbidden();
    // }
}
