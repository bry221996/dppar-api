<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\UnitRepository;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected $unitRepository;

    public function __construct(UnitRepository $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    public function index(Request $request)
    {
        $list = $this->unitRepository->list($request->per_page ?? 10);

        return response($list);
    }
}
