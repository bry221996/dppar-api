<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Filters\Personnel\PersonnelStationFilter;
use App\Filters\Personnel\PersonnelSubUnitFilter;
use App\Filters\Personnel\PersonnelUnitFilter;
use App\Http\Controllers\Controller;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PersonnelAttendanceContoller extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|date_format:Y-m-d|before_or_equal:now',
            'end_date' => 'required|date|date_format:Y-m-d|before_or_equal:now',
        ]);

        $user = Auth::guard('admins')->user();

        $userOffices = $user->offices
            ->map(function ($office) {
                return $office->id;
            })
            ->toArray();

        $userAccessibleClassifications = $user->classifications
            ->map(function ($classification) {
                return $classification->id;
            });

        $list = QueryBuilder::for(Personnel::class)
            ->select(['id', 'designation', 'first_name', 'last_name', 'middle_name', 'qualifier'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
                AllowedFilter::custom('unit_id', new PersonnelUnitFilter)->default($user->unit_id),
                AllowedFilter::custom('sub_unit_id', new PersonnelSubUnitFilter)->default($user->sub_unit_id),
                AllowedFilter::custom('station_id', new PersonnelStationFilter)->default($user->station_id),
                AllowedFilter::custom('office_id', new PersonnelStationFilter)->default(implode(',', $userOffices))
            ])
            ->when($userAccessibleClassifications->count(), function ($query) use ($userAccessibleClassifications) {
                return $query->whereIn('classification_id', $userAccessibleClassifications->toArray());
            })
            ->when(!$user->is_super_admin && !$user->is_intel, function ($query) {
                return $query->where('is_intel', false);
            })
            ->with([
                'checkins' => function ($checkinSubQuery) use ($request) {
                    return $checkinSubQuery
                        ->select(['id', 'personnel_id', 'type', 'created_at'])
                        ->whereDate('created_at', '<=', $request->get('end_date'))
                        ->whereDate('created_at', '>=', $request->get('start_date'))
                        ->orderBy('created_at', 'asc');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response($list);
    }
}
