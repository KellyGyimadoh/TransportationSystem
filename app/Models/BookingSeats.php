<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use function Laravel\Prompts\table;

class BookingSeats extends Model
{
    protected $table = "booking_seats";
    protected $fillable = ['booking_id', 'seat_id'];
    public function booking()
    {
        return $this->belongsTo(Bookings::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seats::class);
    }
    public function ticket()
    {
        return $this->hasOne(Tickets::class, 'booking_seats_id');
    }
}
