<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\StationRepository;
use Illuminate\Http\Request;

class StationController extends Controller
{
    protected $stationRepository;

    public function __construct(StationRepository $stationRepository)
    {
        $this->stationRepository = $stationRepository;
    }

    public function index(Request $request)
    {
        $list = $this->stationRepository->list($request->per_page ?? 10);

        return response($list);
    }
}
