<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Surat;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SuratResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratResource\RelationManagers;

class SuratResource extends Resource
{

    protected static ?string $model = Surat::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Surat';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Developer Tools';
    protected static ?string $slug = 'surat';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Surat')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('jenisSurat.name')
                    ->label('Jenis Surat')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('jenisSurat.user_type')
                    ->label('Tipe Surat')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tanggal diajukan')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
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
            'index' => Pages\ListSurats::route('/'),
            'create' => Pages\CreateSurat::route('/create'),
            'edit' => Pages\EditSurat::route('/{record}/edit'),
        ];
    }
}
