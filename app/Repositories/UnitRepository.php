<?php

namespace App\Repositories;

use App\Models\Unit;

class UnitRepository extends Repository
{
    public function __construct(Unit $unit)
    {
        parent::__construct($unit);
    }
}
