<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    /** @use HasFactory<\Database\Factories\BookingsFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'trip_id', 'status', 'payment_status'];

     // A booking belongs to a user
    public function user(){
        return $this->belongsTo(User::class);
    }

     // A booking belongs to a trip
    public function trip(){
        return $this->belongsTo(Trips::class);
    }
     // A booking has many seats (through pivot table)
    public function seats() {
        return $this->belongsToMany(Seats::class, 'booking_seats');
    }
    
    // A booking has one payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    
}
