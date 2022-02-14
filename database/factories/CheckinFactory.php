<?php

namespace Database\Factories;

use App\Enums\CheckInType;
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
            'personnel_id' => function () {
                return Personnel::factory()->create()->id;
            },
            'image' => $this->faker->imageUrl(),
            'type' => CheckInType::PRESENT,
            'sub_type' => $this->faker->randomElement(CheckInType::getSubType(CheckInType::PRESENT)),
            'is_accounted' => $this->faker->boolean,
            'latitude' => $this->faker->latitude(12, 15),
            'longitude' => $this->faker->longitude(120, 122),
            'remarks' => $this->faker->sentence,
            'admin_remarks' => $this->faker->sentence
        ];
    }

    public function absent()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => CheckInType::ABSENT,
                'sub_type' => $this->faker->randomElement(CheckInType::getSubType(CheckInType::PRESENT)),
            ];
        });
    }

    public function offDuty()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => CheckInType::OFF_DUTY,
                'sub_type' => null
            ];
        });
    }
}
