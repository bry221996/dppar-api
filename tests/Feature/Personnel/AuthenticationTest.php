<?php

namespace Tests\Feature\Personnel;

use App\Models\Personnel;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group personnel */
    public function test_personnel_can_login_using_personnel_id_and_birthdate()
    {
        $personnel = Personnel::factory()->create();

        $data = [
            'personnel_id' => $personnel->personnel_id,
            'mpin' => Carbon::parse($personnel->birth_date)->format('Ymd'),
            'device' => $this->faker->macAddress,
        ];

        $this->postJson('/api/v1/personnel/login', $data)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token'
            ]);
    }

    /** @group personnel */
    public function test_personnel_can_login_using_personnel_id_and_mpin()
    {
        $pin = $this->faker()->numerify('####');

        $personnel = Personnel::factory()->create([
            'mpin' => Hash::make($pin)
        ]);

        $data = [
            'personnel_id' => $personnel->personnel_id,
            'mpin' => $pin,
            'device' => $this->faker->macAddress,
        ];

        $this->postJson('/api/v1/personnel/login', $data)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token'
            ]);
    }

    /** @group personnel */
    public function test_personnel_can_not_login_with_invalid_personnel_id_and_birth_date()
    {
        $data = [
            'personnel_id' => $this->faker->bothify('##-???????'),
            'birth_date' => $this->faker->date(),
            'device' => $this->faker->macAddress,
        ];

        $this->postJson('/api/v1/personnel/login', $data)
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    /** @group personnel */
    public function test_personnel_can_not_login_with_invalid_personnel_id_and_pin()
    {
        $personnel = Personnel::factory()->create();

        $data = [
            'personnel_id' => $personnel->personnel_id,
            'mpin' => '4321',
            'device' => $this->faker->macAddress,
        ];

        $this->postJson('/api/v1/personnel/login', $data)
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    /** @group personnel */
    public function test_personnel_can_not_login_with_empty_credentials()
    {
        $this->postJson('/api/v1/personnel/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['personnel_id', 'birth_date']);
    }

    public function test_personnel_can_logout()
    {
        Sanctum::actingAs(Personnel::factory()->create(), [], 'personnels');

        $this->postJson('/api/v1/personnel/logout')
            ->assertStatus(200);
    }
}
