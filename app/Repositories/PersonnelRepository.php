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

    public function listByUnitId($unit_id, $per_page = 10)
    {
        return $this->model
            ->whereHas('jurisdiction', function ($jurisdictionQuery) use ($unit_id) {
                $jurisdictionQuery
                    ->whereHas('station', function ($stationQuery) use ($unit_id) {
                        $stationQuery
                            ->whereHas('subUnit', function ($subUnitQuery) use ($unit_id) {
                                $subUnitQuery->where('unit_id', $unit_id);
                            });
                    });
            })
            ->paginate($per_page);
    }

    public function listBySubUnitId($sub_unit_id, $per_page = 10)
    {
        return $this->model
            ->whereHas('jurisdiction', function ($jurisdictionQuery) use ($sub_unit_id) {
                $jurisdictionQuery
                    ->whereHas('station', function ($stationQuery) use ($sub_unit_id) {
                        $stationQuery->where('sub_unit_id', $sub_unit_id);
                    });
            })
            ->paginate($per_page);
    }
}
