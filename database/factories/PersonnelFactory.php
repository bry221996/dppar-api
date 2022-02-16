<?php

namespace Database\Factories;

use App\Enums\GenderType;
use App\Enums\PersonnelCategory;
use App\Enums\PersonnelClassification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class PersonnelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $birth_date = $this->faker->date();
        $defaultPin = Carbon::parse($birth_date);

        return [
            'title' => $this->faker->title,
            'qualifier' => $this->faker->randomElement(['Jr.', 'Sr.', 'III', 'IV', null]),
            'badge_no' => $this->faker->bothify('##-???????'),
            'personnel_id' => $this->faker->bothify('##-???????'),
            'designation' => $this->faker->jobTitle,
            'category' => $this->faker->randomElement(PersonnelCategory::getAll()),
            'classification' => $this->faker->randomElement(PersonnelClassification::getAll()),
            'gender' => $this->faker->randomElement(GenderType::getAll()),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'birth_date' => $birth_date,
            'mobile_number' => $this->faker->numerify('09#########'),
            'email' => $this->faker->email,
            'mpin' => Hash::make($defaultPin->format('Ymd')),
        ];
    }
}
