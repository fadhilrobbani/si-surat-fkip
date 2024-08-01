<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\JenisSurat;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JenisSuratResource\Pages;
use App\Filament\Resources\JenisSuratResource\RelationManagers;
use App\Filament\Resources\JenisSuratResource\Widgets\WarningAction;

class JenisSuratResource extends Resource
{
    protected static ?string $model = JenisSurat::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Jenis Surat';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Developer Tools';
    protected static ?string $slug = 'jenis-surat';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema(
                    [
                        TextInput::make('slug')
                            ->label('Slug')
                            ->placeholder('Contoh: surat-keterangan-contoh')
                            ->rules(['alpha_dash'])
                            ->disabledOn('edit')
                            ->required(),
                        Hidden::make('slug')
                            ->label('Slug')
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama Surat')
                            ->placeholder('Surat Keterangan Contoh')
                            ->required(),
                        // ->required(function (Set $set, $state) {
                        //     $newSlug = Str::slug($state);
                        //     $set('slug', $newSlug); // Set the `title` field to `Blog Post`.
                        //     //...
                        // }),
                        // ->disabledOn('edit'),
                        Select::make('user_type')
                            ->label('Tipe Pengguna')
                            ->required()
                            ->options(['mahasiswa' => 'mahasiswa', 'staff' => 'staff']),

                    ]
                )->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Nama Surat')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user_type')
                    ->label('Tipe Pengguna')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
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
            'index' => Pages\ListJenisSurats::route('/'),
            // 'create' => Pages\CreateJenisSurat::route('/create'),
            // 'edit' => Pages\EditJenisSurat::route('/{record}/edit'),
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
