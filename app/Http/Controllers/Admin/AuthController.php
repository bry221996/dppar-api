<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $loginRequest)
    {
        $token = $loginRequest->authenticate();

        return response(['message' => 'Login Successful', 'token' => $token]);
    }
}
