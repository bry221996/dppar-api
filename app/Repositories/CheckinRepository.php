<?php

namespace App\Repositories;

use App\Models\Checkin;

class CheckinRepository extends Repository
{
    public function __construct(Checkin $checkin)
    {
        parent::__construct($checkin);
    }

    public function listByPersonnelId(int $personnel_id, $page_size = 10)
    {
        return $this->model->where('personnel_id', $personnel_id)
            ->paginate($page_size);
    }
}