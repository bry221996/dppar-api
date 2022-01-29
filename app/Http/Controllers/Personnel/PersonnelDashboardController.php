<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Repositories\CheckinRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonnelDashboardController extends Controller
{
    protected $checkinRepository;

    public function __construct(CheckinRepository $checkinRepository)
    {
        $this->checkinRepository = $checkinRepository;
    }

    public function index(Request $request)
    {
        $personnel = Auth::guard('personnels')->user();

        $latest_checkins = $this->checkinRepository
            ->getLatestByPersonnelId($personnel->id, $request->latest_checkins_count ?? 3);

        return response([
            'message' => 'MPIN successfully updated',
            'personnel' => $personnel,
            'latest_checkins' => $latest_checkins
        ]);
    }
}
