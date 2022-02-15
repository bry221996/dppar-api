<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Unit\CreateRequest;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $list = Unit::paginate($request->per_page ?? 10);

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
}
