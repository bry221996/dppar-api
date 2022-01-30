<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PasswordResetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => User::factory()->create()->email,
            'token' => Str::random(24),
            'expires_at' => now()->addHour()
        ];
    }
}
 