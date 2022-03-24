<?php

namespace App\Filters\Checkin;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class CheckinSubUnitFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            $subQuery->whereHas('personnel', function ($personnelQuery) use ($value) {
                $personnelQuery->whereHas('assignment', function ($assignmentQuery) use ($value) {
                    $assignmentQuery->where('sub_unit_id', $value);
                });
            });
        });
    }
}
