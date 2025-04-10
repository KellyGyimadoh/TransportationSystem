<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seats extends Model
{
    /** @use HasFactory<\Database\Factories\SeatsFactory> */
    use HasFactory;
    protected $fillable = ['bus_id', 'seat_number', 'status'];

    // A seat can be booked through booking_seats table
    public function bookings() {
        return $this->belongsToMany(Bookings::class, 'booking_seats',
        'seat_id','booking_id');
    }
    // A seat belongs to a bus
    public function bus(){
        return $this->belongsTo(Buses::class,'bus_id');
    }
    
}
