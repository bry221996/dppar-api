<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
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

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make(Str::random(8));

        $user =  $this->userRepository->create($data);

        return response([
            'message' => 'User created successfully.',
            'data' => $user
        ]);
    }

    public function update(UserRequest $request,  $user_id)
    {
        $user = $this->userRepository->find($user_id);

        $this->userRepository->update($user, $request->validated());

        return response([
            'message' => 'User updated successfully.',
        ]);
    }

    public function destroy($user_id)
    {
        $user = $this->userRepository->findOrFail($user_id);

        $this->userRepository->update($user, ['status' => 'inactive']);

        return response([
            'message' => 'User updated successfully.',
        ]);
    }
}
