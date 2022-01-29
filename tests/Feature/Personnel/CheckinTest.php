<?php

namespace Tests\Feature\Personnel;

use App\Models\Checkin;
use App\Models\Personnel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckinTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_personnel_can_list_checkins()
    {
        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');
        Checkin::factory()->count(3)->create(['personnel_id' => $personnel->id]);

        $this->getJson('/api/v1/personnel/checkins')
            ->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    [
                        'id',
                        'personnel_id',
                        'image',
                        'type',
                        'aor_type',
                        'is_accounted',
                        'latitude',
                        'longitude',
                        'remarks',
                        'admin_remarks',
                        'created_at',
                        'updated_at' 
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
    }

    public function test_guest_can_not_list_checkins()
    {
        $this->getJson('/api/v1/personnel/checkins')
            ->assertStatus(401);
    }
}