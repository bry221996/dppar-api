<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\Users\CreateUserRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make(Str::random(8));

        $user =  $this->userRepository->create($data);

        return response([
            'message' => 'User created successfully.',
            'data' => $user
        ]);
    }
}
