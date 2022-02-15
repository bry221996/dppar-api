<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Repositories\PersonnelRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PersonnelMpinController extends Controller
{
    protected $personnelRepository;

    public function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mpin' => 'required|confirmed|alpha_num|size:4'
        ]);

        $personnel = Auth::guard('personnels')->user();

        $this->personnelRepository->update($personnel, [
            'mpin' => Hash::make($data['mpin']),
            'pin_updated_at' => Carbon::now()->toDateTimeLocalString()
        ]);

        return response(['message' => 'MPIN successfully updated']);
    }
}
