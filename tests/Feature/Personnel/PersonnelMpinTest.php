<?php

namespace Tests\Feature\Personnel;

use App\Models\Personnel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PersonnelMpinTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_personnel_can_update_mpin()
    {
        Sanctum::actingAs(Personnel::factory()->create(), [], 'personnels');
        $mpin = $this->faker()->numerify('####');

        $data = [
            'mpin' => $mpin,
            'mpin_confirmation' => $mpin
        ];

        $this->postJson('/api/v1/personnel/mpin', $data)
            ->assertStatus(200);
    }

    public function test_guess_can_not_update_mpin()
    {
        $this->postJson('/api/v1/personnel/mpin')
            ->assertStatus(401);
    }

    public function test_personnel_can_not_update_mpin_with_empty_data()
    {
        Sanctum::actingAs(Personnel::factory()->create(), [], 'personnels');

        $this->postJson('/api/v1/personnel/mpin')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['mpin']);
    }

    public function test_personnel_can_not_update_mpin_with_not_confirmed_mpin()
    {
        Sanctum::actingAs(Personnel::factory()->create(), [], 'personnels');

        $data = [
            'mpin' => '1234',
            'mpin_confirmation' => '4321'
        ];

        $this->postJson('/api/v1/personnel/mpin', $data)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['mpin']);
    }
}
