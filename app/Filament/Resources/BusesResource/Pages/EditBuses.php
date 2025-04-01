<?php

namespace App\Filament\Resources\BusesResource\Pages;

use App\Filament\Resources\BusesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBuses extends EditRecord
{
    protected static string $resource = BusesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
