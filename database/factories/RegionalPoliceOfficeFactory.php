<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RegionalPoliceOfficeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'region' => $this->faker->numerify('Region #'),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}