<?php

namespace App\Filament\Resources\BookingsResource\Pages;

use App\Filament\Resources\BookingsResource;
use App\Models\Bookings;
use App\Models\Payment;
use App\Models\SeatReservation;
use App\Models\Seats;
use App\Models\Tickets;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class CreateBookings extends CreateRecord
{
    protected static string $resource = BookingsResource::class;
    protected $model= Bookings::class;

    protected function afterCreate(): void
    {
        $this->updateSeatStatuses();
    }

   
   

    protected function updateSeatStatuses(): void
    {
        DB::transaction(function () {
            $selectedSeatIds = $this->form->getState()['seats'];
            $amount = $this->form->getState()['total_price'];
            $paymentMethod = $this->form->getState()['payment_method'];
            $newStatus = $this->record->payment_status === 'paid' ? 'booked' : 'reserved';
    
            $reservations = [];
    
            foreach ($selectedSeatIds as $seat) {
                $reservation = SeatReservation::create([
                    'seat_id' => $seat,
                    'trip_id' => $this->record->trip_id,
                    'user_id' => $this->record->user_id,
                    'booking_date' => $this->record->trip_date,
                    'status' => $newStatus,
                    'booking_id' => $this->record->id,
                ]);
    
                $reservations[] = $reservation;
            }
    
            if ($newStatus === 'booked') {
                Payment::firstOrCreate([
                    'booking_id' => $this->record->id,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'status' => 'successful',
                ]);
    
                foreach ($reservations as $reservation) {
                    $ticketNumber = uniqid('TT') . $reservation->seat_id;
    
                    Tickets::create([
                        'booking_id' => $this->record->id,
                        'seat_reservation_id' => $reservation->id,
                        'ticket_number' => $ticketNumber,
                        'qr_code' => $ticketNumber,
                        'issued_by' => Auth::id(),
                        'issued_for' => $this->record->user_id,
                        'trip_date' => Carbon::parse($this->record->trip_date)->toDateString(),
                        'status' => 'valid',
                    ]);
                }
            }
        });
    }
    
}
