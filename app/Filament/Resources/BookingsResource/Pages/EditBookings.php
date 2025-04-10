<?php

namespace App\Filament\Resources\BookingsResource\Pages;

use App\Filament\Resources\BookingsResource;
use App\Models\Seats;
use App\Models\Trips;
use DB;
use Filament\Actions;
use Filament\Forms\Components\Builder;
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
        return parent::getEloquentQuery()->with('seats');
    }
    protected function afterSave(): void
    {
        $this->updateSeatStatuses();
    }

    protected function updateSeatStatuses(): void
    {
        $newSeatIds = $this->record->seats()->pluck('seats.id')->toArray();
        $oldSeatIds = $this->record->getOriginal('seats') ?? [];
        $bookingCanceled = $this->record->status === 'canceled';

      

        if ($bookingCanceled) {
            Seats::whereIn('id', $newSeatIds)->update([
                'status' => 'available',
            ]);
            return;
        }

        $removedSeatIds = array_diff($oldSeatIds, $newSeatIds);
        $removedSeatsQueryIds = DB::table('booking_seats')
            ->leftJoin('bookings', 'bookings.id', '=', 'booking_seats.booking_id')
            ->pluck('booking_seats.seat_id');  // or use 'id' if seat_id is different

        Seats::whereNotIn('id', $removedSeatsQueryIds)->update(['status' => 'available']);
        Seats::whereIn('id', $newSeatIds)->update([
            'status' => $this->record->payment_status === 'paid' ? 'booked' : 'reserved',
        ]);

    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $data["seats"] = $this->record->seats()->pluck('seats.id')->toArray();

        $busHandlingTrip = Trips::where('id', $this->record->trip_id)->first();
        $data['bus'] = $busHandlingTrip->bus_id;
        $data['seatsnumber'] = Seats::where('bus_id', $busHandlingTrip->bus_id)
            ->where('status', 'available')
            ->whereNotIn('id', $data['seats'])->count();

        return $data;
    }
    protected function getRedirectUrl(): string|null
    {
        return $this->getResource()::geturl('view', ['record' => $this->record]);
    }

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
