<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $loginRequest)
    {
        $token = $loginRequest->authenticate();

        return response(['message' => 'Login Successful', 'token' => $token]);
    }

    public function getMe()
    {
        $data = Auth::guard('admins')->user();

        $data->loadMissing(['offices:id,name', 'classifications:id,name']);

        return response([
            'message' => 'User profile',
            'data' => $data
        ]);
    }

    public function logout()
    {
        $admin = Auth::guard('admins')->user();

        $token = $admin->currentAccessToken();

        $token ? $token->delete() : $admin->tokens()->delete();

        return response(['message' => 'Logout Successfully.']);
    }
}
