<?php

namespace App\Repositories;

use App\Models\SubUnit;

class SubUnitRepository extends Repository
{
    public function __construct(SubUnit $subUnit)
    {
        parent::__construct($subUnit);
    }
}
