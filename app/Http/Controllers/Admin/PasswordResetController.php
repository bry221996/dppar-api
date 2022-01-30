<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PasswordResetRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    protected $passwordResetRepository;

    public function __construct(PasswordResetRepository $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email'
        ]);

        $this->passwordResetRepository->create([
            'email' => $request->email,
            'token' => Str::random(),
            'expires_at' => now()->addHour()
        ]);

        return response([
            'message' => 'Reset password link sent.'
        ]);
    }
}
