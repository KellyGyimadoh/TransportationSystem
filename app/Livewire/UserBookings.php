<?php

namespace App\Livewire;

use App\Models\Bookings;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserBookings extends Component
{
    public $user;

    public $search = '';



    public function mount()
    {
        $this->user = Auth::user();

        if (!$this->user) {
            abort(403); // Or redirect to login if preferred
        }
    }

    public function render()
    {
        return view('livewire.user-bookings', [
            'userbookings'
            => Bookings::with(['trip','seatReservations'])
                    ->where('user_id', Auth::user()->id)
                    ->when($this->search !== '', function ($query) {
                        return $query->whereHas('trip.routes', function ($query) {

                           
                           $query ->where('start_location', 'LIKE', '%' . $this->search . '%')
                                ->orWhere('end_location', 'LIKE', '%' . $this->search . '%');


                        });


                    })

                    ->orderBy('created_at', 'desc')->paginate(5)
        ]);
    }
}
