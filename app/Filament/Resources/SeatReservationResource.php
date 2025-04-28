<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeatReservationResource\Pages;
use App\Filament\Resources\SeatReservationResource\RelationManagers;
use App\Models\SeatReservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeatReservationResource extends Resource
{
    protected static ?string $model = SeatReservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking.id')->label('Booking Ref'),
                TextColumn::make('user.name')->label('Booked By'),
                TextColumn::make('trip.routes.start_location')->label('Trip Start'),
                TextColumn::make('trip.routes.end_location')->label('Trip End'),
                TextColumn::make('seat.seat_number')->label('Seat Number'),
                TextColumn::make('status')->label('Status')->badge()->color(
                    fn(string $state) => match ($state) {
                        'cancelled' => 'danger',
                        'completed' => 'success',
                        'reserved' => 'info',
                        'booked'=>Color::Lime,
                    }
                )->searchable(),
                TextColumn::make('booking_date')->label('Booking Date')->date(),

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
            'index' => Pages\ListSeatReservations::route('/'),
            'create' => Pages\CreateSeatReservation::route('/create'),
            'edit' => Pages\EditSeatReservation::route('/{record}/edit'),
        ];
    }
}
