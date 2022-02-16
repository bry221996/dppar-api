<?php

namespace App\Filters\Checkin;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class CheckinUnitFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            $subQuery->whereHas('personnel', function ($personnelQuery) use ($value) {
                $personnelQuery->whereHas('assignments', function ($assignmentQuery) use ($value) {
                    $assignmentQuery->where('unit_id', $value);
                });
            });
        });
    }
}
