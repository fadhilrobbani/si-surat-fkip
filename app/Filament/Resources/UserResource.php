<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
// use Filament\Resources\Components\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Semua Akun';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'semua-akun';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Select::make('role_id')
                    ->relationship('role', 'name')
                    ->required(),
                TextInput::make('username')
                    ->placeholder('NPM atau Username')
                    ->required(),
                TextInput::make('name')
                    ->placeholder('Masukkan nama lengkap')
                    ->required(),
                TextInput::make('email')->email()
                    ->placeholder('email@example.com')
                    ->required(),
                Select::make('program_studi_id')
                    ->relationship('programStudi', 'name'),
                Select::make('jurusan_id')
                    ->relationship('jurusan', 'name'),
                TextInput::make('password')
                    ->password()
                    ->placeholder('********')
                    ->confirmed()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('password_confirmation')
                    ->placeholder('********')
                    ->password()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                // Toggle::make('email_verified_at')->label('Verifikasi Akun')

                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('username')
                    ->label('Username / NPM')
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
                TextColumn::make('jurusan.name')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('role.name')
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
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role', 'name'),
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

    // public static function getTabs(): array
    // {
    //     return [
    //         'admin' => Tab::make('Admin')
    //         ->modifyQueryUsing(fn (Builder $query) => $query->where('role_id', 1)),
    //         'mahasiswa' => Tab::make('Mahasiswa')
    //             ->modifyQueryUsing(fn (Builder $query) => $query->where('role_id', 2)),
    //         'staff' => Tab::make('Staff')
    //             ->modifyQueryUsing(fn (Builder $query) => $query->where('role_id', 3)),
    //     ];
    // }


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


    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'username', 'programStudi.name', 'jurusan.name', 'role.name', 'email'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();

    }



}
