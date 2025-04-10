<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->label('User Name'),
                TextInput::make('email')->required()->label('Email'),
                TextInput::make('password')->required()->label('Password'),
                TextInput::make('password_confirmation')->required()->label('Confirm Password'),
                TextInput::make('phone')->nullable()->label('Phone')->numeric()
                ->rules(rules: function(){
                    return ['digits:10',
                        Rule::unique('users','phone')->ignore('id'),
                ];
                }),
                Select::make('roles.name')->options(Role::all()->pluck('name','id'))
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('User Name')->searchable(),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('phone')->label('Phone')->searchable(),
                TextColumn::make('roles.name')->label('Account Type')->badge()
                ->color(fn($state)=>match($state){
                    'admin'=>'success',
                    'walk_in'=>'warning',
                    'online_customer'=>'info'
                }),
            ])
            ->filters([
                SelectFilter::make('user_roles')->relationship('roles','name')
                ->getOptionLabelFromRecordUsing(fn(Model $record) =>match($record->name){
                    'admin'=>'Admin',
                    'walk_in'=>'Walk In Customer'
                })
                ->label('Account Type')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
