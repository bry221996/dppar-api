<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CheckInType;
use App\Http\Controllers\Controller;
use App\Models\Checkin;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admins')->user();
        $user->loadMissing(['offices:id,name', 'classifications:id,name']);

        $query = Checkin::when(!$user->is_super_admin, function ($query) use ($user) {
            $query->whereHas('personnel', function ($personnelQuery) use ($user) {
                $personnelQuery
                    ->when($user->classifications->count(), function ($personnelSubQuery) use ($user) {
                        $classificationIds = $user->classifications->map(function ($classification) {
                            return $classification->id;
                        });
                        $personnelSubQuery->whereIn('classification_id', $classificationIds);
                    })
                    ->whereHas('assignments', function ($assignmentQuery) use ($user) {
                        $assignmentQuery
                            ->when($user->unit_id, function ($subAssignmentQuery) use ($user) {
                                $subAssignmentQuery->where('unit_id', $user->unit_id);
                            })
                            ->when($user->sub_unit_id, function ($subAssignmentQuery) use ($user) {
                                $subAssignmentQuery->where('sub_unit_id', $user->sub_unit_id);
                            })
                            ->when($user->station_id, function ($subAssignmentQuery) use ($user) {
                                $subAssignmentQuery->where('station_id', $user->station_id);
                            })
                            ->when($user->offices->count(), function ($subAssignmentQuery) use ($user) {
                                $officeIds = $user->offices
                                    ->map(function ($office) {
                                        return $office->id;
                                    })->toArray();

                                $subAssignmentQuery->whereIn('office_id', $officeIds);
                            });
                    });
            });
        });

        return response([
            'data' => [
                'checkins' => [
                    CheckInType::PRESENT => $this->getCheckinCountByType($query, CheckInType::PRESENT),
                    CheckInType::LEAVE =>  $this->getCheckinCountByType($query, CheckInType::LEAVE),
                    CheckInType::OFF_DUTY =>  $this->getCheckinCountByType($query, CheckInType::OFF_DUTY),
                    CheckInType::UNACCOUNTED =>  $this->getCheckinCountByType($query, CheckInType::UNACCOUNTED),
                ]
            ]
        ]);
    }

    public function getCheckinCountByType($query, $type)
    {
        return $query->where('type', $type)->count();
    }
}
