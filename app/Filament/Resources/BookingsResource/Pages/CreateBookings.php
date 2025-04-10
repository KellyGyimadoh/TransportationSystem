<?php

namespace App\Filament\Resources\BookingsResource\Pages;

use App\Filament\Resources\BookingsResource;
use App\Models\Bookings;
use App\Models\Seats;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
        $seatIds = $this->record->seats()->pluck('seats.id')->toArray();

        Seats::whereIn('id', $seatIds)->update([
            'status' => $this->record->payment_status === 'paid' ? 'booked' : 'reserved',
        ]);
    }
}
