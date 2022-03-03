<?php

namespace Database\Factories;

use App\Enums\OfficeClassification;
use App\Enums\OfficeType;
use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->bothify('#?#?#?#?#?#?#?#?#?#?#?'),
            'type' => OfficeType::REGIONAL,
            'classification' => OfficeClassification::getRandomValue(),
            'status' => 'active',
            'unit_id' => function () {
                return Unit::factory()->create()->id;
            },
        ];
    }

    public function regional()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => OfficeType::REGIONAL,
                'unit_id' =>  Unit::factory()->create()->id,
            ];
        });
    }

    public function provincial()
    {
        $unitId = Unit::factory()->create()->id;
        $subUnitId = SubUnit::factory()->create(['unit_id' => $unitId])->id;

        return $this->state(function (array $attributes) use ($unitId, $subUnitId) {
            return [
                'type' => OfficeType::PROVINCIAL,
                'unit_id' => $unitId,
                'sub_unit_id' => $subUnitId
            ];
        });
    }


    public function municipal()
    {
        $unitId = Unit::factory()->create()->id;
        $subUnitId = SubUnit::factory()->create(['unit_id' => $unitId])->id;
        $stationId = Station::factory()->create(['sub_unit_id' => $subUnitId])->id;

        return $this->state(function (array $attributes) use ($unitId, $subUnitId, $stationId) {
            return [
                'type' => OfficeType::MUNICIPAL,
                'unit_id' => $unitId,
                'sub_unit_id' => $subUnitId,
                'station_id' => $stationId
            ];
        });
    }

    public function regular()
    {
        return $this->state(function (array $attributes) {
            return [
                'classification' => OfficeClassification::REGULAR,
            ];
        });
    }

    public function special()
    {
        return $this->state(function (array $attributes) {
            return [
                'classification' => OfficeClassification::SPECIAL,
            ];
        });
    }

    public function mobileForce()
    {
        return $this->state(function (array $attributes) {
            return [
                'classification' => OfficeClassification::MOBILE_FORCE,
            ];
        });
    }
}
