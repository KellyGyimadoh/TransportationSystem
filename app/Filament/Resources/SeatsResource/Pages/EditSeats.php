<?php

namespace App\Filament\Resources\SeatsResource\Pages;

use App\Filament\Resources\SeatsResource;
use App\Models\Buses;
use App\Models\Seats;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeats extends EditRecord
{
    protected static string $resource = SeatsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array{
        if (!empty($data["bus_id"])) {
            $data["bus_capacity"] = Buses::where('id', $data['bus_id'])->value('capacity');
            $data['seat_assigned'] = Seats::where('bus_id', $data['bus_id'])->count();
        }
        return $data;
    }
    protected function getRedirectUrl(): string|null{
        return $this->getResource()::geturl('view', ['record'=>$this->record]);
    }
}
