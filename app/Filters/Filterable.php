<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * Filter a result set.
     *
     * @param  Builder $query
     * @param  Filters $filters
     * @return Builder
     */
    public function scopeFilter($query, Filters $filters)
    {
        return $filters->apply($query);
    }
}
