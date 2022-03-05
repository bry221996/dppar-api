<?php

namespace App\Http\Controllers\Admin;

use App\Filters\Checkin\CheckinOfficeFilter;
use App\Http\Controllers\Controller;

use App\Models\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

use App\Filters\Checkin\CheckinUnitFilter;
use App\Filters\Checkin\CheckinSubUnitFilter;
use App\Filters\Checkin\CheckinStationFilter;

class CheckinController extends Controller
{
    public function index(Request $request)
    {
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

        $list = QueryBuilder::for(Checkin::class)
            ->allowedFilters([
                AllowedFilter::custom('unit_id', new CheckinUnitFilter)->default($user->unit_id),
                AllowedFilter::custom('sub_unit_id', new CheckinSubUnitFilter)->default($user->sub_unit_id),
                AllowedFilter::custom('station_id', new CheckinStationFilter)->default($user->station_id),
                AllowedFilter::custom('office_id', new CheckinOfficeFilter)->default(implode(',', $userOffices)),
                AllowedFilter::exact('type'),
            ])
            ->when($userAccessibleClassifications->count(), function ($query) use ($userAccessibleClassifications) {
                return $query->whereHas('personnel', function ($personnelQuery) use ($userAccessibleClassifications) {
                    return $personnelQuery->whereIn('classification_id', $userAccessibleClassifications->toArray());
                });
            })
            ->with('personnel:id,personnel_id,first_name,middle_name,last_name')

            ->paginate($request->per_page ?? 10);

        return response($list);
    }
}
