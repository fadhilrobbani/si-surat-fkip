<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\StaffNilai;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StaffNilaiResource\Pages;
use App\Filament\Resources\StaffNilaiResource\RelationManagers;

class StaffNilaiResource extends Resource
{
    protected static ?string $model = StaffNilai::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Staff Nilai';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-staff-nilai';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Hidden::make('role_id')
                        ->default(7),
                    TextInput::make('username')
                        ->placeholder('Username')
                        ->required()
                        ->alphaDash()
                        ->unique(ignorable: fn ($record) => $record),
                    TextInput::make('email')
                        ->email()
                        ->unique(ignorable: fn ($record) => $record)
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
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create'),
                    TextInput::make('password_confirmation')
                        ->label('Konfirmasi kata sandi baru')
                        ->placeholder('********')
                        ->password()
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create'),
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
            'index' => Pages\ListStaffNilais::route('/'),
            'create' => Pages\CreateStaffNilai::route('/create'),
            // 'edit' => Pages\EditStaffNilai::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role_id', 7);
    }
}
