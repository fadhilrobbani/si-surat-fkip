<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Email;
use App\Models\EmailLog;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StatistikEmail extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Developer Tools';

    protected static ?string $title = 'Statistik Email (SMTP)';

    protected static ?string $navigationLabel = 'Statistik Email';

    protected static ?string $slug = 'statistik-email';

    protected static string $view = 'filament.pages.statistik-email';

    protected function getHeaderWidgets(): array
    {
        return [
            Email\EmailStatsWidget::class,
            Email\EmailHarianChart::class,
            Email\EmailKategoriChart::class,
            Email\EmailAsalChart::class,
            Email\EmailPerJamChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(EmailLog::query()->with(['user', 'role']))
            ->heading('Log email')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => EmailLog::STATUS[$state] ?? $state)
                    ->color(fn ($state) => match ($state) { 'terkirim' => 'success', 'gagal' => 'danger', default => 'info' }),
                Tables\Columns\TextColumn::make('kategori')->formatStateUsing(fn ($state) => EmailLog::KATEGORI[$state] ?? $state),
                Tables\Columns\TextColumn::make('subjek')->limit(40)->searchable(),
                Tables\Columns\TextColumn::make('penerima')->limit(30)->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Dipicu oleh')->placeholder('Tamu'),
                Tables\Columns\TextColumn::make('role.description')->label('Role')->placeholder('-'),
                Tables\Columns\TextColumn::make('route')->label('Halaman/aksi')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(EmailLog::STATUS),
                Tables\Filters\SelectFilter::make('kategori')->options(EmailLog::KATEGORI),
                Tables\Filters\Filter::make('tanggal')
                    ->form([\Filament\Forms\Components\DatePicker::make('tanggal')->label('Tanggal')])
                    ->query(fn (Builder $q, array $data) => $q->when($data['tanggal'] ?? null, fn ($q, $t) => $q->whereDate('created_at', $t))),
            ]);
    }
}
