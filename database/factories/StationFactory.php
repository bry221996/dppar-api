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
            'sub_unit_id' => function () {
                return SubUnit::factory()->create()->id;
            },
            'name' => $municipality . ' Municipal Police Station',
            'municipality' => $municipality,
            'code' => $this->faker->swiftBicNumber,
            'status' => 'active',
        ];
    }
}
