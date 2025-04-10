<?php

namespace App\Filament\Resources\JourneyroutesResource\Pages;

use App\Filament\Resources\JourneyroutesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJourneyroutes extends ListRecords
{
    protected static string $resource = JourneyroutesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
