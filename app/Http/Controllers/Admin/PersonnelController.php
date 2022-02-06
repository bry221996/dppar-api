<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PersonnelRepository;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    protected $personnelRepository;

    public function __construct(PersonnelRepository $personnelRepository)
    {
        $this->personnelRepository = $personnelRepository;
    }

    public function index(Request $request)
    {
        $list = $this->personnelRepository->list($request->per_page ?? 10);

        return response($list);
    }
}
