<?php

namespace Tests\Feature\Personnel;

use App\Models\Personnel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PersonnelDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @group personnel */
    public function test_logged_in_personnel_can_fetch_details()
    {
        Sanctum::actingAs(Personnel::factory()->create(), [], 'personnels');

        $this->getJson('/api/v1/personnel/details')
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'personnel_id',
                    'first_name',
                    'last_name',
                    'middle_name',
                    'email',
                    'created_at',
                    'updated_at',
                    'id',
                    'has_pin',
                    'title',
                    'qualifier',
                    'badge_no',
                    'designation',
                    'category',
                    'classification_id',
                    'gender'
                ]
            ]);
    }

    /** @group personnel */
    public function test_guess_can_not_fetch_personnel_details()
    {
        $this->getJson('/api/v1/personnel/details')
            ->assertStatus(401);
    }
}
