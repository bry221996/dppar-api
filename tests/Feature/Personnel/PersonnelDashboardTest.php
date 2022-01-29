<?php

namespace Tests\Feature\Personnel;

use App\Models\Checkin;
use App\Models\Personnel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PersonnelDashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group personnel */
    public function test_personnel_can_fetch_dashboard()
    {
        $personnel = Personnel::factory()->create();
        Sanctum::actingAs($personnel, [], 'personnels');

        Checkin::factory()->count(4)->create(['personnel_id' => $personnel->id]);

        $this->getJson('/api/v1/personnel/dashboard')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'personnel',
                'latest_checkins'
            ]);
    }

    /** @group personnel */
    public function test_guess_can_not_fetch_dashboard()
    {
        $this->getJson('/api/v1/personnel/dashboard')
            ->assertStatus(401);
    }
}
