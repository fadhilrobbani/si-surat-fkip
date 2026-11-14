<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SsoClientResource\Pages;
use App\Models\OAuthClient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Laravel\Passport\Passport;

class SsoClientResource extends Resource
{
    protected static ?string $model = OAuthClient::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'SSO Client';

    protected static ?string $pluralModelLabel = 'SSO Clients';

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Developer Tools';

    protected static ?string $slug = 'sso-clients';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Aplikasi Klien')
                    ->description('Aplikasi yang diizinkan memakai SSO e-Surat sebagai identity provider.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Aplikasi')
                            ->placeholder('Contoh: Sistem Bimbingan Skripsi')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Ditampilkan pada halaman persetujuan (consent screen).')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('redirect')
                            ->label('Redirect URI')
                            ->placeholder('https://skripsi.fkip.unib.ac.id/auth/esurat/callback')
                            ->helperText('Harus cocok PERSIS dengan yang dikirim aplikasi klien. Satu URI per klien.')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('Kebijakan Akses')
                    ->description('Role e-Surat yang dicentang hanya boleh menjadi gerbang masuk. Otorisasi tetap milik aplikasi klien.')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Matikan untuk menonaktifkan seluruh akses aplikasi ini tanpa menghapus klien.')
                            ->default(true),

                        Forms\Components\Toggle::make('skip_consent')
                            ->label('Lewati layar persetujuan')
                            ->helperText('Aktifkan hanya untuk aplikasi resmi FKIP. Pengguna langsung kembali ke aplikasi setelah login tanpa layar "Izinkan akses".')
                            ->default(false),

                        Forms\Components\Toggle::make('require_email_verified')
                            ->label('Wajib email terverifikasi')
                            ->helperText('Jika aktif, akun tanpa verifikasi email ditolak saat authorize.')
                            ->default(false),

                        Forms\Components\Select::make('roles')
                            ->label('Role yang diizinkan')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Kosong = tidak ada yang boleh masuk (deny by default).')
                            ->columnSpanFull(),

                        Forms\Components\CheckboxList::make('scopes')
                            ->label('Scope yang diizinkan')
                            ->options(fn () => Passport::scopes()->pluck('description', 'id')->all())
                            ->default(['profile'])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Kredensial')
                    ->description('Secret hanya ditampilkan sekali saat dibuat atau di-regenerate. Simpan segera ke .env aplikasi klien.')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Client ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),

                        Forms\Components\Placeholder::make('grant_types_hint')
                            ->label('Grant type')
                            ->content('authorization_code + refresh_token (PKCE wajib)'),

                        Forms\Components\Toggle::make('revoked')
                            ->label('Cabut akses (revoke)')
                            ->helperText('Revoke akan mematikan klien dan seluruh token aktifnya, tetapi tidak menghapus data.')
                            ->default(false)
                            ->visibleOn('edit'),
                    ])->columns(2)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Aplikasi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('redirect')
                    ->label('Redirect URI')
                    ->limit(45)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role diizinkan')
                    ->badge()
                    ->separator(','),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\IconColumn::make('revoked')
                    ->label('Revoked')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSsoClients::route('/'),
            'create' => Pages\CreateSsoClient::route('/create'),
            'edit' => Pages\EditSsoClient::route('/{record}/edit'),
        ];
    }
}
