<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Office\CreateRequest;
use App\Http\Requests\Admin\Office\UpdateRequest;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('admins')->user();
        $baseQuery = $user->is_super_admin ? Office::class : $user->offices();

        $list = QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('classification'),
                AllowedFilter::exact('unit_id'),
                AllowedFilter::exact('sub_unit_id'),
                AllowedFilter::exact('station_id'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->per_page ?? 10);

        return response($list);
    }

    public function store(CreateRequest $request)
    {
        $office = Office::create($request->validated());

        return response([
            'message' => 'Office successfully created.',
            'data' => $office
        ]);
    }

    public function show(Office $office)
    {
        return response([
            'message' => 'Office successfully fetched.',
            'data' => $office
        ]);
    }

    public function update(UpdateRequest $request, Office $office)
    {
        $office->update($request->validated());

        return response([
            'message' => 'Office successfully updated.',
            'data' => $office
        ]);
    }
}
