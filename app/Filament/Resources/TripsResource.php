<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TripsResource\Pages;
use App\Filament\Resources\TripsResource\RelationManagers;
use App\Models\Bookings;
use App\Models\Buses;
use App\Models\JourneyRoutes;
use App\Models\SeatReservation;
use App\Models\Tickets;
use App\Models\Trips;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class TripsResource extends Resource
{
    protected static ?string $model = Trips::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('bus_id')->options(Buses::where('status','active')
                ->pluck('model','id'))->label('Bus Name'),
                Select::make('route_id')->relationship('routes',)
                ->getOptionLabelFromRecordUsing(fn(Model $record)=>
                ucfirst($record->start_location)." To ".  ucfirst($record->end_location)),
                TimePicker::make("departure_time")->label('Departure Time'),
                TimePicker::make("arrival_time")->label('Arrival Time'),
                TextInput::make('price')->label('Price')->prefix('GHS')
                ->numeric()->inputMode('decimal'),
                Select::make('status')->label('Status')
                ->options(['scheduled'=>'Scheduled',
                                'ongoing'=>'Ongoing',
                                'completed'=>'Completed',
                                'canceled'=>'Canceled'])->default('scheduled'),

            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bus.model')->label('Bus Name'),
                TextColumn::make('routes.start_location')->label('Route Start Location'),
                TextColumn::make('routes.end_location')->label('Route End Location'),
               
                TextColumn::make('departure_time')->label('Departure') ->date('h:i A'),
                TextColumn::make('arrival_time')->label('Arrival') ->date('h:i A'),
                TextColumn::make('price')->label('Price (GHS)')->prefix('GHS '),
                TextColumn::make('status')->label('Status')->badge()->color(
                    fn(string $state) => match ($state) {
                        'scheduled' => 'warning',
                        'ongoing' => 'info',
                        'completed' => 'success',
                        'canceled'=>'danger'
                    }
                )->searchable(),

              

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('rescheduleTrip')->label('Reschedule Trip')
                ->icon('heroicon-o-check-circle')->requiresConfirmation()
                ->action(function($record){
                    if($record->status !=='scheduled'){
                        $record->status='scheduled';
                        $record->save();
                    }
                    Notification::make()
                    ->title('Trip Rescheduled.')
                    ->success()
                    ->send();
                
                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('completeTrip')
                ->label('Complete Trip')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->action(function ($record) {
                    if ($record->status !== 'completed') {
                        $record->status = 'completed';
                        $record->save();
                    }
            
                    $bookingIds = Bookings::where('trip_id', $record->id)
                        ->where('payment_status', 'paid')
                        ->pluck('id');
            
                    Tickets::whereIn('booking_id', $bookingIds)
                        ->where('status', 'valid')
                        ->update(['status' => 'used']);
                        SeatReservation::whereIn('booking_id',$bookingIds)
                        ->where('status','reserved')->update(['status'=>'completed']);
                      
                    Notification::make()
                        ->title('Trip marked as completed and tickets updated.')
                        ->success()
                        ->send();
                    })
             
                ],position: ActionsPosition::BeforeCells) 
  
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    

                ]),
                Tables\Actions\BulkAction::make('completeTrip')
                    ->label('Complete Trip')->requiresConfirmation()
                    ->action(function(Collection $records){
                        $records->each->update(['status'=>'completed']);
                        $records->each->bookings()->tickets()
                        ->where('status','valid')->update(['status' => 'used']);
                        // $bookingIds = Bookings::where('trip_id', $record->id)
                        // ->where('payment_status', 'paid')
                        // ->pluck('id');
            
                    // Tickets::whereIn('booking_id', $bookingIds)
                    //     ->where('status', 'valid')
                    //     ->update(['status' => 'used']);
                    $records->each->bookings()->seatReservations()
                        ->where('status','reserved')->update(['status' => 'completed']);
                        // SeatReservation::whereIn('booking_id',$bookingIds)
                        // ->where('status','reserved')->update(['status'=>'completed']);
                      
                    }),
                    Tables\Actions\BulkAction::make('rescheduleTrip')
                    ->label('Reschedule Trip')->requiresConfirmation()
                    ->action(function(Collection $records){
                        $records->each->update(['status'=>'scheduled']);
                    })
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
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrips::route('/create'),
            'edit' => Pages\EditTrips::route('/{record}/edit'),
        ];
    }
}
