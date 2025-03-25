<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AkademikFakultas;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AkademikFakultasResource\Pages;
use App\Filament\Resources\AkademikFakultasResource\RelationManagers;

class AkademikFakultasResource extends Resource
{




    protected static ?string $model = AkademikFakultas::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Akademik Fakultas';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-akademik-fakultas';
    protected static ?int $navigationSort = 16;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Hidden::make('role_id')
                        ->default(16),

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

                    // Select::make('program_studi_id')
                    //     ->relationship('programStudi', 'name'),
                    // Select::make('jurusan_id')
                    //     ->required()
                    //     ->relationship('jurusan', 'name'),

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
                    // FileUpload::make('tandatangan')
                    //     ->image()
                    //     ->label('Stempel (Background Image Transparent PNG & Max. 2 MB)')
                    //     ->directory('stempel')
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
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                // TextColumn::make('jurusan.name')
                //     ->searchable()
                //     ->toggleable()
                //     ->sortable(),
                // ImageColumn::make('tandatangan')
                //     ->label('Stempel')
                //     ->toggleable(),
                TextColumn::make('created_at')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('programStudi')
                    ->relationship('programStudi', 'name'),
                // SelectFilter::make('jurusan')
                //     ->relationship('jurusan', 'name'),
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAkademikFakultas::route('/'),
            'create' => Pages\CreateAkademikFakultas::route('/create'),
            // 'edit' => Pages\EditAkademikFakultas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = User::where('role_id', 16);

        return $query;
    }
}
