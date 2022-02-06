<?php

namespace App\Repositories;

use App\Models\Station;

class StationRepository extends Repository
{
    public function __construct(Station $station)
    {
        parent::__construct($station);
    }
}
