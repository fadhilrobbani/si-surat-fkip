<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Jurusan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\JurusanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JurusanResource\RelationManagers;
use App\Filament\Resources\JurusanResource\Widgets\WarningAction;

class JurusanResource extends Resource
{
    protected static ?string $model = Jurusan::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Jurusan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Fakultas';
    protected static ?string $slug = 'jurusan';
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()->schema(
                [
                    TextInput::make('name')
                        ->label('Nama Jurusan')
                        ->placeholder('Contoh: Jurusan Ilmu Pengetahuan Akhirat')
                        ->required(),

                ]
            )->columnSpan(2)

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')
                ->label('Nama Jurusan')
                ->searchable()
                ->sortable()
                ->toggleable(),
        ])
        ->filters([
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();

    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJurusans::route('/'),
            // 'create' => Pages\CreateJurusan::route('/create'),
            // 'edit' => Pages\EditJurusan::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            WarningAction::class,
        ];
    }
}
