<?php

namespace Tests\Feature\V1\Controllers\Admin\OfficeController;

use App\Enums\OfficeClassification;
use App\Enums\OfficeType;
use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Str;

class StoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.store
     */
    public function test_super_admin_can_create_office()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $type = Str::camel(OfficeType::getRandomValue());
        $classification = Str::camel(OfficeClassification::getRandomValue());

        $data = Office::factory()->$type()->$classification()->make()->toArray();

        $this->postJson('/api/v1/admin/offices', $data)
            ->assertSuccessful();
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.store
     */
    public function test_only_super_admin_can_create_office()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->postJson('/api/v1/admin/offices', [])
            ->assertForbidden();
    }


    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.store
     */
    public function test_super_admin_can_not_create_office_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $this->postJson('/api/v1/admin/offices', [])
            ->assertStatus(422);
    }
}
