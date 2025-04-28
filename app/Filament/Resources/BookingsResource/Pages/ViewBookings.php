<?php

namespace App\Filament\Resources\BookingsResource\Pages;

use App\Filament\Resources\BookingsResource;
use App\Models\SeatReservation;
use App\Models\Seats;
use App\Models\Trips;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookings extends ViewRecord
{
    protected static string $resource = BookingsResource::class;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data["seats"] = $this->record->seatReservations()->pluck('seat_id')->toArray();
        // $data['seats'] = SeatReservation::where('booking_id', $this->record->id)
        // ->where('booking_date', $this->record->trip_date)
        // ->whereIn('status', ['reserved', 'booked'])
        // ->pluck('seat_id');
        $busHandlingTrip = Trips::where('id', $this->record->trip_id)->first();
        $data['bus'] = $busHandlingTrip->bus_id;
        $data['seatsnumber'] =Seats::where('bus_id', $this->record->trip->bus_id)
        ->whereNotIn('id', $data['seats'])
          ->count();

        return $data;
    }
}
