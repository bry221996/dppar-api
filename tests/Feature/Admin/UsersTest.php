<?php

namespace Tests\Feature\Admin;

use App\Models\Personnel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group admin */
    public function test_super_admin_can_create_another_super_admin()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = User::factory()->make()->toArray();

        $this->postJson('/api/v1/admin/users', $data)
            ->assertStatus(200)
            ->assertJsonFragment(['role' => 'super_admin']);
    }

    /** @group admin */
    public function test_super_admin_can_create_regional_police_officer()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = User::factory()->regionalPoliceOfficer()->make()->toArray();

        $this->postJson('/api/v1/admin/users', $data)
            ->assertStatus(200)
            ->assertJsonFragment(['role' => 'regional_police_officer']);
    }

    /** @group admin */
    public function test_super_admin_can_create_provincial_police_officer()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = User::factory()->provincialPoliceOfficer()->make()->toArray();

        $this->postJson('/api/v1/admin/users', $data)
            ->assertStatus(200)
            ->assertJsonFragment(['role' => 'provincial_police_officer']);
    }

    /** @group admin */
    public function test_super_admin_can_create_municipal_police_officer()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = User::factory()->municipalPoliceOfficer()->make()->toArray();

        $this->postJson('/api/v1/admin/users', $data)
            ->assertStatus(200)
            ->assertJsonFragment(['role' => 'municipal_police_officer']);
    }

    /** @group admin */
    public function test_personnel_can_not_admin_user()
    {
        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        $this->postJson('/api/v1/admin/users', [])
            ->assertStatus(401);
    }

    /** @group admin */
    public function test_only_super_admin_can_create_user()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->postJson('/api/v1/admin/users', [])
            ->assertStatus(403);
    }
}
