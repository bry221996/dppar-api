<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PersonnelRepository;
use App\Repositories\UnitRepository;
use Illuminate\Http\Request;

class UnitPersonnelController extends Controller
{
    protected $unitRepository;

    protected $personnelRepository;

    public function __construct(UnitRepository $unitRepository,  PersonnelRepository $personnelRepository)
    {
        $this->unitRepository = $unitRepository;

        $this->personnelRepository = $personnelRepository;
    }

    public function index(Request $request)
    {
        $list = $this->unitRepository->list($request->per_page ?? 10);

        return response($list);
    }
}
