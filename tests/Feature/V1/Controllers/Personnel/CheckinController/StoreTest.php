<?php

namespace Tests\Feature\V1\Controllers\Personnel\CheckinController;

use App\Models\Checkin;
use App\Models\Personnel;
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
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.store
     */
    public function test_personnel_can_create_checkin()
    {
        Storage::fake('s3');

        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        $data = Checkin::factory()
            ->make([
                'personnel_id' => $personnel->id,
                'image' => UploadedFile::fake()->image('checkin-selfie.jpeg')
            ])
            ->toArray();


        $this->postJson('/api/v1/personnel/checkins', $data)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'image',
                    'type',
                    'latitude',
                    'longitude',
                    'remarks',
                    'personnel_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }


    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.store
     */
    public function test_personnel_can_create_checkin_without_selfie()
    {
        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        $data = Checkin::factory()->leave()->make()->toArray();

        $this->postJson('/api/v1/personnel/checkins', $data)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'image',
                    'type',
                    'latitude',
                    'longitude',
                    'remarks',
                    'personnel_id',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.store
     */
    public function test_guess_can_not_create_checkin()
    {
        $this->postJson('/api/v1/personnel/checkins', [])
            ->assertStatus(401);
    }
}
