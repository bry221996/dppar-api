<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Repositories\CheckinRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckinController extends Controller
{
    protected $checkinRepository;

    public function __construct(CheckinRepository $checkinRepository)
    {
        $this->checkinRepository = $checkinRepository;
    }

    public function index(Request $request)
    {
        $personnel = Auth::guard('personnels')->user();

        $list = $this->checkinRepository->listByPersonnelId($personnel->id, $request->per_page);

        return response($list);
    }
}
