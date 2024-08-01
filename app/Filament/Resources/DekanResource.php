<?php

namespace App\Filament\Resources;

use Pages\ListWDS;
use Filament\Forms;
use Filament\Tables;
use App\Models\Dekan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\DekanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DekanResource\Pages\ListDekans;
use App\Filament\Resources\DekanResource\RelationManagers;

class DekanResource extends Resource
{
    protected static ?string $model = Dekan::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Dekan';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-dekan';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Hidden::make('role_id')
                        ->default(8),
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
                    TextInput::make('nip')
                        ->label('NIP')
                        ->placeholder('NIP')
                        ->unique(ignorable: fn ($record) => $record)
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
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                // ImageColumn::make('tandatangan')
                //     ->toggleable(),
                TextColumn::make('created_at')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label('Terverifikasi')
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
                    }),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),

                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }

    // public static function canCreate(): bool
    // {
    //     return false;
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
            'index' => ListDekans::route('/'),
            // 'create' => Pages\CreateWD::route('/create'),
            // 'edit' => Pages\EditWD::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role_id', 8);
    }
}
