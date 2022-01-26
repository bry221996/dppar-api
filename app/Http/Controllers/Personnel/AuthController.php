<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personnel\LoginRequest;
use App\Services\PersonnelService;

class AuthController extends Controller
{
    protected $service;

    public function __construct(PersonnelService $personnelService)
    {
        $this->service = $personnelService;
    }

    public function login(LoginRequest $request)
    {
        $result = $this->service->login($request->personnel_id, $request->birth_date);

        return response($result->data, $result->code);
    }
}
