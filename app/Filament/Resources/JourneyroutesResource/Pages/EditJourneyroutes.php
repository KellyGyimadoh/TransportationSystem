<?php

namespace App\Filament\Resources\JourneyroutesResource\Pages;

use App\Filament\Resources\JourneyroutesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJourneyroutes extends EditRecord
{
    protected static string $resource = JourneyroutesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
