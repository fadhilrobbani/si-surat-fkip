<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'username';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('role_id')
                    ->relationship('role', 'name')->required(),
                TextInput::make('username')->placeholder('NPM atau Username')->required(),
                TextInput::make('name')->placeholder('Masukkan nama lengkap')->required(),
                TextInput::make('email')->email()->placeholder('email@example.com')->required(),
                Select::make('program_studi_id')
                    ->relationship('programStudi', 'name'),
                Select::make('jurusan_id')
                    ->relationship('jurusan', 'name'),
                TextInput::make('password')->password()->placeholder('********')->confirmed()->required(),
                TextInput::make('password_confirmation')->placeholder('********')->password()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('programStudi.name')->searchable()->sortable(),
                TextColumn::make('jurusan.name')->searchable()->sortable(),
                TextColumn::make('role.name')->searchable()->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role','name'),
                    SelectFilter::make('programStudi')
                    ->relationship('programStudi','name'),
                    SelectFilter::make('jurusan')
                    ->relationship('jurusan','name')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }



    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()->where('users','=',1);
    // }
}
