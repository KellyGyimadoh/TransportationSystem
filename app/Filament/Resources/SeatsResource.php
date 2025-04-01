<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeatsResource\Pages;
use App\Filament\Resources\SeatsResource\Pages\ViewSeats;
use App\Filament\Resources\SeatsResource\RelationManagers;
use App\Models\Buses;
use App\Models\Seats;
use Closure;
use Filament\Forms;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SeatsResource extends Resource
{
    protected static ?string $model = Seats::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('bus_id')->label('Bus Name/Model')
                    ->options(Buses::pluck('model', 'id'))->required()->reactive()
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state) {
                            $busCapacity = Buses::where('id', $state)->value('capacity');
                            $seatAssigned = Seats::where('bus_id', $state)->count();

                            // Ensure values are correctly set
                            $set('bus_id', $state);
                            $set('bus_capacity', $busCapacity ?? 'Unknown');
                            $set('seat_assigned', $seatAssigned);
                        }
                    }),
                Placeholder::make('seat_assigned')->label('Seats Assigned')
                    ->content(fn($get) => ($get('seat_assigned')
                        ? $get('seat_assigned') . '/' . $get('bus_capacity') : "N/A")),

                Placeholder::make('bus_capacity')->label('Bus Capacity')
                    ->content(
                        content: fn($get): string =>
                        $get('bus_capacity') ? " {$get('bus_capacity')}" : "Select a Bus"
                    ),
                TextInput::make('seat_number')->label('Seat Number')->required()->unique(ignoreRecord: true)
                    ->rules([
                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $currentSeatId = request()->route('record')?->id ?? $get('id'); // Ensure we get the seat ID
                            $busId = $get('bus_id'); // Get selected bus
                            $busCapacity = $get('bus_capacity'); // Get max capacity
                

                            if (!$busId) {
                                $fail('Please select a bus.');
                                return;
                            }

                            // Fetch the latest seat count directly from the database
                            $seatCount = Seats::where('bus_id', $busId)
                                ->when($currentSeatId, fn($query) => $query->where('id', '!=', $currentSeatId))
                                ->count();

                            if ($seatCount >= (int) $busCapacity) {
                                $fail("Bus Max Capacity reached (Current: $seatCount / Max: $busCapacity)");
                            }
                        },
                    ]),
                Select::make('status')->label('Status')->options([
                    'available' => 'Available',
                    'booked' => 'Booked',
                    'reserved' => 'Reserved'
                ])->required(),
            ]);


    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bus.model')->label('Bus Name')
                ->formatStateUsing(fn($state)=>Str::title($state))
                ->searchable(true),
                TextColumn::make('seat_number')->label('Seat Number')
                ->formatStateUsing(fn($state)=>Str::ucfirst($state)),

                TextColumn::make('status')->label('Status')->badge()->color(
                    fn(string $state) => match ($state) {
                        'available' => 'warning',
                        'booked' => 'success',
                        'reserved' => 'info'
                    }
                )->searchable(),
               
            ])->defaultSort('created_at', 'desc')
            ->groups([
                Group::make('bus.model')->label('Bus Model'),
                Group::make('status')->label('Status')
            ])->defaultGroup('bus.model')
            ->filters([
                SelectFilter::make('status')
                ->multiple()
                ->options([
                    'available' => 'Available',
                    'booked' => 'Booked',
                    'reserved' => 'Reserved'
                ])
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
            'index' => Pages\ListSeats::route('/'),
            'create' => Pages\CreateSeats::route('/create'),
            'edit' => Pages\EditSeats::route('/{record}/edit'),
            'view' => ViewSeats::route('/{record}'),
        ];
    }

}
