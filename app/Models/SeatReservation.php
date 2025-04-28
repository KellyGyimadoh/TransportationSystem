<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatReservation extends Model
{
    protected $fillable = ['seat_id', 'trip_id', 'user_id', 'booking_date', 'status','booking_id'];

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seats::class, 'seat_id');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trips::class, 'trip_id');
    }
    public function booking()
{
    return $this->belongsTo(Bookings::class,'booking_id');
}


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket(){
        return $this->hasOne(Tickets::class);
    }
}
