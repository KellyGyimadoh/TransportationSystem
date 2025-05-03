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
public $userName='';

    public function render()
    {
        return view('livewire.welcome-page',['trips'=>
        Cache::remember('trips',60*60,function(){
            return TripsResource::collection(Trips::all());
        })])->title('Welcome '.$this->userName);
    }

    public function alertMessage(){
        session()->flash('error','Please log in to enable booking');
        $this->redirectIntended('/login');
    }
    public function mount(){
        if(Auth::user()){
        $this->userName=Auth::user()->name ?: Auth::user()->name;
        }
    }

    
}
