<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Personnel;
use App\Models\Station;
use App\Models\SubUnit;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        $station = Station::first();
        $assignmentData = [
            'unit_id' => $station->subUnit->unit_id,
            'sub_unit_id' => $station->sub_unit_id,
            'station_id' => $station->id
        ];

        collect(['1996-12-22', '1998-05-23', '1988-08-30', '2000-01-01', '2000-01-02', '2000-01-03', '2000-01-04'])
            ->each(function ($dev) use ($assignmentData) {
                $personnel_id = Carbon::parse($dev)->format('Ymd');
                $personnel = Personnel::create([
                    'personnel_id' => $personnel_id,
                    'birth_date' => $dev,
                    'mpin' => Hash::make($personnel_id)
                ]);
                $personnel->assignments()->create($assignmentData);
            });
    }
}
