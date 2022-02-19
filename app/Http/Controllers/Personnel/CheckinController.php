<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Personnel\CheckinRequest;
use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckinController extends Controller
{
    public function index(Request $request)
    {
        $personnel = Auth::guard('personnels')->user();

        $list =  Checkin::where('personnel_id', $personnel->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page);

        return response($list);
    }

    public function store(CheckinRequest $request)
    {
        $checkin = Checkin::create($request->validated());

        return response([
            'message' => 'Successfully create checkin.',
            'data' => $checkin
        ]);
    }
}
