<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusesResource\Pages;
use App\Filament\Resources\BusesResource\RelationManagers;
use App\Models\Buses;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class BusesResource extends Resource
{
    protected static ?string $model = Buses::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                TextInput::make('model')->label('Model'),
                TextInput::make('plate_number')->label('Number Plate'),
                TextInput::make('capacity')->label('Capacity')->numeric(),
                Select::make('driver_id')->label('Driver')
                ->options(User::all()->pluck('name','id')),
                Select::make('status')->label('Status')->options([
                    'maintenance'=>'Maintenance',
                    'active'=>'Active',
                    'inactive'=>'Inactive',
                ]),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_number')->label('No')->rowIndex(),
                TextColumn::make('model')->label('Model'),
                TextColumn::make('plate_number')->label('Number Plate'),
                TextColumn::make('capacity')->label('Capacity'),
                TextColumn::make('status')->label('Status') ->badge()
                ->color(fn (string $state): string => match ($state) {
        
                    'maintenance' => 'warning',
                    'active' => 'success',
                    'inactive' => 'danger',
                }),
                TextColumn::make('driver.name')->label('Driver')
               
            ])
            ->filters([
                //
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
            'index' => Pages\ListBuses::route('/'),
            'create' => Pages\CreateBuses::route('/create'),
            'edit' => Pages\EditBuses::route('/{record}/edit'),
            'view'=> Pages\ViewBuses::route('/{record}'),
        ];
    }
}
