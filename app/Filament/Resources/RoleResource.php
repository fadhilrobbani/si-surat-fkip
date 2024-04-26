<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Role;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Filament\Resources\RoleResource\Widgets\WarningAction;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;


    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Roles';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Developer Tools';
    protected static ?string $slug = 'roles';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->label('ID Role')
                    ->placeholder('(Dalam angka, contoh: 1)')
                    ->numeric()
                    ->disabledOn('edit'),
                TextInput::make('name')
                    ->label('Nama Role')
                    ->placeholder('Contoh: ddministrator')
                    ->disabledOn('edit')
                    ->required(),
                TextInput::make('description')
                    ->label('Deskripsi Role')
                    ->placeholder('Role ini digunakan untuk...')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Role')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Nama Role')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->label('Deskripsi role')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            // 'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getWidgets(): array
    {
        return [
            WarningAction::class,
        ];
    }
}
