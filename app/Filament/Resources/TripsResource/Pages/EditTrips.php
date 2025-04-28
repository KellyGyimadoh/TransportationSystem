<?php

namespace App\Filament\Resources\TripsResource\Pages;

use App\Filament\Resources\TripsResource;
use App\Models\Bookings;
use App\Models\SeatReservation;
use App\Models\Tickets;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTrips extends EditRecord
{
    protected static string $resource = TripsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(){
        if($this->record->status==='completed'){
            $this->updateTickets();
        }
        
    }

    protected function updateTickets(){
        //$tickets= Tickets::where('status','valid')->get();
        $bookings=Bookings::where('trip_id',$this->record->id)
        ->where('payment_status','paid')->pluck('id');
        Tickets::whereIn('booking_id',$bookings)
        ->where('status','valid')->update(['status'=>'used']);
        SeatReservation::whereIn('booking_id',$bookings)
        ->where('status','reserved')->update(['status'=>'completed']);
      
    }
}
