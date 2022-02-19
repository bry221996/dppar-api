<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Station\CreateRequest;
use App\Http\Requests\Admin\Station\UpdateRequest;
use App\Models\Station;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StationController extends Controller
{
    public function index(Request $request)
    {
        $list = QueryBuilder::for(Station::class)
            ->allowedFilters([
                AllowedFilter::exact('sub_unit_id'),
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
                AllowedFilter::callback('unit_id', function (Builder $query, $value) {
                    $query->whereHas('subUnit', function ($subUnitQuery) use ($value) {
                        $subUnitQuery->where('unit_id', $value);
                    });
                }),
            ])
            ->paginate($request->per_page ?? 10);

        return response($list);
    }

    public function show(Station $station)
    {
        return response([
            'message' => 'Successfully fetched station.',
            'data' => $station
        ]);
    }

    public function store(CreateRequest $request)
    {
        $station = Station::create($request->validated());

        return response([
            'message' => 'Successfully create station.',
            'data' => $station
        ]);
    }

    public function update(UpdateRequest $request, Station $station)
    {
        $station->update($request->validated());

        return response([
            'message' => 'Successfully updated station.',
            'data' => $station
        ]);
    }

    public function destroy(Station $station)
    {
        $station->delete();

        return response([
            'message' => 'Successfully deleted station.',
            'data' => $station
        ]);
    }

    public function restore($id)
    {
        $station = Station::withTrashed()->findOrFail($id);
        $station->restore();

        return response([
            'message' => 'Successfully restored station.',
            'data' => $station
        ]);
    }
}
