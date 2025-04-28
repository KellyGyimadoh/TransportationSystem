<?php

namespace App\Filament\Resources\BookingsResource\Pages;

use App\Filament\Resources\BookingsResource;
use App\Models\SeatReservation;
use App\Models\Seats;
use App\Models\Trips;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditBookings extends EditRecord
{
    protected static string $resource = BookingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected static function getEloquentQuery()
    {
        return parent::getEloquentQuery()->with(['seatReservations', 'seats']);
    }
    protected function afterSave(): void
    {
        $this->updateSeatStatuses();
    }

    protected function updateSeatStatuses(): void
{
    $selectedSeatIds = $this->form->getState()['seats'];
    $existingSeatIds = SeatReservation::where('booking_id', $this->record->id)
        ->pluck('seat_id')
        ->toArray();

    $bookingCanceled = $this->record->status === 'canceled';
    $newStatus = $this->record->payment_status === 'paid' ? 'booked' : 'reserved';

    // Handle canceled booking
    if ($bookingCanceled) {
        SeatReservation::where('booking_id', $this->record->id)
            ->update(['status' => 'cancelled']);
        return;
    }

    // Removed seat IDs
    $removedSeatIds = collect($existingSeatIds)->diff($selectedSeatIds)->values()->toArray();

    if (!empty($removedSeatIds)) {
        SeatReservation::where('booking_id', $this->record->id)
            ->whereIn('seat_id', $removedSeatIds)
            ->update(['status' => 'cancelled']);
    }

    // New seat IDs (added)
    $newSeatIds = collect($selectedSeatIds)->diff($existingSeatIds)->values()->toArray();

    foreach ($newSeatIds as $seatId) {
        SeatReservation::create([
            'booking_id'    => $this->record->id,
            'trip_id'       => $this->record->trip_id,
            'user_id' => $this->record->user_id,
            'seat_id'       => $seatId,
            'booking_date'  => $this->record->trip_date,
            'status'        => $newStatus,
        ]);
    }

    // Update status for remaining (existing or newly added) seats
    SeatReservation::where('booking_id', $this->record->id)
        ->whereIn('seat_id', $selectedSeatIds)
        ->update(['status' => $newStatus]);
}

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $data['bookingid'] = $this->record->id;
        $data['tripId'] = $this->record->trip_id;
        $data['selectedDate'] = $this->record->trip_date;
        $data["seats"] = $this->record->seatReservations()->whereIn('status',['reserved','booked'])
        ->pluck('seat_id')->toArray();

        $busHandlingTrip = Trips::where('id', $this->record->trip_id)->first();
        $data['bus'] = $busHandlingTrip->bus_id;
        // $data['seats'] = SeatReservation::where('booking_id', $this->record->id)
        // ->where('booking_date', $this->record->trip_date)
        // ->whereIn('status', ['reserved', 'booked'])
        // ->pluck('seat_id');

        $reservedSeatIds = SeatReservation::where('trip_id', $this->record->trip_id)
            ->whereIn('status', ['reserved', 'booked'])
            ->pluck('seat_id')
            ->toArray();

        $data['seatsnumber'] = Seats::where('bus_id', $this->record->trip->bus_id)
            ->whereNotIn('id', $reservedSeatIds)
            ->count();

        return $data;
    }
    // protected function getRedirectUrl(): string|null
    // {
    //     return $this->getResource()::geturl('index');
    // }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] === 'canceled' && $data['payment_status'] === 'paid') {
            if (!$this->record->isRefundable()) {


                Notification::make()
                    ->title('Booking cancellation blocked')
                    ->body('This paid booking cannot be canceled. Please contact an admin.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'status' => 'This paid booking cannot be canceled. Please contact an admin.',
                ]);
            }

            // You could trigger a refund here if connected to Stripe or Flutterwave
            // $this->record->refund();

            // Optionally: $this->record->update(['refunded' => true]);
        }

        return $data;
    }

}
