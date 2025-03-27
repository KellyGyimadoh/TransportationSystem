<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    /** @use HasFactory<\Database\Factories\TicketsFactory> */
    use HasFactory;

    protected $fillable = ['booking_seats_id', 'ticket_number', 'qr_code', 'issued_by', 'issued_for', 'issue_date', 'status'];

   
    public function bookingSeat() {
        return $this->belongsTo(BookingSeats::class);
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
