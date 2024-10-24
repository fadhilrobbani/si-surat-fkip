<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\StaffWD1;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StaffWD1Resource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StaffWD1Resource\RelationManagers;

class StaffWD1Resource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = StaffWD1::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Staff Wakil Dekan 1';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-staff-wd1';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Hidden::make('role_id')
                        ->default(11),
                    TextInput::make('username')
                        ->placeholder('Username')
                        ->required()
                        ->alphaDash()
                        ->unique(ignorable: fn($record) => $record),
                    TextInput::make('email')
                        ->email()
                        ->unique(ignorable: fn($record) => $record)
                        ->placeholder('email@example.com')
                        ->required(),
                    TextInput::make('name')
                        ->label('Nama')
                        ->placeholder('Masukkan nama lengkap')
                        ->required(),


                    // Select::make('program_studi_id')
                    //     ->relationship('programStudi', 'name'),
                    // Select::make('jurusan_id')
                    //     ->relationship('jurusan', 'name'),

                    TextInput::make('password')->password()
                        ->label('Kata sandi baru')
                        ->placeholder('********')
                        ->confirmed()
                        ->dehydrated(fn(?string $state): bool => filled($state))
                        ->required(fn(string $operation): bool => $operation === 'create'),
                    TextInput::make('password_confirmation')
                        ->label('Konfirmasi kata sandi baru')
                        ->placeholder('********')
                        ->password()
                        ->dehydrated(fn(?string $state): bool => filled($state))
                        ->required(fn(string $operation): bool => $operation === 'create'),
                    // FileUpload::make('tandatangan')
                    //     ->image()
                    //     ->label('Tanda Tangan (Background Image Transparent PNG & Max. 2 MB)')
                    //     ->directory('ttd')
                    //     ->columnSpan(2)

                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStaffWD1s::route('/'),
            'create' => Pages\CreateStaffWD1::route('/create'),
            // 'edit' => Pages\EditStaffNilai::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role_id', 11);
    }
}
