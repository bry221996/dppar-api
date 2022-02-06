<?php

namespace App\Http\Controllers\Admin;

use App\Filters\CheckinFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListPersonnelRequest;
use App\Repositories\CheckinRepository;

class CheckinController extends Controller
{
    protected $checkinRepository;

    public function __construct(CheckinRepository $checkinRepository)
    {
        $this->checkinRepository = $checkinRepository;
    }

    public function index(ListPersonnelRequest $request, CheckinFilter $checkinFilter)
    {
        $list = $this->checkinRepository->listWithFilters($checkinFilter, $request->per_page ?? 10);

        return response($list);
    }
}
