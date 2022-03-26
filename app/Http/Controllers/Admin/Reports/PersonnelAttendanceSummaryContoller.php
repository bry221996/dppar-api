<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Enums\CheckInType;
use App\Filters\Personnel\PersonnelStationFilter;
use App\Filters\Personnel\PersonnelSubUnitFilter;
use App\Filters\Personnel\PersonnelUnitFilter;
use App\Http\Controllers\Controller;
use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PersonnelAttendanceSummaryContoller extends Controller
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
                'assignment' => function ($assignmentQuery) {
                    return $assignmentQuery->select(['personnel_id', 'unit_id', 'sub_unit_id', 'station_id', 'office_id'])
                        ->with(['unit:id,name', 'subUnit:id,name', 'station:id,name', 'office:id,name']);
                }
            ])
            ->withCount([
                'checkins as present_count' => function ($checkinQuery) use ($request) {
                    $checkinQuery
                        ->select(DB::raw('COUNT(DISTINCT(type))'))
                        ->whereDate('created_at', '<=', $request->get('end_date'))
                        ->whereDate('created_at', '>=', $request->get('start_date'))
                        ->where('type', CheckInType::PRESENT);
                },
                'checkins as leave_count' => function ($checkinQuery) use ($request) {
                    $checkinQuery
                        ->select(DB::raw('COUNT(DISTINCT(type))'))
                        ->whereDate('created_at', '<=', $request->get('end_date'))
                        ->whereDate('created_at', '>=', $request->get('start_date'))
                        ->where('type', CheckInType::LEAVE);
                },
                'checkins as off_duty_count' => function ($checkinQuery) use ($request) {
                    $checkinQuery
                        ->select(DB::raw('COUNT(DISTINCT(type))'))
                        ->whereDate('created_at', '<=', $request->get('end_date'))
                        ->whereDate('created_at', '>=', $request->get('start_date'))
                        ->where('type', CheckInType::OFF_DUTY);
                },
                'checkins as unaccounted_count' => function ($checkinQuery) use ($request) {
                    $checkinQuery
                        ->select(DB::raw('COUNT(DISTINCT(type))'))
                        ->whereDate('created_at', '<=', $request->get('end_date'))
                        ->whereDate('created_at', '>=', $request->get('start_date'))
                        ->where('type', CheckInType::UNACCOUNTED);
                },
                'checkins as absent_count' => function ($checkinQuery) use ($request) {
                    $checkinQuery
                        ->select(DB::raw('COUNT(DISTINCT(type))'))
                        ->whereDate('created_at', '<=', $request->get('end_date'))
                        ->whereDate('created_at', '>=', $request->get('start_date'))
                        ->where('type', CheckInType::ABSENT);
                },
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response($list);
    }
}
