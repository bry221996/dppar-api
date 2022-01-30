<?php

namespace Database\Factories;

use App\Models\ProvincialPoliceOffice;
use Illuminate\Database\Eloquent\Factories\Factory;

class MunicipalPoliceStationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'provincial_police_office_id' => ProvincialPoliceOffice::factory()->create()->id,
            'name' => $this->faker->sentence,
            'municipality' => $this->faker->municipality,
            'latitude' => $this->faker->latitude(12, 15),
            'longitude' => $this->faker->longitude(120, 122),
        ];
    }
}