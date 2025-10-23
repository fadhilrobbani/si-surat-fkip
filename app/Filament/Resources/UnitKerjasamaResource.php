<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitKerjasamaResource\Pages;
use App\Filament\Resources\UnitKerjasamaResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class UnitKerjasamaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Unit Kerjasama';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-unit-kerjasama';
    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Hidden::make('role_id')
                        ->default(20),

                    TextInput::make('username')
                        ->placeholder('Username')
                        ->alphaDash()
                        ->unique(ignorable: fn($record) => $record)
                        ->required(),
                    TextInput::make('email')
                        ->email()
                        ->unique(ignorable: fn($record) => $record)
                        ->placeholder('email@example.com')
                        ->required(),
                    TextInput::make('name')
                        ->label('Nama')
                        ->placeholder('Masukkan nama lengkap')
                        ->required(),

                    TextInput::make('nip')
                        ->label('NIP')
                        ->placeholder('Masukkan NIP (opsional)')
                        ->nullable(),

                    TextInput::make('password')->password()
                        ->placeholder('********')
                        ->label('Kata sandi baru')
                        ->confirmed()
                        ->dehydrated(fn(?string $state): bool => filled($state))
                        ->required(fn(string $operation): bool => $operation === 'create'),
                    TextInput::make('password_confirmation')
                        ->label('Konfirmasi kata sandi baru')
                        ->placeholder('********')
                        ->password()
                        ->dehydrated(fn(?string $state): bool => filled($state))
                        ->required(fn(string $operation): bool => $operation === 'create'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('role_id', 20))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Role')
                    ->sortable(),
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
            'index' => Pages\ListUnitKerjasamas::route('/'),
            'create' => Pages\CreateUnitKerjasama::route('/create'),
            'edit' => Pages\EditUnitKerjasama::route('/{record}/edit'),
        ];
    }    
}
