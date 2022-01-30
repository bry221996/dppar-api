<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personnel\CheckinRequest;
use App\Repositories\CheckinRepository;
use App\Services\Geocoder\OpenCage\OpenCageService;
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

    public function store(CheckinRequest $checkinRequest)
    {
        $data = $checkinRequest->validated();

        $address = (new OpenCageService())->reverse($data['latitude'], $data['longitude']);
        $data['town'] = $address->getTown();
        $data['province'] = $address->getProvince();

        $checkin = $this->checkinRepository->create($data);

        return response([
            'message' => 'Successfully create checkin.',
            'data' => $checkin
        ]);
    }
}
