<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filters\Personnel\PersonnelOfficeFilter;
use App\Filters\Personnel\PersonnelStationFilter;
use App\Filters\Personnel\PersonnelSubUnitFilter;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\Personnel\PersonnelUnitFilter;
use App\Models\Personnel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PersonnelAttendanceExport implements FromCollection, WithHeadings
{
    protected $start_date;

    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;

        $this->end_date = $end_date;
    }

    public function headings(): array
    {
        $header =  [
            'Designation',
            'Name',
        ];

        $dateReference = Carbon::parse($this->start_date);

        while ($dateReference <= Carbon::parse($this->end_date)) {
            array_push($header, $dateReference->format('m/d'));
            $dateReference->addDay();
        }

        return $header;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
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

        $collection = QueryBuilder::for(Personnel::class)
            ->select(['id', 'designation', 'first_name', 'last_name', 'middle_name', 'qualifier'])
            ->allowedFilters([
                AllowedFilter::custom('unit_id', new PersonnelUnitFilter)->default($user->unit_id),
                AllowedFilter::custom('sub_unit_id', new PersonnelSubUnitFilter)->default($user->sub_unit_id),
                AllowedFilter::custom('station_id', new PersonnelStationFilter)->default($user->station_id),
                AllowedFilter::custom('office_id', new PersonnelOfficeFilter)->default(implode(',', $userOffices))
            ])
            ->when($userAccessibleClassifications->count(), function ($query) use ($userAccessibleClassifications) {
                return $query->whereIn('classification_id', $userAccessibleClassifications->toArray());
            })
            ->when(!$user->is_super_admin && !$user->is_intel, function ($query) {
                return $query->where('is_intel', false);
            })
            ->with([
                'checkins' => function ($checkinSubQuery) {
                    return $checkinSubQuery
                        ->select(['id', 'personnel_id', 'type', 'created_at'])
                        ->whereDate('created_at', '<=', request()->get('end_date'))
                        ->whereDate('created_at', '>=', request()->get('start_date'))
                        ->orderBy('created_at', 'asc');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($personnel) {
                $data = [
                    $personnel->designation,
                    $personnel->last_name . ", " . $personnel->first_name,
                ];

                $checkins = $personnel->checkins->map(function ($checkin) {
                    $checkin->date = Carbon::parse($checkin->created_at)->format('Y-m-d');
                    return $checkin;
                });

                $dateReference = Carbon::parse($this->start_date);

                while ($dateReference <= Carbon::parse($this->end_date)) {
                    $checkin = $checkins->where('date', $dateReference->format('Y-m-d'))->first();
                    array_push($data, $checkin ? $checkin->type_short_code : "");
                    $dateReference->addDay();
                }

                return $data;
            });


        return $collection;
    }
}
