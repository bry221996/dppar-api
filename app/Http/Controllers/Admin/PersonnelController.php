<?php

namespace App\Http\Controllers\Admin;

use App\Filters\PersonnelFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListPersonnelRequest;
use App\Repositories\PersonnelRepository;

class PersonnelController extends Controller
{
    protected $personnelRepository;

    public function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }

    public function index(ListPersonnelRequest $request, PersonnelFilter $personnelFilter)
    {
        $list = $this->personnelRepository->listWithFilters($personnelFilter, $request->per_page ?? 10);

        return response($list);
    }
}
