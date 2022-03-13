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

class UpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @group controllers
     * @group controllers.admin
     * @group controllers.admin.personnel
     * @group controllers.admin.personnel.update
     */
    public function test_super_admin_can_update_personnel_with_new_image()
    {
        Storage::fake('s3');

        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $personnel = Personnel::factory()->create();
        Assignment::factory()->create(['personnel_id' => $personnel->id]);

        $data = Personnel::factory()
            ->make([
                'new_image' =>  UploadedFile::fake()->image('personnel.jpeg')
            ])
            ->toArray();

        $data['assignment'] = Assignment::factory()->make()->toArray();

        $this->putJson("/api/v1/admin/personnels/$personnel->id", $data)
            ->assertSuccessful()
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
     * @group controllers.admin.personnel.update
     */
    public function test_super_admin_can_update_personnel_without_new_image()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $personnel = Personnel::factory()->create();
        Assignment::factory()->create(['personnel_id' => $personnel->id]);

        $data = Personnel::factory()
            ->make([
                'image' =>  $this->faker()->imageUrl
            ])
            ->toArray();

        $data['assignment'] = Assignment::factory()->make()->toArray();

        $this->putJson("/api/v1/admin/personnels/$personnel->id", $data)
            ->assertSuccessful()
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
     * @group controllers.admin.personnel.update
     */
    public function test_super_admin_can_not_update_personnels_with_empty_data()
    {
        $superAdmin = User::factory()->superAdmin()->create();
        Sanctum::actingAs($superAdmin, [], 'admins');

        $personnel = Personnel::factory()->create();

        $this->putJson("/api/v1/admin/personnels/$personnel->id", [])
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
     * @group controllers.admin.personnel.update
     */
    public function test_only_super_admin_can_update_personnels()
    {
        $rpo = User::factory()->regionalPoliceOfficer()->create();

        $ppo = User::factory()->provincialPoliceOfficer()->create();

        $mpo = User::factory()->municipalPoliceOfficer()->create();

        Sanctum::actingAs($this->faker->randomElement([$rpo, $ppo, $mpo]), [], 'admins');

        $personnel = Personnel::factory()->create();

        $this->putJson("/api/v1/admin/personnels/$personnel->id", [])
            ->assertForbidden();
    }
}
