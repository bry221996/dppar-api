<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
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

    public function reset(Request $request)
    {
        $request->validate([
            'personnel_id' => 'required',
            'birth_date' => 'required'
        ]);

        $personnel = Personnel::where('personnel_id', $request->personnel_id)
            ->first();

        if (!$personnel) {
            return response(['message' => 'Personnel not found.'], 400);
        }

        if (Carbon::parse($personnel->birth_date)->format('Ymd') !== $request->birth_date) {
            return response(['message' => 'Personnel not found.'], 400);
        }

        $personnel->update([
            'mpin' => Hash::make(Carbon::parse($personnel->birth_date)->format('Ymd')),
            'pin_updated_at' => null,
        ]);

        return response(['message' => 'MPIN successfully reset.']);
    }
}
