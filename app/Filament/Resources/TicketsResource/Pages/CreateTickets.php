<?php

namespace App\Filament\Resources\TicketsResource\Pages;

use App\Filament\Resources\TicketsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTickets extends CreateRecord
{
    protected static string $resource = TicketsResource::class;

   protected function beforeCreate(){
   // dd($this->form->getState()['seat_reservation_id']);
   }
}
