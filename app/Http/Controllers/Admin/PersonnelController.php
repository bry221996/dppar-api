<?php

namespace App\Http\Controllers\Admin;

use App\Filters\Personnel\PersonnelStationFilter;
use App\Filters\Personnel\PersonnelSubUnitFilter;
use App\Filters\Personnel\PersonnelUnitFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Personnel\CreateRequest;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('admins')->user();

        $list = QueryBuilder::for(Personnel::class)
            ->allowedFilters([
                AllowedFilter::custom('unit_id', new PersonnelUnitFilter)->default($user->unit_id),
                AllowedFilter::custom('sub_unit_id', new PersonnelSubUnitFilter)->default($user->sub_unit_id),
                AllowedFilter::custom('station_id', new PersonnelStationFilter)->default($user->station_id),
            ])
            ->paginate($request->per_page ?? 10);

        return response($list);
    }

    public function store(CreateRequest $request)
    {
        $personnel = Personnel::create($request->personnelData());

        $personnel->assignments()->create($request->assignmentData());

        return response([
            'message' => 'Personnel successfully created.',
            'data' => $personnel
        ]);
    }
}
