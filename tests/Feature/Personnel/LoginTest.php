<?php

namespace Tests\Feature\Personnel;

use App\Models\Personnel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_personnel_can_login_using_id_and_birthdate()
    {
        $personnel = Personnel::factory()->create();

        $this->postJson('/api/v1/personnel/login', $personnel->toArray())
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token'
            ]);
    }

    public function test_personnel_can_not_login_with_invalid_credentials()
    {
        $data = [
            'personnel_id' => $this->faker->bothify('##-???????'),
            'birth_date' => $this->faker->date(),
        ];

        $this->postJson('/api/v1/personnel/login', $data)
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_personnel_can_not_login_with_empty_credentials()
    {
        $this->postJson('/api/v1/personnel/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['personnel_id', 'birth_date']);
    }
}
