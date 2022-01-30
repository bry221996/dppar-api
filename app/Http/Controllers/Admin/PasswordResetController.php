<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    protected $passwordResetRepository;

    protected $userRepository;

    public function __construct(PasswordResetRepository $passwordResetRepository, UserRepository $userRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;

        $this->userRepository = $userRepository;
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email'
        ], [
            'email.exists' => 'Email not found.'
        ]);

        $passwordReset = $this->passwordResetRepository->create([
            'email' => $request->email,
            'token' => Str::random(),
            'expires_at' => now()->addHour()
        ]);

        $user = $this->userRepository->findByEmail($request->email);

        $user->notify(new PasswordResetNotification($passwordReset));

        return response([
            'message' => 'Reset password link sent.'
        ]);
    }
}
