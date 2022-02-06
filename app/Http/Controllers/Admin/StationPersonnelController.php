<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PersonnelRepository;
use Illuminate\Http\Request;

class StationPersonnelController extends Controller
{
    protected $personnelRepository;

    public function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }

    public function index(Request $request, $station_id)
    {
        $list = $this->personnelRepository->listByStationId($station_id, $request->per_page ?? 10);

        return response($list);
    }
}
