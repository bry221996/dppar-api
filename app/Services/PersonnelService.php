<?php

namespace App\Services;

use App\Repositories\PersonnelRepository;

class PersonnelService
{
    protected $personnelRepository;

    public function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }

    public function login(string $personnel_id, string $birth_date): object
    {
        $personnel = $this->personnelRepository->getByPersonnelIdAndBirthDate($personnel_id, $birth_date);

        if (!$personnel) {
            return (object) [
                'code' => 401,
                'data' => [
                    'message' => 'Invalid Credentials'
                ]
            ];
        }

        return (object) [
            'code' => 200,
            'data' => [
                'message' => 'Successful Login.',
                'token' => $personnel->createToken('')->plainTextToken
            ]
        ];
    }
}
