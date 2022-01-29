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
            'type' => $this->faker->randomElement(['regular_checkin', 'leave_of_absence', 'off_duty']),
            'is_accounted' => $this->faker->boolean,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'remarks' => $this->faker->paragraph,
            'admin_remarks' => $this->faker->paragraph
        ];
    }

    public function outOfAreaOfResponsibility()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'out_of_area_of_responsibility',
                'aor_type' => $this->faker->randomElement(['hospital', 'travel', 'under_instruction', 'official_mission', 'conference', 'others']),
            ];
        });
    }
}