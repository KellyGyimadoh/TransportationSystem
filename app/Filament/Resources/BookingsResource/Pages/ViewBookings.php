<?php

namespace App\Filament\Resources\BookingsResource\Pages;

use App\Filament\Resources\BookingsResource;
use App\Models\Trips;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookings extends ViewRecord
{
    protected static string $resource = BookingsResource::class;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data["seats"] =$this->record->seats()->pluck('seats.id')->toArray();
        
        $busHandlingTrip = Trips::where('id', $this->record->trip_id)->first();
        $data['bus']= $busHandlingTrip->bus_id;
        
        return $data;
    }
}
