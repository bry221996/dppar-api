<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $region = $this->faker->numerify('Region #####');

        return [
            'name' =>  $region . ' Regional Police Office',
            'region' => $region,
            'code' => $this->faker->swiftBicNumber,
            'status' => 'active',
            'deleted_at' => null
        ];
    }
}
