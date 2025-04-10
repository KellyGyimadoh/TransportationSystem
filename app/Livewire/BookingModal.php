<?php

namespace App\Livewire;

use App\Models\Trips;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BookingModal extends Component
{
    protected $listeners = [ 'openModal'];

    public $show=false;
    public $tripDetails=null;
    public ?Carbon $tripDate;
#[Validate(["tripId"=>'required'],message:"Please Select A Trip")]
    public $tripId=null;
  
    
    public function openModal($tripId){
        $this->tripId = $tripId;
        $trip=Trips::with('routes')->findOrFail($this->tripId);
        $this->tripDetails =ucfirst($trip->routes->start_location) .' To '. ucfirst($trip->routes->end_location);
        $this->show=true;
    }

    public function closeModal(){
        $this->show=false;

        $this->tripDetails=null;
        $this->tripDate = null;
        $this->tripId = null;
    }
    public function submitBooking(){
       $validated= $this->validate([
            'tripDate'=>['required',function($attribute,$value,$fail){
                $today=now();
                $maxdate=$today->copy()->addDays(30);
                $mindate=$today;
                if($value<$mindate || $value>$maxdate){
                    $fail('Trips are not scheduled for the date selected. Please choose a date within the next 30 days.');
        
                }
            }]
        ]);
        Log::info($this->tripDate);
        Log::info($this->tripId);
        Log::info($validated);
        $this->closeModal();
    }
    public function render()
    {
        return view('livewire.booking-modal');
    }
}
