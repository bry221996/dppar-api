<?php

namespace Tests\Feature\Admin;

use App\Models\Personnel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PersonnelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_super_admin_can_get_personnel_list()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');
        $count = $this->faker()->numberBetween(1, 10);

        Personnel::factory()->count($count)->create();

        $this->getJson('/api/v1/admin/personnels')
            ->assertStatus(200)
            ->assertJsonFragment(['total' => $count]);
    }

    public function test_only_super_admin_can_get_personnel_list()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->getJson('/api/v1/admin/personnels')
            ->assertStatus(403);
    }
}
