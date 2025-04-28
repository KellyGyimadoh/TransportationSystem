<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    /** @use HasFactory<\Database\Factories\TicketsFactory> */
    use HasFactory;

    protected $fillable = ['booking_id', 'ticket_number', 'qr_code', 'issued_by',
     'issued_for', 'issue_date', 'status','trip_date','seat_reservation_id'];

   
    public function seatReservation() {
        return $this->belongsTo(SeatReservation::class);
    }
    public function booking(){
        return $this->belongsTo(Bookings::class);
    }
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function issuedFor()
    {
        return $this->belongsTo(User::class, 'issued_for');
    }
    
}
