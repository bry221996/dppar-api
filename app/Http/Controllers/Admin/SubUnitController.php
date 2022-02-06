<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\SubUnitRepository;
use Illuminate\Http\Request;

class SubUnitController extends Controller
{
    protected $subUnitRepository;

    public function __construct(SubUnitRepository $subUnitRepository)
    {
        $this->subUnitRepository = $subUnitRepository;
    }

    public function index(Request $request)
    {
        $list = $this->subUnitRepository->list($request->per_page ?? 10);

        return response($list);
    }
}
