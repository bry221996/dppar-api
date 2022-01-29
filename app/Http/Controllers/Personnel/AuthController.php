<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personnel\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $loginRequest)
    {
        $token = $loginRequest->authenticate();

        return $token ?
            response(['message' => 'Successful Login.', 'token' => $token]) :
            response(['message' => 'Invalid Credentials'], 401);
    }

    public function logout()
    {
        $personnel = Auth::guard('personnels')->user();

        $token = $personnel->currentAccessToken();

        $token ? $token->delete() : $personnel->tokens()->delete();

        return response(['message' => 'Logout Successfully.']);
    }

    public function details()
    {
        return response([
            'message' => 'Successfully fetch personnel details',
            'data' => Auth::guard('personnels')->user()
        ]);
    }
}
