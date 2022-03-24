<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CheckInType;
use App\Http\Controllers\Controller;
use App\Models\Checkin;
use App\Models\Personnel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admins')->user();
        $user->loadMissing(['offices:id,name', 'classifications:id,name']);

        return response([
            'data' => [
                'personnels' => [
                    'total' =>  $this->getPersonnelCountWithCheckin($user),
                    CheckInType::PRESENT => $this->getPersonnelCountWithCheckin($user, CheckInType::PRESENT),
                    CheckInType::LEAVE =>  $this->getPersonnelCountWithCheckin($user, CheckInType::LEAVE),
                    CheckInType::OFF_DUTY =>  $this->getPersonnelCountWithCheckin($user, CheckInType::OFF_DUTY),
                    CheckInType::UNACCOUNTED =>  $this->getPersonnelCountWithCheckin($user, CheckInType::UNACCOUNTED),
                ]
            ]
        ]);
    }

    public function getPersonnelCountWithCheckin($user, $type = null)
    {
        $date = Carbon::now()->format('Y-m-d');

        return Personnel::when(!$user->is_super_admin, function ($query) use ($user, $type, $date) {
            $query
                ->when($user->classifications->count(), function ($personnelSubQuery) use ($user) {
                    $classificationIds = $user->classifications->map(function ($classification) {
                        return $classification->id;
                    });
                    $personnelSubQuery->whereIn('classification_id', $classificationIds);
                })
                ->whereHas('assignment', function ($assignmentQuery) use ($user) {
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
        })
            ->when($type === CheckInType::UNACCOUNTED, function ($personnelSubQuery) use ($date) {
                $personnelSubQuery->whereDoesntHave('checkins', function ($checkinQuery) use ($date) {
                    return $checkinQuery->whereDate('created_at', $date);
                });
            })
            ->when($type && $type !== CheckInType::UNACCOUNTED, function ($personnelSubQuery)  use ($date, $type) {
                $personnelSubQuery->whereHas('checkins', function ($checkinQuery) use ($date, $type) {
                    return $checkinQuery->where('type', $type)->whereDate('created_at', $date);
                });
            })->count();
    }

    public function getCheckinCountByType($query, $type)
    {
        return $query->where('type', $type)->count();
    }
}
