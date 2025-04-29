<?php

namespace App\Livewire;

use App\Http\Resources\TripsResource;
use App\Models\Trips;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Title;
use Livewire\Component;

class WelcomePage extends Component
{
//    #[Title('Welcome ')]
    public function render()
    {
        return view('livewire.welcome-page',['trips'=>
        Cache::remember('trips',60*60,function(){
            return TripsResource::collection(Trips::all());
        })])->title('Welcome '.Auth::user()->name);
    }

    
}
