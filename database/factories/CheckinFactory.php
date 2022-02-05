<?php

namespace Database\Factories;

use App\Models\Personnel;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'personnel_id' => Personnel::factory()->create()->id,
            'image' => $this->faker->imageUrl(),
            'type' => 'present',
            'sub_type' => $this->faker->randomElement(['duty', 'under_instruction', 'conference', 'schooling', 'travel', 'off_duty']),
            'is_accounted' => $this->faker->boolean,
            'latitude' => $this->faker->latitude(12, 15),
            'longitude' => $this->faker->longitude(120, 122),
            'remarks' => $this->faker->paragraph,
            'admin_remarks' => $this->faker->paragraph
        ];
    }

    public function absent()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'absent',
                'sub_type' => $this->faker->randomElement(['leave', 'confined_in_hospital', 'sick', 'suspended']),
            ];
        });
    }
}