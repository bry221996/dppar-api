<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $list = User::paginate($request->per_page ?? 10);

        return response($list);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make(Str::random(8));

        $user =  User::create($data);

        return response([
            'message' => 'User created successfully.',
            'data' => $user
        ]);
    }

    public function show(User $user)
    {
        return response([
            'message' => 'User fetched successfully.',
            'data' => $user
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validated());

        return response([
            'message' => 'User updated successfully.',
            'data' => $user
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response([
            'message' => 'User updated successfully.',
            'data' => $user
        ]);
    }
}
