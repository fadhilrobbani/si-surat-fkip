<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProgramStudi;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProgramStudiResource\Pages;
use App\Filament\Resources\ProgramStudiResource\RelationManagers;
use App\Filament\Resources\ProgramStudiResource\Widgets\ProgramStudiOverview;
use App\Filament\Resources\ProgramStudiResource\Widgets\WarningAction;

class ProgramStudiResource extends Resource
{
    protected static ?string $model = ProgramStudi::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Program Studi';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Fakultas';
    protected static ?string $slug = 'program-studi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema(
                    [
                        TextInput::make('name')
                            ->label('Nama Prodi')
                            ->placeholder('Contoh: S1 Sastra Inggris')
                            ->required(),
                        Select::make('jurusan_id')
                            ->relationship('jurusan', 'name')
                            ->required()
                    ]
                )->columnSpan(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Prodi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('jurusan.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('jurusan')
                    ->relationship('jurusan', 'name'),
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
            'index' => Pages\ListProgramStudis::route('/'),
            // 'create' => Pages\CreateProgramStudi::route('/create'),
            // 'edit' => Pages\EditProgramStudi::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            WarningAction::class,
        ];
    }
}
