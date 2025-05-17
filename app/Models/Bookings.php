<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bookings extends Model
{
    /** @use HasFactory<\Database\Factories\BookingsFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'trip_id', 'status', 'payment_status','trip_date'];


    protected static function boot()
{
    parent::boot();
     static::creating(function (Bookings $booking) {
   $booking->slug=Str::uuid()->toString();

       
     });

    // static::deleting(function (Bookings $booking) {
    //     // When the booking is deleted, update the status of all associated seats to 'available'
    //     $booking->seats()->update(['status' => 'available']);
    // });
}

public function getRouteKeyName(){
    return 'slug';
}
public function seatReservations()
{
    return $this->hasMany(SeatReservation::class,'booking_id','id');
}


     // A booking belongs to a user
    public function user(){
        return $this->belongsTo(User::class);
    }

     // A booking belongs to a trip
    public function trip(){
        return $this->belongsTo(Trips::class);
    }
     //A booking has many seats (through pivot table)
    // public function seats() {
    //     return $this->belongsToMany(Seats::class, 'booking_seats',
    //     'booking_id','seat_id');
    // }
    
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

    public function tickets(){
        return $this->hasMany(Tickets::class,'booking_id');
    }
}
