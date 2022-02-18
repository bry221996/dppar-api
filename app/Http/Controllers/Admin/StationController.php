<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Station\CreateRequest;
use App\Http\Requests\Admin\Station\UpdateRequest;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index(Request $request)
    {
        $list = Station::withTrashed()
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
