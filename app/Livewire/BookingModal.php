<?php

namespace App\Livewire;

use App\Models\Bookings;
use App\Models\BookingSeats;
use App\Models\SeatReservation;
use App\Models\Seats;
use App\Models\Trips;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BookingModal extends Component
{
    protected $listeners = ['openModal'];

    public $show = false;
    public $tripDetails = null;
    public ?Carbon $tripDate = null;

    #[Validate(["tripId" => 'required'], message: "Please Select A Trip")]
    public $tripId = null;

    public $busId = null;
    public $seatsAvailable = null;
    public $seatsbooked = null;

    public $tripDepartureTime=null;

    public function openModal($tripId)
    {
        
        $this->tripId = $tripId;
        $trip = Trips::with(['routes', 'bus'])->findOrFail($this->tripId);
        $this->tripDepartureTime = $trip->departure_time;
        

        // Assuming trip status is stored on the trip itself (not seatReservations)
        // if (in_array($trip->status, ['ongoing', 'completed'])) {
        //     session()->flash('error', 'This trip is not available for booking.');
        //     $this->show = false;
        //     return;
        // }
      

        $this->tripDetails = ucfirst($trip->routes->start_location) . ' To ' . ucfirst($trip->routes->end_location);
        $this->busId = $trip->bus->id;
        $this->seatsAvailable = null; // Will be updated once tripDate is selected
        $this->show = true;
        
    }

    public function updatedTripDate($value)
    {
        if (!$value || !$this->tripId || !$this->busId) return;

        $selectedDate = Carbon::parse($value)->toDateString();
        $fullTripDateTime = Carbon::parse($selectedDate . ' ' . $this->tripDepartureTime);

        if ($selectedDate === today()->toDateString() && $fullTripDateTime->lt(now())) {
            $this->closeModal();
            session()->flash('error', 'You can no longer book for today. This trip has already departed.');
            $this->redirectIntended();
            
        }

        // Fetch reserved seat IDs for the selected trip and date
        $reservedSeatIds = SeatReservation::where('trip_id', $this->tripId)
            ->where('booking_date', $selectedDate)
            ->whereIn('status', ['reserved', 'booked'])
            ->pluck('seat_id');

        // Count available seats by excluding reserved ones
        $this->seatsAvailable = Seats::where('bus_id', $this->busId)
            ->whereNotIn('id', $reservedSeatIds)
            ->count();
    }

    public function closeModal()
    {
        $this->show = false;

        $this->tripDetails = null;
        $this->tripDate = null;
        $this->tripId = null;
        $this->busId = null;
        $this->seatsAvailable = null;
        $this->seatsbooked = null;
        $this->tripDepartureTime=null;
    }

    public function submitBooking()
    {
        $validated = $this->validate([
            'seatsbooked' => ['required', 'integer', function ($attribute, $value, $fail) {
                if ($value > $this->seatsAvailable || $value <= 0) {
                    $fail('Please select a valid number of seats.');
                }
            }],
            'tripDate' => ['required', function ($attribute, $value, $fail) {
                $today = now()->toDateString();
                $maxdate = now()->copy()->addDays(30)->toDateString();
                $selectedDate = Carbon::parse($value)->toDateString();

                if ($selectedDate < $today || $selectedDate > $maxdate) {
                    $fail('Trips are not scheduled for the date selected. Please choose a date within the next 30 days.');
                }
                
            }]
        ]);

        $user = Auth::user();

        // Create booking
        if(!$user){
            $this->closeModal();
            session()->flash('error', 'Must be Logged In to enable Booking.');
           $this->redirectIntended('/login');   
        }
       

        if ($validated['seatsbooked']) {

           
            // Get available seats
            $reservedSeatIds = SeatReservation::where('trip_id', $this->tripId)
                ->where('booking_date', $validated['tripDate'])
                ->whereIn('status', ['reserved', 'booked'])
                ->pluck('seat_id');

            $availableSeats = Seats::where('bus_id', $this->busId)
                ->whereNotIn('id', $reservedSeatIds)
                ->orderBy('seat_number')
                ->take($validated['seatsbooked'])
                ->get();

            if ($availableSeats->count() < $validated['seatsbooked']) {
                session()->flash('error', 'Not enough available seats for this date.');
                return;
            }
            $booking = Bookings::create([
                'trip_date' => $validated['tripDate'],
                'user_id' => $user->id,
                'trip_id' => $this->tripId
            ]);
            foreach ($availableSeats as $seat) {
                // Reserve seat
                SeatReservation::create([
                    'seat_id' => $seat->id,
                    'trip_id' => $this->tripId,
                    'user_id' => $user->id,
                    'booking_date' => $validated['tripDate'],
                    'status' => 'reserved',
                    'booking_id' => $booking->id,
                ]);

                // Attach seat to booking
               // $booking->seats()->attach($seat->id);
            }
        }
        
        $this->closeModal();
        session()->flash('success', 'Booking successful.');
       $this->redirectIntended();

    }
   
    public function submitPayment()
    {
        // Placeholder for future payment logic
    }

    public function render()
    {
        return view('livewire.booking-modal');
    }
}
