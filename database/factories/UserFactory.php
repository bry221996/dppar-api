<?php

namespace Database\Factories;

use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'role' => $this->faker->randomElement(['super_admin'])
        ];
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
            ];
        });
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function superAdmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'super_admin',
            ];
        });
    }

    public function regionalPoliceOfficer()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'regional_police_officer',
                'unit_id' => function() {
                    return Unit::factory()->create()->id;
                }
            ];
        });
    }

    public function provincialPoliceOfficer()
    {
        return $this->state(function (array $attributes) {

            return [
                'role' => 'provincial_police_officer',
                'unit_id' => function () {
                    return Unit::factory()->create()->id;
                },
                'sub_unit_id' => function () {
                    return SubUnit::factory()->create()->id;
                }
            ];
        });
    }

    public function municipalPoliceOfficer()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'municipal_police_officer',
                'unit_id' => function () {
                    return Unit::factory()->create()->id;
                },
                'sub_unit_id' => function () {
                    return SubUnit::factory()->create()->id;
                },
                'station_id' => function () {
                    return Station::factory()->create()->id;
                }
            ];
        });
    }
}
