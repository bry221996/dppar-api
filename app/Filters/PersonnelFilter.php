<?php

namespace App\Filters;

class PersonnelFilter extends Filters
{
    public function unitId($unitId)
    {
        return $this->builder->whereHas('jurisdiction', function ($jurisdictionQuery) use ($unitId) {
            $jurisdictionQuery
                ->whereHas('station', function ($stationQuery) use ($unitId) {
                    $stationQuery
                        ->whereHas('subUnit', function ($subUnitQuery) use ($unitId) {
                            $subUnitQuery->where('unit_id', $unitId);
                        });
                });
        });
    }

    public function subUnitId($subUnitId)
    {
        return $this->builder->whereHas('jurisdiction', function ($jurisdictionQuery) use ($subUnitId) {
            $jurisdictionQuery
                ->whereHas('station', function ($stationQuery) use ($subUnitId) {
                    $stationQuery->where('sub_unit_id', $subUnitId);
                });
        });
    }

    public function stationId($stationId)
    {
        return $this->builder->whereHas('jurisdiction', function ($jurisdictionQuery) use ($stationId) {
            $jurisdictionQuery->where('station_id', $stationId);
        });
    }
}