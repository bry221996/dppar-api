<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personnel\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $personnel = $request->authenticate();

        if (!$personnel) {
            return response(['message' => 'Invalid Credentials'], 401);
        }

        return response([
            'message' => 'Successful Login.',
            'token' => $personnel->createToken('')->plainTextToken
        ]);
    }

    public function details()
    {
        return response([
            'message' => 'Successful get personnel details.',
            'data' => Auth::user()
        ]);
    }
}
