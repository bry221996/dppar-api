<?php

namespace Database\Factories;

use App\Models\Jurisdiction;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonnelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'personnel_id' => $this->faker->bothify('##-???????'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->lastName,
            'birth_date' => $this->faker->date(),
            'mobile_number' => $this->faker->numerify('09#########'),
            'email' => $this->faker->email,
            'mpin' => '$2y$10$ZuIK3W0OOO4auqoXeCddS.9wwkhHl/YymCNEQr/OgC.0bhMSbOtGW', // 1234,
            'type' => $this->faker->randomElement(['uniformed', 'non_uniformed', 'intel', 'special', 'department_heads']),
            'jurisdiction_id' => Jurisdiction::factory()->create()->id,
        ];
    }
}