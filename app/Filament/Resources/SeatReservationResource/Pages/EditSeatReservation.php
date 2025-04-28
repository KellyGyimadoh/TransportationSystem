<?php

namespace App\Filament\Resources\SeatReservationResource\Pages;

use App\Filament\Resources\SeatReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeatReservation extends EditRecord
{
    protected static string $resource = SeatReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
