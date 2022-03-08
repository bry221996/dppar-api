<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Unit\CreateRequest;
use App\Http\Requests\Admin\Unit\UpdateRequest;
use App\Models\Unit;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $list = QueryBuilder::for(Unit::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search')
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response($list);
    }

    public function store(CreateRequest $request)
    {
        $unit = Unit::create($request->validated());

        return response([
            'message' => 'Successfully create unit.',
            'data' => $unit
        ]);
    }

    public function show(Unit $unit)
    {
        return response([
            'message' => 'Successfully fetched unit.',
            'data' => $unit
        ]);
    }

    public function update(UpdateRequest $request, Unit $unit)
    {
        $unit->update($request->validated());

        return response([
            'message' => 'Successfully updated unit.',
            'data' => $unit
        ]);
    }
}
