<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonnelDashboardController extends Controller
{
    protected $checkinRepository;

    public function index(Request $request)
    {
        $personnel = Auth::guard('personnels')->user();

        $latest_checkins = Checkin::where('personnel_id', $personnel->id)
            ->take($request->latest_checkins_count ?? 3)
            ->orderBy('created_at', 'desc')
            ->get();

        return response([
            'message' => 'Personnel Dashboard',
            'personnel' => $personnel,
            'latest_checkins' => $latest_checkins
        ]);
    }
}
