<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    /** @use HasFactory<\Database\Factories\BookingsFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'trip_id', 'status', 'payment_status','trip_date'];


    protected static function booted()
{
    // static::created(function (Bookings $booking) {
    //     $seatIds = $booking->seats()->pluck('seats.id')->toArray();
    //     dd($seatIds);
    //     // Optional: revert all seats previously assigned to this booking
    //     Seats::whereIn('id', $seatIds)
    //         ->update(['status' => 'booked']);

       
    // });

    static::deleting(function (Bookings $booking) {
        // When the booking is deleted, update the status of all associated seats to 'available'
        $booking->seats()->update(['status' => 'available']);
    });
}


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
        return $this->belongsToMany(Seats::class, 'booking_seats',
        'booking_id','seat_id');
    }
    
    // A booking has one payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function isRefundable(): bool
    {
        // Customize this logic
        return now()->diffInMinutes($this->event_time, false) > 60; // Refund if > 1 hour before event
    } 
}
