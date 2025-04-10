<?php

namespace App\Livewire;

use App\Models\Trips;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;


class Bookings extends Component
{
    use WithPagination;
    // public $bookings;
    
    //public $trips;

    // #[Validate(['currentTripId'=>"required"],message:"Please select a trip")]
    // public $currentTripId=null;

    // public $price=null;

   
    // public ?Carbon $tripDate=null;

   
    // protected function messages() 
    // {
    //     return [
    //         'tripDate.required' => 'Please Select A Trip Date',
            
    //     ];
    // }
    // public function  submitBooking(){
    //     Log::info($this->currentTripId);
    //     Log::info($this->tripDate);
    //     // $validated=$this->validate();
    //     // Log::info('Booking submitted for trip: ' . $this->currentTripId );
            
    //  }
   

    // public function updatedCurrentTripId($tripId){
    //     if($tripId){
    //     // Log::info('Trip updated: ' . $tripId);
     
    //     $trip=Trips::with('routes')->findOrFail($tripId);
    //     $this->currentTrip=$trip;
    //   $this->price=  $trip->price ;
    // //   Log::info('Price updated to: ' . $this->price);
    //     }else{

    //         $this->currentTripId=null;    
    //         $this->price= null;
    //     }
     
    // }
    // public function change(){
    //     $this->dispatch('updatedCurrentTrip');
    // }
    #[Url]
    public $search="";  
    public function mount(){
      //  $this->trips = Trips::with('routes')->where('status', 'scheduled')->get();
        
  
    }
    public function render()
    {
        return view('livewire.bookings',['trips'=>
        Trips::with('routes')
        ->when($this->search !=='', function ($query) {
            return $query->whereHas('routes',function($query){
                $query->where('start_location','like','%'.$this->search.'%')
                ->orWhere('end_location','like','%'.$this->search.'%');
                ;
            });
        })
        ->where('status', 'scheduled')
        ->paginate(10)]);
    }
    public function updating($key): void
    {
        if ($key === 'search' ) {
            $this->resetPage();
        }
    }

}
