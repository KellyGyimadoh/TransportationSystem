<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JourneyroutesResource\Pages;
use App\Filament\Resources\JourneyroutesResource\RelationManagers;
use App\Models\JourneyRoutes;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JourneyroutesResource extends Resource
{
    protected static ?string $model = JourneyRoutes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('start_location')->label('Start Location')->required(),
                TextInput::make('end_location')->label('End Location')->required(),
                TextInput::make('distance')->label('Distance (Km)')->numeric(),
                TextInput::make('estimated_time')->label('Estimated time')->suffix('hr'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('start_location')->searchable(),
                TextColumn::make('end_location')->searchable(),
                TextColumn::make('distance')->label('Distance ')->suffix('km'),
                TextColumn::make('estimated_time')->label('Estimated Time (/hr)')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJourneyroutes::route('/'),
            'create' => Pages\CreateJourneyroutes::route('/create'),
            'edit' => Pages\EditJourneyroutes::route('/{record}/edit'),
        ];
    }
}
