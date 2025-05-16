<?php

namespace App\Console\Commands;

use App\Models\Bookings;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-payment-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Payment Status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today=today()->toDateString();
        $bookings=Bookings::where('payment_status','unpaid')
        ->where('trip_date','<',$today)->get();

        foreach($bookings as $booking){
            $booking->update(['status'=>'canceled']);
        }
    }
}
