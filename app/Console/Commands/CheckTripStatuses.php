<?php

namespace App\Console\Commands;

use App\Models\Trips;
use Illuminate\Console\Command;

class CheckTripStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-trip-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Trip Status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trips= Trips::where('status','scheduled')
        ->where('departure_time','<=',now())->get();

        foreach ($trips as $trip) {
            $trip->update(['status'=>'ongoing']);
        }
    }
}
