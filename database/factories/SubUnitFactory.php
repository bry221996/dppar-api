<?php

namespace Database\Factories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $province = $this->faker->province;

        return [
            'unit_id' => Unit::factory()->create()->id,
            'name' => $province . 'Provincial Police Office',
            'province' => $province,
            'type' => $this->faker->randomElement(['provincial', 'city']),
            'latitude' => $this->faker->latitude(12, 15),
            'longitude' => $this->faker->longitude(120, 122),
        ];
    }
}