<?php

namespace App\Console\Commands;

use App\Enums\CheckInType;
use App\Models\Checkin;
use App\Models\Personnel;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InsertUnaccountedCheckins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:unaccounted_checkins {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert unaccounted personnel checkins.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $unaccountedCheckins = Personnel::select('id', 'status')
            ->whereDate('created_at', '<=', $this->argument('date'))
            ->whereDoesntHave('checkins', function ($checkinQuery) {
                return $checkinQuery->whereDate('created_at', $this->argument('date'));
            })
            ->get()
            ->map(function ($personnel) {
                return [
                    'personnel_id' => $personnel->id,
                    'type' => $personnel->status === 'active' ? CheckInType::UNACCOUNTED : 'inactive',
                    'created_at' => Carbon::parse($this->argument('date'))->endOf('day')->toDateTimeString(),
                    'updated_at' => Carbon::parse($this->argument('date'))->endOf('day')->toDateTimeString(),
                ];
            })
            ->toArray();

        Log::info("Saving unaccounted checkins for " . $this->argument('date') . ". Total: " . count($unaccountedCheckins));

        Checkin::insert($unaccountedCheckins);
    }
}
