<?php

namespace Tests\Feature\V1\Controllers\Personnel\CheckinController;

use App\Models\Checkin;
use App\Models\Personnel;
use Carbon\Carbon;
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
        Storage::fake('do_spaces');

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
            ])
            ->assertJsonFragment(['from_offline_sync' => false]);
    }

    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.store
     */
    public function test_personnel_can_create_multiple_present_checkins()
    {
        Storage::fake('do_spaces');

        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        Checkin::factory()
            ->present()
            ->create([
                'personnel_id' => $personnel->id,
            ]);

        $data = Checkin::factory()
            ->present()
            ->make([
                'personnel_id' => $personnel->id,
                'image' => UploadedFile::fake()->image('checkin-selfie.jpeg')
            ])
            ->toArray();


        $this->postJson('/api/v1/personnel/checkins', $data)
            ->assertSuccessful();
    }

    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.store
     */
    public function test_personnel_can_create_checkin_from_offline_sync()
    {
        Storage::fake('do_spaces');

        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        $data = Checkin::factory()
            ->make([
                'personnel_id' => $personnel->id,
                'image' => UploadedFile::fake()->image('checkin-selfie.jpeg')
            ])
            ->toArray();

        $data['created_at'] = Carbon::now()->subDay()->toDateTimeString();

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
            ])
            ->assertJsonFragment(['created_at' => $data['created_at']])
            ->assertJsonFragment(['from_offline_sync' => true]);
    }

    /**
     * @group controllers
     * @group controllers.personnel
     * @group controllers.personnel.checkin
     * @group controllers.personnel.checkin.store
     * @dataProvider invalidCheckinTypeProvider
     */
    public function test_personnel_cannot_create_multiple_checkin_with_different_type($existingCheckinType, $newCheckinType)
    {
        Storage::fake('do_spaces');

        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        Checkin::factory()
            ->$existingCheckinType()
            ->create([
                'personnel_id' => $personnel->id,
            ]);

        $data = Checkin::factory()
            ->$newCheckinType()
            ->make([
                'personnel_id' => $personnel->id,
                'image' => UploadedFile::fake()->image('checkin-selfie.jpeg')
            ])
            ->toArray();

        $this->postJson('/api/v1/personnel/checkins', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function invalidCheckinTypeProvider()
    {
        return [
            ['present', 'leave'],
            ['present', 'offDuty'],
            ['leave', 'present'],
            ['offDuty', 'present'],
        ];
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
