<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingsResource\Pages;
use App\Filament\Resources\BookingsResource\Pages\ViewBookings;
use App\Filament\Resources\BookingsResource\RelationManagers;
use App\Models\Bookings;
use App\Models\Buses;
use App\Models\SeatReservation;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use function Filament\Support\format_money;

class BookingsResource extends Resource
{
    protected static ?string $model = Bookings::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('user_name')
                    ->label('Customer Name')->reactive() ->disabled(fn(Get $get) => 
                    filled($get('user_id')))

                ,

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->reactive()
                    ->debounce(1000) // Waits 1 second after user stops typing
                    ->afterStateUpdated(function ($set, $state, $get) {
                        // Block creation if phone number is still incomplete or blank
                        if (blank($state) || strlen($state) != 10)
                            return;

                        $existingUser = User::where('phone', $state)->first();

                        if ($existingUser) {
                            $set('user_id', $existingUser->id);
                            $set('user_name', $existingUser->name);
                            $set('phone', '');

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



                Select::make('user_id')->relationship('user','name')
                ->placeholder('Select Customer Name')
                    ->label('Customer Name')->required()
                    ->searchable()->preload(),


                Select::make('trip_id')->relationship('trip')
                    ->getOptionLabelFromRecordUsing(fn(Model $record) =>
                        ($record->routes->start_location . ' To ' . $record->routes->end_location .
                            ' @ ' . date('H:i a', strtotime($record->departure_time))))
                    ->searchLabels()
                    ->reactive()
                    ->afterStateUpdated(
                       
                       function ($set,$state){
                        $set('tripId',$state);
                        $trip=Trips::findOrFail($state);
                        $set('tripPrice',$trip->price);
                       }

                    )
                   ,
                Select::make('status')
                    ->options(
                        [
                            'confirmed' => 'Confirmed',
                            'pending' => 'Pending',
                            'canceled' => 'Canceled'
                        ]
                    )
                    ->default('pending') 
                    

                    ->rules(function($get,$state){
                        return function($attribute,$value,$fail) use ($get){
                            $paymentStatus= $get('payment_status');
                            if ($paymentStatus === 'unpaid' && $value === 'confirmed') {
                                $fail('Payment should be made before booking is confirmed.');
                            }
                    
                            if ($paymentStatus === 'paid' && $value === 'pending') {
                                $fail('Confirmed bookings should not be set back to pending if already paid.');
                            }
                    
                            if ($value === 'canceled' && $paymentStatus === 'paid') {
                                $fail('This paid booking cannot be canceled. Please contact an admin.');
                            }
                        };
                    }),
                Select::make('payment_status')->options(['paid' => 'Paid', 'unpaid' => 'Unpaid'])
                    ->default('unpaid'),
                DatePicker::make('trip_date')->label('Trip Date')
                
                ->rules(fn($get)=>function($attribute,$value,$fail) use ($get){
                    $selectedDate = Carbon::parse($value)->toDateString();
                    $tripId=$get('tripId') ?? null;
                    
                    $trip=Trips::findOrFail($tripId);
                    if (!$tripId || !($trip = Trips::find($tripId))) return;
                    $fullTripDateTime = Carbon::parse($selectedDate . ' ' . $trip->departure_time);
            
                    if ($selectedDate === today()->toDateString() && $fullTripDateTime->lt(now())) {
                       $fail('failed trip already ongoing');
                        
                    }
                })
                -> reactive()->afterStateUpdated(function($set,$state,$get){
                    $tripId=$get('tripId') ?? null;
                    $trip=Trips::findOrFail($tripId);
                    
                    // Fetch reserved seat IDs for the selected trip and date
                    $reservedSeatIds = SeatReservation::where('trip_id', $trip->id)
                        ->where('booking_date', $state)
                        ->whereIn('status', ['reserved', 'booked'])
                        ->pluck('seat_id');
            
                    // Count available seats by excluding reserved ones
                    $seatsAvailable = Seats::where('bus_id', $trip->bus_id)
                        ->whereNotIn('id', $reservedSeatIds)
                        ->count();
                        $set('seatsnumber',$seatsAvailable);
                        $set('selectedDate',$state);
                }),
                Placeholder::make('seatsnumber')->label('Seats Available')->content(fn($get)
                    => $get('seatsnumber') == 0 ? 'No Seats Available' : $get('seatsnumber')),
                   
                   
                    Select::make('seats')
                    ->label('Select Seats')
                    ->multiple()
                    ->options(function ($get) {
                        $tripId = $get('tripId');
                        $date = $get('selectedDate');
                    
                        if (!$tripId || !$date) return [];
                    
                        $trip = Trips::find($tripId);
                        if (!$trip) return [];
                    
                        $currentBookingId =  $get('bookingid') ?? null;
                    
                        $selectedSeatIds = [];
                        if ($currentBookingId) {
                            $selectedSeatIds = SeatReservation::where('booking_id', $currentBookingId)
                                ->pluck('seat_id')
                                ->toArray();
                        }
                    
                        $reserved = SeatReservation::where('trip_id', $tripId)
                            ->where('booking_date', $date)
                            ->whereIn('status', ['booked', 'reserved'])
                            ->pluck('seat_id')
                            ->toArray();
                    
                        $excludedSeats = collect($reserved)
                            ->diff($selectedSeatIds)
                            ->values()
                            ->toArray();
                    
                        return Seats::where('bus_id', $trip->bus_id)
                            ->whereNotIn('id', $excludedSeats)
                            ->pluck('seat_number', 'id')
                            ->toArray();
                    })
                   
                    
                    ->searchable()
                    ->preload()
                    ->getOptionLabelsUsing(function ($values) {
                        return Seats::whereIn('id', $values)
                            ->pluck('seat_number', 'id')
                            ->toArray();
                    })->reactive()->afterStateUpdated(function($set,$state,$get){
                        
                        $count = is_array($state) ? count($state) : 0;
                        $tripPrice = $get('tripPrice') ?? 0;
                        $set('seatsSelected', $count);
                        $set('total_price', $count * $tripPrice);
                    })
                
                    ->rules(function (callable $get) {
                        return function ($attribute, $value, $fail) use ($get) {
                            $tripId = $get('tripId');
                            if (!$tripId) {
                                $fail('Please select a trip.');
                            }
                            if (empty($value)) {
                                $fail('You must select at least one seat.');
                            }
                        };
                    }),
                TextInput::make('total_price')->label('Trip Total Price')
                ->readOnly()
                ->default(0)
                ->reactive(),

                Select::make('payment_method')->label('Payment Method')->options([
                    'mobile_money'=>'Mobile Money',
                    'cash'=>'Cash',
                    'card'=>'Card'
                ])->default('cash')
                ->disabled(fn($get)=>$get('payment_status')!=='paid')
               
                

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
                TextColumn::make('trip_date')
                ->label('Booking Date'),
                
                
                

                 TextColumn::make('seats')
                 ->label('Total Seats Booked')
                 ->getStateUsing(fn($record) =>  $record->seatReservations
                 ->whereIn('status',['reserved','booked'])->count()),
             

                    TextColumn::make('seatReservations')
                    ->label('Seats')->badge()->color('success')
                    ->getStateUsing(fn ($record) => $record->seatReservations
                    ->whereIn('status',['reserved','booked'])
                        ->pluck('seat.seat_number') // Assumes you have a seat() relationship in SeatReservation
                        ->join(', ')),
                TextColumn::make('status')->label('Status')->badge()->color(
                    fn(string $state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'canceled' => 'danger'
                        
                    }
                )->searchable(),
                TextColumn::make('trip')->label('GHS Amount')
                ->formatStateUsing(function($record){
                    return number_format($record->seatReservations
                    ->whereIn('status',['booked','reserved'])->count() * $record->trip->price,2);
                })->prefix('GHS'),
                TextColumn::make('payment_status')->label('Payment Status')->badge()->color(
                    fn(string $state) => match ($state) {

                        'paid' => 'success',
                        'unpaid' => 'danger'
                    }
                )->searchable(),
            ])

            ->filters([
                SelectFilter::make('status')->multiple()->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'canceled' => 'Canceled'
                ]),
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
            'view' => ViewBookings::route('/{record}'),
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
