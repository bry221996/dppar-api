<?php

namespace Database\Seeders;

use App\Models\Checkin;
use App\Models\Jurisdiction;
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
        Unit::factory()->count(2)
            ->create()
            ->each(function ($unit) {
                User::factory()
                    ->regionalPoliceOfficer()
                    ->count(3)
                    ->create(['unit_id' => $unit->id]);

                // Create Sub Units.
                SubUnit::factory()
                    ->count(2)
                    ->create(['unit_id' => $unit->id])
                    ->each(function ($subUnit) use ($unit) {
                        User::factory()
                            ->provincialPoliceOfficer()
                            ->count(5)
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
                                    ->provincialPoliceOfficer()
                                    ->count(2)
                                    ->create([
                                        'unit_id' => $unit->id,
                                        'sub_unit_id' => $subUnit->id,
                                        'station_id' => $station->id
                                    ]);

                                Jurisdiction::factory()
                                    ->count(2)
                                    ->create(['station_id' => $station->id])
                                    ->each(function ($jurisdiction) {
                                        Personnel::factory()->count(2)
                                            ->create(['jurisdiction_id' => $jurisdiction->id])
                                            ->each(function ($personnel) {
                                                Checkin::factory()
                                                    ->create(['personnel_id' => $personnel->id]);

                                                Checkin::factory()
                                                    ->absent()
                                                    ->create(['personnel_id' => $personnel->id]);
                                            });
                                    });
                            });
                    });
            });
    }
}
