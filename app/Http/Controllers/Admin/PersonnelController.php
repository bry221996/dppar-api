<?php

namespace App\Http\Controllers\Admin;

use App\Filters\Personnel\PersonnelOfficeFilter;
use App\Http\Controllers\Controller;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\Personnel\PersonnelStationFilter;
use App\Filters\Personnel\PersonnelSubUnitFilter;
use App\Filters\Personnel\PersonnelUnitFilter;

use App\Http\Requests\Admin\Personnel\CreateRequest;
use App\Http\Requests\Admin\Personnel\UpdateRequest;

class PersonnelController extends Controller
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

        $list = QueryBuilder::for(Personnel::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
                AllowedFilter::custom('unit_id', new PersonnelUnitFilter)->default($user->unit_id),
                AllowedFilter::custom('sub_unit_id', new PersonnelSubUnitFilter)->default($user->sub_unit_id),
                AllowedFilter::custom('station_id', new PersonnelStationFilter)->default($user->station_id),
                AllowedFilter::custom('office_id', new PersonnelOfficeFilter)->default(implode(',', $userOffices))
            ])
            ->with(['classification'])
            ->when($userAccessibleClassifications->count(), function ($query) use ($userAccessibleClassifications) {
                return $query->whereIn('classification_id', $userAccessibleClassifications->toArray());
            })
            ->when(!$user->is_super_admin && !$user->is_intel, function ($query) {
                return $query->where('is_intel', false);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);

        return response($list);
    }

    public function show(Personnel $personnel)
    {
        $personnel->loadMissing('assignment');

        return response([
            'message' => 'Personnel successfully fetched.',
            'data' => $personnel
        ]);
    }

    public function store(CreateRequest $request)
    {
        $personnel = Personnel::create($request->personnelData());

        $personnel->assignment()->create($request->assignmentData());

        return response([
            'message' => 'Personnel successfully created.',
            'data' => $personnel
        ]);
    }

    public function update(UpdateRequest $request, Personnel $personnel)
    {
        $data = $request->personnelData();
        $needLogout = $personnel->personnel_id !== $data['personnel_id'] || $personnel->birth_date !== $data['birth_date'];

        $personnel->update($data);

        $personnel->assignment()->first()->update($request->assignmentData());

        if ($personnel->fresh()->is_inactive && $needLogout) {
            $personnel->tokens()->delete();
        }

        return response([
            'message' => 'Personnel successfully updated.',
            'data' => $personnel
        ]);
    }

    public function activate(Personnel $personnel)
    {
        $personnel->update(['status' => 'active']);

        return response([
            'message' => 'Personnel successfully updated.',
            'data' => $personnel
        ]);
    }

    public function deactivate(Personnel $personnel)
    {
        $personnel->update(['status' => 'inactive']);

        return response([
            'message' => 'Personnel successfully updated.',
            'data' => $personnel
        ]);
    }

    public function destroy(Personnel $personnel)
    {
        $personnel->delete();

        return response([
            'message' => 'Personnel successfully deleted.',
            'data' => $personnel
        ]);
    }

    public function restore($id)
    {
        $personnel = Personnel::withTrashed()->findOrFail($id);
        $personnel->restore();

        return response([
            'message' => 'Personnel successfully restored.',
            'data' => $personnel
        ]);
    }
}
