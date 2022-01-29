<?php

namespace App\Repositories;

use App\Models\Personnel;

class PersonnelRepository extends Repository
{
    public function __construct(Personnel $personnel)
    {
        parent::__construct($personnel);
    }

    public function getByPersonnelIdAndBirthDate(string $personnel_id, string $birth_date)
    {
        return $this->model->where('personnel_id', $personnel_id)
            ->where('birth_date', $birth_date)
            ->first();
    }

    public function getByPersonnelId(string $personnel_id)
    {
        return $this->model->where('personnel_id', $personnel_id)
            ->first();
    }
}
