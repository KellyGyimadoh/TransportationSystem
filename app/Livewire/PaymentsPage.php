<?php

namespace App\Livewire;

use App\Models\Bookings;
use App\Models\Payment;
use App\Models\Tickets;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentsPage extends Component
{
    public Bookings $bookings;
    public $amount=null;

    public $paymentMethod;
    public function makePayment(){
        if($this->bookings->user_id !== Auth::user()->id){
            abort(403,'unauthorized access');
        }
        if($this->bookings->payment_status==='paid'){
            session()->flash('error','Payment Made Already');
            $this->redirectIntended('mybookings');
           
        }else{
        $this->validate(['paymentMethod'=>['required']]);
        $payments=Payment::create([
            'booking_id'=>$this->bookings->id,
            'amount'=>$this->amount,
                'payment_method'=>$this->paymentMethod,
                'status'=>'successful'
        ]);
        $this->bookings->update(['payment_status'=>'paid','status'=>'confirmed']);
        if($payments->status=='successful'){
        
           foreach ($this->bookings->seatReservations as $seat) {
            $seat->update(['status'=>'booked']);
            Tickets::create(['booking_id' => $this->bookings->id,
                                        'seat_reservation_id' => $seat->id,
                                        'ticket_number'=>uniqid('TT').$seat->id,
                                        'issued_by'=>Auth::user()->id,
                                        'issued_for'=>$this->bookings->user_id,
                                        'trip_date'=>Carbon::parse($this->bookings->trip_date)->toDateString(),
                                        'status'=> 'valid',
        ]);
           }
        }
        session()->flash('success','Payment Successful');
        $this->redirectRoute('tickets', $this->bookings);
    }
    }
    public function mount(Bookings $bookings){
        $this->bookings=$bookings;
        $this->amount=$this->bookings->seatReservations->count() * $this->bookings->trip->price;
       
    }
    public function render()
    {
        return view('livewire.payments-page');
    }
}
