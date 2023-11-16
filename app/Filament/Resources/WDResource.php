<?php

namespace App\Filament\Resources;

use App\Models\WD;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\WDResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WDResource\RelationManagers;

class WDResource extends Resource
{
    protected static ?string $model = WD::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Wakil Dekan I';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-wd1';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Hidden::make('role_id')
                        ->default(5),

                    TextInput::make('username')
                        ->placeholder('Username')
                        ->required()
                        ->unique(ignorable: fn ($record) => $record)
                        ->columnSpan(2),
                    TextInput::make('name')
                        ->placeholder('Masukkan nama lengkap')
                        ->required(),
                    TextInput::make('email')
                        ->email()
                        ->unique(ignorable: fn ($record) => $record)
                        ->placeholder('email@example.com')
                        ->required(),

                    // Select::make('program_studi_id')
                    //     ->relationship('programStudi', 'name'),
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
                    FileUpload::make('tandatangan')
                        ->image()
                        ->label('Tanda Tangan (Background Image Transparent PNG & Max. 2 MB)')
                        ->directory('ttd')
                        ->columnSpan(2)

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
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                ImageColumn::make('tandatangan')
                    ->toggleable(),
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
            'index' => Pages\ListWDS::route('/'),
            'create' => Pages\CreateWD::route('/create'),
            // 'edit' => Pages\EditWD::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role_id', 5);
    }
}
