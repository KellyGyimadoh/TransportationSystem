<?php

namespace Database\Seeders;

use App\Models\Bookings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings=Bookings::all();
        foreach($bookings as $booking){
            $booking->update(['slug'=> Str::uuid()->toString()]);
        }
    }
}
