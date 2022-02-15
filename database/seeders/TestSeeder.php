<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Personnel;
use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create(['email' => 'super@admin.com']);

        Unit::factory()->count(2)
            ->create()
            ->each(function ($unit) {
                User::factory()
                    ->regionalPoliceOfficer()
                    ->create(['unit_id' => $unit->id]);

                // Create Sub Units.
                SubUnit::factory()
                    ->count(2)
                    ->create(['unit_id' => $unit->id])
                    ->each(function ($subUnit) use ($unit) {
                        User::factory()
                            ->provincialPoliceOfficer()
                            ->create([
                                'unit_id' => $unit->id,
                                'sub_unit_id' => $subUnit->id
                            ]);

                        // Create Stations
                        Station::factory()
                            ->count(2)
                            ->create(['sub_unit_id' => $subUnit->id])
                            ->each(function ($station) use ($unit, $subUnit) {
                                User::factory()
                                    ->municipalPoliceOfficer()
                                    ->create([
                                        'unit_id' => $unit->id,
                                        'sub_unit_id' => $subUnit->id,
                                        'station_id' => $station->id
                                    ]);

                                Personnel::factory()->count(2)->create()
                                    ->each(function ($personnel) use ($station, $unit, $subUnit) {
                                        Assignment::factory()->create([
                                            'personnel_id' => $personnel->id,
                                            'unit_id' => $unit->id,
                                            'sub_unit_id' => $subUnit->id,
                                            'station_id' => $station->id,
                                        ]);
                                    });
                            });
                    });
            });
    }
}
