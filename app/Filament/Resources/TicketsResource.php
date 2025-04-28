<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketsResource\Pages;
use App\Filament\Resources\TicketsResource\RelationManagers;
use App\Models\Bookings;
use App\Models\Tickets;
use App\Models\User;
use Date;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\select;

class TicketsResource extends Resource
{
    protected static ?string $model = Tickets::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('booking_id')
                    ->label('Booking ID')
                    ->options(
                        Bookings::where('payment_status', 'paid')
                            ->get()
                            ->mapWithKeys(fn ($booking) => [
                                $booking->id => "Booking #{$booking->id} - Trip: {$booking->trip_date}"
                            ])

                        // Bookings::where('payment_status', 'paid')
                        //     ->whereDoesntHave('tickets') // only if each booking should only get tickets once
                        //     ->get()
                        //     ->mapWithKeys(fn($booking) => [
                        //         $booking->id => "Booking #{$booking->id} - Trip: {$booking->trip_date}"
                        //     ])
                    )
                    ->reactive()
                    ->afterStateUpdated(function ($set, $state) {
                        $booking = Bookings::find($state);
                        if ($booking) {
                            $ticketNumber = uniqid('TT') . $state;
                            $set('ticket_number', $ticketNumber);
                            $set('issued_for', $booking->user_id);
                            $set('trip_date', $booking->trip_date);
                            $set('qr_code', $ticketNumber);

                        }
                    })
                    ->preload(),

                TextInput::make('ticket_number')->label('Ticket Number')->reactive()
                    ->afterStateUpdated(fn($set, $state) => $set('qr_code', $state)),

                TextInput::make('qr_code')->label('QR CODE')->reactive(),

                Select::make('seat_reservation_id')
                    ->label('Reserved Seats')

                    ->reactive()
                    ->options(function (callable $get) {
                        $bookingId = $get('booking_id');

                        if (!$bookingId)
                            return [];

                        $booking = Bookings::with('seatReservations.seat')->find($bookingId);

                        if (!$booking)
                            return [];

                        return $booking->seatReservations
                            ->where('status', 'reserved')
                            ->mapWithKeys(fn($reservation) => [
                                $reservation->id => "Seat # {$reservation->seat->seat_number}"
                            ])
                            ->toArray();
                    }),

                Select::make('issued_by')->label('Issued By')
                    ->relationship('issuedBy', 'name')
                    ->updateOptionUsing(fn($record) => $record->hasRole(['admin', 'ticket_seller']))
                    ->preload()->searchable(),

                Select::make('issued_for')->label('Customer Name')
                    ->relationship('issuedFor', 'name')

                    ->preload()->searchable(),

                Select::make('status')->label('Ticket Status')->options([
                    'valid' => 'Valid',
                    'used' => 'Used',
                    'expired' => 'Expired'
                ]),

                DatePicker::make('trip_date')->label('Trip Date')

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_id')->label('Booking Seats ID'),
                TextColumn::make('ticket_number')->label('Ticket Number'),
                TextColumn::make('qr_code')->label('Qr Code'),
                TextColumn::make('issuedBy.name')->label('Issued By'),
                TextColumn::make('issuedFor.name')->label('Customer Name'),
                TextColumn::make('trip_date')->label('Trip Date'),
                TextColumn::make('status')->label('Status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'valid' => 'success',
                        'used' => 'danger',
                        'expired' => 'info'
                    }),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTickets::route('/create'),
            'edit' => Pages\EditTickets::route('/{record}/edit'),
            'view' => Pages\ViewTickets::route('/{record}'),
        ];
    }
}
