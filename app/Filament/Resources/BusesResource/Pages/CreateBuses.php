<?php

namespace App\Filament\Resources\BusesResource\Pages;

use App\Filament\Resources\BusesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBuses extends CreateRecord
{
    protected static string $resource = BusesResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
