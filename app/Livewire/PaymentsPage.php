<?php

namespace App\Livewire;

use App\Models\Bookings;
use App\Models\Payment;
use App\Models\Tickets;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
 use Illuminate\Support\Facades\Http;
class PaymentsPage extends Component
{
    protected $listeners=['paymentSuccessful'];
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
           
        }
        $this->dispatch('initiatePaystack',
           email: $this->bookings->user->email,
           amount: $this->amount *100, // Paystack expects amount in kobo
           reference: uniqid('PS_'), // or use a UUID
           callback_url:'transport.test',
            
    );
        
    //     else{
    //    // $this->validate(['paymentMethod'=>['required']]);

    //     $payments=Payment::create([
    //         'booking_id'=>$this->bookings->id,
    //         'amount'=>$this->amount,
    //             'payment_method'=>$this->paymentMethod,
    //             'status'=>'successful'
    //     ]);
    //     $this->bookings->update(['payment_status'=>'paid','status'=>'confirmed']);
    //     if($payments->status=='successful'){
        
    //        foreach ($this->bookings->seatReservations as $seat) {
    //         $seat->update(['status'=>'booked']);
    //         Tickets::create(['booking_id' => $this->bookings->id,
    //                                     'seat_reservation_id' => $seat->id,
    //                                     'ticket_number'=>uniqid('TT').$seat->id,
    //                                     'issued_by'=>Auth::user()->id,
    //                                     'issued_for'=>$this->bookings->user_id,
    //                                     'trip_date'=>Carbon::parse($this->bookings->trip_date)->toDateString(),
    //                                     'status'=> 'valid',
    //     ]);
    //        }
    //     }
    //     session()->flash('success','Payment Successful');
    //     $this->redirectRoute('tickets', $this->bookings);
    // }
    }



public function paymentSuccessful($response)
{
    
    $reference = $response['reference'] ?? null;

    if (!$reference) {
        session()->flash('error', 'Payment reference missing.');
        return;
    }

    $secretKey = config('services.paystack.secret');

    // Verify transaction with Paystack API
    $response = Http::withToken($secretKey)
        ->get("https://api.paystack.co/transaction/verify/{$reference}");

    if (!$response->successful()) {
        session()->flash('error', 'Unable to verify payment at the moment.');
        return;
    }

    $result = $response->json();

    if ($result['status'] !== true || $result['data']['status'] !== 'success') {
        session()->flash('error', 'Payment verification failed or transaction not successful.');
        return;
    }

    // Extract payment method from authorization channel
    $channel = $result['data']['authorization']['channel'] ?? 'unknown';

    // Create the payment record
    $payment = Payment::create([
        'booking_id' => $this->bookings->id,
        'amount' => $this->amount,
        'payment_method' => $channel, // 'card', 'momo', etc.
        'status' => 'successful',
        'reference' => $reference,
        'raw_response' => json_encode($result['data']), // Optional: save full response
    ]);

    // Update booking status
    $this->bookings->update([
        'payment_status' => 'paid',
        'status' => 'confirmed',
    ]);

    // Create tickets and update seat status
    foreach ($this->bookings->seatReservations as $seat) {
        $seat->update(['status' => 'booked']);

        Tickets::create([
            'booking_id' => $this->bookings->id,
            'seat_reservation_id' => $seat->id,
            'ticket_number' => uniqid('TT') . $seat->id,
            'issued_by' => Auth::user()->id,
            'issued_for' => $this->bookings->user_id,
            'trip_date' => Carbon::parse($this->bookings->trip_date)->toDateString(),
            'status' => 'valid',
        ]);
    }

    session()->flash('success', 'Payment verified and booking confirmed successfully!');
    $this->redirectRoute('tickets', $this->bookings);
}

    public function mount(Bookings $bookings){
        $this->bookings=$bookings;
        $this->amount=$this->bookings->seatReservations->count() * $this->bookings->trip->price;
       
    }
    public function render()
    {
        return view('livewire.payments-page');
    }

    public function initializeTransaction(){
        $url = config('services.paystack.transactionurl');

        $fields = [
          'email' => $this->bookings->user->email,
          'amount' => $this->amount,
        ];
      
        $fields_string = http_build_query($fields);
      
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Authorization: Bearer SECRET_KEY",
          "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        //execute post
        $result = curl_exec($ch);
       
      
    }
}
