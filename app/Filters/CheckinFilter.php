<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckinFilter extends Filters
{
    public function __construct(Request $request)
    {
        if (Auth::guard('admins')->check()) {
            $user = Auth::guard('admins')->user();
            if ($user->is_regional_police_officer) {
                $request->merge([
                    'unit_id' => $user->unit_id
                ]);
            }

            if ($user->is_provincial_police_officer) {
                $request->merge([
                    'unit_id' => $user->unit_id,
                    'sub_unit_id' => $user->sub_unit_id,
                ]);
            }

            if ($user->is_municipal_police_officer) {
                $request->merge([
                    'unit_id' => $user->unit_id,
                    'sub_unit_id' => $user->sub_unit_id,
                    'station_id' => $user->station_id,
                ]);
            }
        }
        parent::__construct($request);
    }

    public function unitId($unitId)
    {
        return $this->builder->whereHas('personnel', function ($personnelQuery) use ($unitId) {
            $personnelQuery->whereHas('jurisdiction', function ($jurisdictionQuery) use ($unitId) {
                $jurisdictionQuery
                    ->whereHas('station', function ($stationQuery) use ($unitId) {
                        $stationQuery
                            ->whereHas('subUnit', function ($subUnitQuery) use ($unitId) {
                                $subUnitQuery->where('unit_id', $unitId);
                            });
                    });
            });
        });
    }

    public function subUnitId($subUnitId)
    {
        return $this->builder->whereHas('personnel', function ($personnelQuery) use ($subUnitId) {
            $personnelQuery->whereHas('jurisdiction', function ($jurisdictionQuery) use ($subUnitId) {
                $jurisdictionQuery
                    ->whereHas('station', function ($stationQuery) use ($subUnitId) {
                        $stationQuery->where('sub_unit_id', $subUnitId);
                    });
            });
        });
    }

    public function stationId($stationId)
    {
        return $this->builder->whereHas('personnel', function ($personnelQuery) use ($stationId) {
            $personnelQuery->whereHas('jurisdiction', function ($jurisdictionQuery) use ($stationId) {
                $jurisdictionQuery->where('station_id', $stationId);
            });
        });
    }
}
