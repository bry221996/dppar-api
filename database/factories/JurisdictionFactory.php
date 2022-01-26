<?php

namespace Database\Factories;

use App\Models\MunicipalPoliceStation;
use Illuminate\Database\Eloquent\Factories\Factory;

class JurisdictionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'code' => $this->faker->bothify('### ???#?#?#?'),
            'municipal_police_station_id' => MunicipalPoliceStation::factory()->create()->id,
            'radius' => $this->faker->numberBetween(5, 10)
        ];
    }
}
