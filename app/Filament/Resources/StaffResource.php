<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Staff;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\StaffResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StaffResource\RelationManagers;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Staff';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-staff';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('role_id')
                    ->default(3)
                    ->relationship('role', 'name')
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('username')
                    ->placeholder('Username')
                    ->required(),
                TextInput::make('name')
                    ->placeholder('Masukkan nama lengkap')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->placeholder('email@example.com')
                    ->required(),
                Select::make('program_studi_id')
                    ->relationship('programStudi', 'name'),
                // Select::make('jurusan_id')
                //     ->relationship('jurusan', 'name'),

                TextInput::make('password')->password()
                    ->placeholder('********')
                    ->confirmed()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('password_confirmation')
                    ->placeholder('********')
                    ->password()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),

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
            TextColumn::make('name')
                ->searchable()
                ->toggleable()
                ->sortable(),
            TextColumn::make('email')
                ->searchable()
                ->toggleable()
                ->sortable(),
            TextColumn::make('programStudi.name')
                ->searchable()
                ->toggleable()
                ->sortable(),
            TextColumn::make('programStudi.jurusan.name')
                ->searchable()
                ->toggleable()
                ->sortable(),
            IconColumn::make('email_verified_at')
                ->label('Terverifikasi')
                ->boolean()
                ->trueIcon('heroicon-o-check-badge')
                ->falseIcon('heroicon-o-x-mark'),
            TextColumn::make('created_at')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->sortable(),
        ])
        ->filters([
            SelectFilter::make('programStudi')
                ->relationship('programStudi', 'name'),
            SelectFilter::make('jurusan')
                ->relationship('jurusan', 'name'),
            TernaryFilter::make('email_verified_at')
                ->nullable(),
            Filter::make('created_at')
                ->form([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
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
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            // 'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = User::where('role_id', 3);

        return $query;
    }
}
