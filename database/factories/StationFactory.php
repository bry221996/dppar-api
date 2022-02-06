<?php

namespace Database\Factories;

use App\Models\SubUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class StationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $municipality = $this->faker->municipality;

        return [
            'sub_unit_id' => SubUnit::factory()->create()->id,
            'name' => $municipality . ' Municipal Police Station',
            'municipality' => $municipality,
            'latitude' => $this->faker->latitude(12, 15),
            'longitude' => $this->faker->longitude(120, 122),
        ];
    }
}