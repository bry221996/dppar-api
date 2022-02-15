<?php

namespace Tests\Feature\Admin;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group admin
     * @group unit
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
     * @group admin
     * @group unit
     */
    public function test_only_super_admin_can_list_units()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->getJson('/api/v1/admin/units')
            ->assertStatus(403);
    }

    /**
     * @group admin
     * @group unit
     */
    public function test_super_admin_can_create_unit()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->make();

        $this->postJson('/api/v1/admin/units', $unit->toArray())
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'region', 'latitude', 'longitude', 'updated_at', 'created_at']
            ]);
    }

    /**
     * @group admin
     * @group unit
     */
    public function test_only_super_admin_can_create_unit()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->postJson('/api/v1/admin/units', [])
            ->assertStatus(403);
    }

    /**
     * @group admin
     * @group unit
     */
    public function test_super_admin_cannot_create_unit_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $this->postJson('/api/v1/admin/units', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'region', 'latitude', 'longitude']);
    }

    /**
     * @group admin
     * @group unit
     */
    public function test_super_admin_can_update_unit()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();
        $data = Unit::factory()->make();

        $this->putJson("/api/v1/admin/units/$unit->id", $data->toArray())
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'region', 'latitude', 'longitude', 'updated_at', 'created_at']
            ]);

        $this->assertDatabaseHas('units', [
            'name' => $data->name,
            'region' => $data->region,
        ]);
    }

    /**
     * @group admin
     * @group unit
     */
    public function test_super_admin_can_not_update_unit_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();

        $this->putJson("/api/v1/admin/units/$unit->id", [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'region', 'latitude', 'longitude']);
    }

    /**
     * @group admin
     * @group unit
     */
    public function test_super_admin_can_delete_unit()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create();

        $this->deleteJson("/api/v1/admin/units/$unit->id")
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'region', 'latitude', 'longitude', 'updated_at', 'created_at']
            ]);

        $this->assertNotNull($unit->fresh()->deleted_at);
    }

    /**
     * @group admin
     * @group unit
     */
    public function test_super_admin_can_restore_unit()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $unit = Unit::factory()->create(['deleted_at' => now()]);

        $this->postJson("/api/v1/admin/units/$unit->id/restore")
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'region', 'latitude', 'longitude', 'updated_at', 'created_at']
            ]);

        $this->assertNull($unit->fresh()->deleted_at);
    }
}
