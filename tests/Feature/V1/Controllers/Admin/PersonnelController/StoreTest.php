<?php

namespace Tests\Feature\V1\Controllers\Admin\PersonnelController;

use App\Models\Assignment;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        Storage::fake('do_spaces');

        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $data = Personnel::factory()->make()->toArray();
        $data['image'] = UploadedFile::fake()->image('personnel.jpeg');
        $data['assignment'] = Assignment::factory()->make()->toArray();

        $this->postJson('/api/v1/admin/personnels', $data)
            ->assertSuccessful()
            ->assertJsonFragment(['personnel_id' => $data['personnel_id']])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'title',
                    'qualifier',
                    'badge_no',
                    'personnel_id',
                    'designation',
                    'category',
                    'classification_id',
                    'gender',
                    'first_name',
                    'last_name',
                    'middle_name',
                    'birth_date',
                    'mobile_number',
                    'email',
                    'image'
                ]
            ]);
    }

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.store
     */
    public function test_super_admin_can_not_create_personnels_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $this->postJson('/api/v1/admin/personnels', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'badge_no',
                'personnel_id',
                'designation',
                'category',
                'classification_id',
                'gender',
                'first_name',
                'last_name',
                'middle_name',
                'birth_date',
                'mobile_number',
                'email'
            ]);
    }


    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.store
     */
    public function test_only_super_admin_can_create_personnels()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $this->postJson('/api/v1/admin/personnels', [])
            ->assertForbidden();
    }
}
