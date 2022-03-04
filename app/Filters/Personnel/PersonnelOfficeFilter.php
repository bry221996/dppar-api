<?php

namespace App\Filters\Personnel;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class PersonnelOfficeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        return $query->when($value, function ($subQuery) use ($value) {
            $subQuery->whereHas('assignments', function ($assignmentQuery) use ($value) {
                $assignmentQuery->where('office_id', $value);
            });
        });
    }
}
