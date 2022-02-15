<?php

namespace Database\Factories;

use App\Models\Jurisdiction;
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
            'personnel_id' => $this->faker->bothify('##-???????'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'birth_date' => $birth_date,
            'mobile_number' => $this->faker->numerify('09#########'),
            'email' => $this->faker->email,
            'mpin' => Hash::make($defaultPin->format('Ymd')),
            'type' => $this->faker->randomElement(['uniformed', 'non_uniformed', 'intel', 'special', 'department_heads']),
        ];
    }
}
