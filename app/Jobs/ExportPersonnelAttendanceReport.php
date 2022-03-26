<?php

namespace App\Jobs;

use App\Exports\PersonnelAttendanceExport;
use App\Notifications\ExportReportNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportPersonnelAttendanceReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $start_date;

    protected $end_date;


    public function __construct($user, $start_date, $end_date)
    {
        $this->user = $user;

        $this->start_date = $start_date;

        $this->end_date = $end_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filename = "reports/" . Str::random(40) . ".csv";

        $exported = Excel::store(new PersonnelAttendanceExport($this->start_date, $this->end_date), $filename, 'do_spaces');

        if ($exported) {
            $link = Storage::disk('do_spaces')->url($filename);

            $this->user->notify(new ExportReportNotification('Personnel Attendance Report', $link));
        }
    }
}
