<?php

namespace App\Http\Controllers\Admin;

use App\Filters\PersonnelFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListPersonnelRequest;
use App\Models\Personnel;

class PersonnelController extends Controller
{
    public function index(ListPersonnelRequest $request, PersonnelFilter $personnelFilter)
    {
        $list = Personnel::filter($personnelFilter)
            ->paginate($request->per_page ?? 10);

        return response($list);
    }
}
