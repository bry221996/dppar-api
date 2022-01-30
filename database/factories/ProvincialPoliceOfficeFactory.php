<?php

namespace Database\Factories;

use App\Models\RegionalPoliceOffice;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProvincialPoliceOfficeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'regional_police_office_id' => RegionalPoliceOffice::factory()->create()->id,
            'name' => $this->faker->sentence,
            'province' => $this->faker->province,
            'type' => $this->faker->randomElement(['provincial', 'city']),
            'latitude' => $this->faker->latitude(12, 15),
            'longitude' => $this->faker->longitude(120, 122),
        ];
    }
}