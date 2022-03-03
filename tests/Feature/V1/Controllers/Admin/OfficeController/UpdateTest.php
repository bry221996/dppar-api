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

class UpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.update
     */
    public function test_super_admin_can_create_office()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $type = Str::camel(OfficeType::getRandomValue());
        $classification = Str::camel(OfficeClassification::getRandomValue());

        $office = Office::factory()->create();

        $data = Office::factory()->$type()->$classification()->make()->toArray();

        $this->putJson("/api/v1/admin/offices/$office->id", $data)
            ->assertSuccessful();
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.update
     */
    public function test_only_super_admin_can_update_office()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $office = Office::factory()->create();

        $this->putJson("/api/v1/admin/offices/$office->id", [])
            ->assertForbidden();
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.office
     * @group controllers.admin.office.update
     */
    public function test_super_admin_can_not_update_office_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $office = Office::factory()->create();

        $this->putJson("/api/v1/admin/offices/$office->id", [])
            ->assertStatus(422);
    }
}
