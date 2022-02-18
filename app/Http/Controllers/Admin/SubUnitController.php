<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubUnit\CreateRequest;
use App\Http\Requests\Admin\SubUnit\UpdateRequest;
use App\Models\SubUnit;
use Illuminate\Http\Request;

class SubUnitController extends Controller
{
    public function index(Request $request)
    {
        $list = SubUnit::withTrashed()
            ->paginate($request->per_page ?? 10);

        return response($list);
    }

    public function show(SubUnit $sub_unit)
    {
        return response([
            'message' => 'Successfully fetched sub unit.',
            'data' => $sub_unit
        ]);
    }

    public function store(CreateRequest $request)
    {
        $sub_unit = SubUnit::create($request->validated());

        return response([
            'message' => 'Successfully create sub unit.',
            'data' => $sub_unit
        ]);
    }

    public function update(UpdateRequest $request, SubUnit $sub_unit)
    {
        $sub_unit->update($request->validated());

        return response([
            'message' => 'Successfully updated sub unit.',
            'data' => $sub_unit
        ]);
    }

    public function destroy(SubUnit $sub_unit)
    {
        $sub_unit->delete();

        return response([
            'message' => 'Successfully deleted sub unit.',
            'data' => $sub_unit
        ]);
    }

    public function restore($id)
    {
        $sub_unit = SubUnit::withTrashed()->findOrFail($id);
        $sub_unit->restore();

        return response([
            'message' => 'Successfully restored sub unit.',
            'data' => $sub_unit
        ]);
    }
}
