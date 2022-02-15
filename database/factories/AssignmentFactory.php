<?php

namespace Database\Factories;

use App\Models\Personnel;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'personnel_id' => function () {
                return Personnel::factory()->create()->id;
            },
            'unit_id' => function () {
                return Unit::factory()->create()->id;
            },
            'sub_unit_id' => null,
            'station_id' => null
        ];
    }
}
