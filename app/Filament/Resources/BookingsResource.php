<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingsResource\Pages;
use App\Filament\Resources\BookingsResource\Pages\ViewBookings;
use App\Filament\Resources\BookingsResource\RelationManagers;
use App\Models\Bookings;
use App\Models\Buses;
use App\Models\Seats;
use App\Models\Trips;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class BookingsResource extends Resource
{
    protected static ?string $model = Bookings::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                TextInput::make('user_name')
                ->label('Customer Name')->reactive()
                
               ,
                
               TextInput::make('phone')
               ->label('Phone Number')
               ->reactive()
               ->debounce(1000) // Waits 1 second after user stops typing
               ->afterStateUpdated(function ($set, $state, $get) {
                   // Block creation if phone number is still incomplete or blank
                   if (blank($state) || strlen($state) != 10  ) return;
           
                   $existingUser = User::where('phone', $state)->first();
             
                   if ($existingUser) {
                       $set('user_id', $existingUser->id);
                       $set('user_name', $existingUser->name);
                       $set('phone','');
                       
    Notification::make()
    ->title('Customer Found')
    ->body("Existing customer details filled.")
    ->success()
    ->duration(3000)
    ->send();
                   } else {
                       $name = $get('user_name');
           
                       if ($name) {
                           $newUser = User::firstOrCreate([
                               'name' => $name,
                               'phone' => $state,
                               'email' => uniqid() . '@walkin.local',
                               'password' => bcrypt(Str::random(20)),
                           ]);
           
                           if (!Role::where('name', 'walk_in')->exists()) {
                               Role::create(['name' => 'walk_in']);
                           }
           
                           $newUser->assignRole('walk_in');
           
                           $set('user_id', $newUser->id);
                       } else {
                           $set('user_id', null);
                       }
                   }
               })
               ->rules(function (callable $get) {
                return blank($get('user_id')) ? [
                    'nullable',
                    'string',
                    'digits:10',
                    Rule::unique('users', 'phone'),
                ] : [];
            }),
            
           
               
                   Select::make('user_id')->options(User::all()
                   ->pluck('name', 'id')->toArray())->placeholder('Select Customer Name')
                   ->label('Customer Name')->required()
                   ->searchable(),


                Select::make('trip_id')->relationship('trip')
                    ->getOptionLabelFromRecordUsing(fn(Model $record) =>
                        ($record->routes->start_location . ' To ' . $record->routes->end_location .
                            ' @ ' . date('H:i a', strtotime($record->departure_time))))
                    ->searchLabels()->reactive()->afterStateUpdated(
                        function ($set, $state) {
                            if ($state) {
                                $busHandlingTrip = Trips::where('id', $state)->first();

                                $seats=Seats::where('bus_id', $busHandlingTrip->bus_id)
                                ->where('status','available')->count();
                                $set('bus', $busHandlingTrip->bus_id);
                                $set('seatsnumber',$seats);
                            }

                        }

                    ),
                Select::make('status')
                    ->options(
                        [
                            'confirmed' => 'Confirmed',
                            'pending' => 'Pending',
                            'canceled' => 'Canceled'
                        ]
                    )
                    ->default('pending'),
                Select::make('payment_status')->options(['paid' => 'Paid', 'unpaid' => 'Unpaid'])
                    ->default('unpaid'),
                DatePicker::make('trip_date')->label('Trip Date')->default(now()),
                Placeholder::make('seatsnumber')->label('Seats Available')->content(fn($get)
                =>$get('seatsnumber') == 0 ? 'No Seats Available' : $get('seatsnumber') ),
                BelongsToManyMultiSelect::make('seats')->multiple()
                    ->relationship(
                        'seats',
                        'seat_number',
                        function ($query, $get) {
                            $busid = $get('bus');
                           
                            $currentBookingId = request()->route('record') ?? $get('id');
                            $booking = $currentBookingId ? Bookings::findOrFail($currentBookingId) : null;
                            
                            $selectedSeatIds= $booking?->seats()->pluck('seats.id')->toArray()??[];
                            $query->where('bus_id', $busid)
                            ->where(function($query) use ($selectedSeatIds) {
                                $query->where('status', 'available')
                                ->orWhereIn('seats.id', $selectedSeatIds);
                            });

                        }
                    )->label('Select Seats')
                    ->searchable()->preload()
                    //  ->disabled(fn(Get $get) => (int)$get('seatsnumber') == 0)
                       ->rules(fn(Get $get) => function ($attributes, $value, $fail) use ($get) {
                        $busid = $get('bus');
                        $seatsnumber=$get('seatsnumber');
                        
                        if (!$busid) {
                            $fail('Please select a trip');
                            return;
                        }
                     if($seatsnumber=0){
                        $fail('No Seats Available');
                        return;
                     }
                     if (empty($value)) {
                        $fail('You must select at least one seat');
                        return;
                    }

                    
                    })

            ])


        ;

    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Customer Name'),
                TextColumn::make('trip.routes.start_location')->label(' Start Location'),
                TextColumn::make('trip.routes.end_location')->label('End Location'),
                TextColumn::make('trip.departure_time')->label('Departure Time')->date('h:i A'),
                TextColumn::make('seats_count')->label('Total Seats Booked')
                ->counts('seats'),
               
                TextColumn::make('seats.seat_number')->label('Seats Booked')->badge()
                ->separator(','),
                TextColumn::make('status')->label('Status')->badge()->color(
                    fn(string $state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'canceled' => 'danger'
                    }
                )->searchable(),
                TextColumn::make('payment_status')->label('Payment Status')->badge()->color(
                    fn(string $state) => match ($state) {

                        'paid' => 'success',
                        'unpaid' => 'danger'
                    }
                )->searchable(),
            ])

            ->filters([
                SelectFilter::make('status')->multiple()->options([ 'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'canceled' => 'Canceled']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBookings::route('/create'),
            'edit' => Pages\EditBookings::route('/{record}/edit'),
            'view'=>ViewBookings::route('/{record}'),
        ];
    }

    // public static function deleteRecord($record): void
    // {
    //     // First, call the parent deleteRecord to delete the booking
    //     parent::deleteRecord($record);

    //     // Now update the seat statuses to 'available'
    //     $record->seats()->update(['status' => 'available']);
    // }
}
