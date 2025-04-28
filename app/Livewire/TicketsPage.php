<?php

namespace App\Livewire;

use App\Models\Bookings;
use App\Models\Payment;
use App\Models\Tickets;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TicketsPage extends Component
{
public Bookings $bookings;
public $paymentDetails;

public $ticketDetails;
    public function mount(Bookings $bookings){
        $this->bookings=$bookings;
        $this->paymentDetails= Payment::where('booking_id',$this->bookings->id)->first();
        $this->ticketDetails=Tickets::where('booking_id',$this->bookings->id)->get();
        if($this->bookings->user_id !== Auth::user()->id){
            abort(403,'unauthorized access');
        }
        if($this->ticketDetails->isEmpty()){
            foreach ($this->bookings->seatReservations as $seat) {
                $seat->update(['status'=>'booked']);
                $ticketNumber = uniqid('TT') . $seat->id;
                Tickets::create(['booking_id' => $this->bookings->id,
                                            'seat_reservation_id' => $seat->id,
                                            'ticket_number'=>$ticketNumber,
                                            'qr_code'=>$ticketNumber,
                                            'issued_by'=>Auth::user()->id,
                                            'issued_for'=>$this->bookings->user_id,
                                            'trip_date'=>Carbon::parse($this->bookings->trip_date)->toDateString(),
                                            'status'=> 'valid',
            ]);
            }
            $this->ticketDetails = Tickets::where('booking_id', $this->bookings->id)->get();
        }

       
    
    }
    public function render()
    {
        return view('livewire.tickets-page');
    }
}
