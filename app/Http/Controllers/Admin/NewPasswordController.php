<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewPasswordRequest;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class NewPasswordController extends Controller
{
    protected $userRepository;

    protected $passwordResetRepository;

    public function __construct(UserRepository $userRepository, PasswordResetRepository $passwordResetRepository)
    {
        $this->userRepository = $userRepository;

        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function store(NewPasswordRequest $request)
    {
        $user = $this->userRepository->findByEmail($request->email);

        $this->userRepository->update($user, ['password' => Hash::make($request->password)]);

        $this->passwordResetRepository->deleteByEmailAndToken($request->email, $request->token);

        return response([
            'message' => 'Password successfully updated.'
        ]);
    }
}
