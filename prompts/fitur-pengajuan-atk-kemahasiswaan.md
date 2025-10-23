# Fitur Pengajuan ATK untuk Kemahasiswaan

## Deskripsi

Menambahkan role baru "kemahasiswaan" dengan fitur pengajuan Surat ATK. Role ini mengikuti pola yang sama dengan role akademik dan akademik-fakultas, yaitu langsung ke Kabag (tanpa melalui Kaprodi seperti Staff).

## Tanggal Implementasi

23 Oktober 2025

## Database Changes

### 1. Migration

| File | Deskripsi | Perintah |
|------|-----------|----------|
| `database/migrations/2025_10_23_100000_add_kemahasiswaan_to_user_type_enum_in_jenis_surat_table.php` | Menambahkan "kemahasiswaan" ke dalam enum user_type di tabel jenis_surat_tables. | `php artisan migrate` |

### 2. Seeder

#### RoleSeeder (`database/seeders/RoleSeeder.php`)
```php
// Tambahkan ke array $roles:
['name' => 'kemahasiswaan', 'description' => 'Kemahasiswaan'],
```

#### UserSeeder (`database/seeders/UserSeeder.php`)
```php
// Tambahkan ke array $users:
[
    'username' => 'kemahasiswaan',
    'name' => 'Kemahasiswaan',
    'email' => 'kemahasiswaan@email.com',
    'password' => bcrypt('password'),
    'role_id' => 18,
    'nip' => null,
    'jurusan_id' => null,
    'program_studi_id' => null,
    'email_verified_at' => now()
]
```

**PERHATIAN:** Ubah logic dari `User::create()` menjadi `User::firstOrCreate()` untuk menghindari duplikasi:

```php
foreach ($users as $user) {
    User::firstOrCreate(
        ['username' => $user['username']],
        [
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $user['password'],
            'role_id' => $user['role_id'],
            'nip' => $user['nip'],
            'program_studi_id' => $user['program_studi_id'],
            'jurusan_id' => $user['jurusan_id'],
            'email_verified_at' => $user['email_verified_at']
        ]
    );
};
```

#### JenisSuratSeeder (`database/seeders/JenisSuratSeeder.php`)
```php
// Tambahkan ke array $daftarJenisSurat:
[
    'name' => 'Surat Pengajuan ATK',
    'slug' => 'surat-pengajuan-atk-kemahasiswaan',
    'user_type' => 'kemahasiswaan'
],
```

## Controllers

### 1. KemahasiswaanController (`app/Http/Controllers/KemahasiswaanController.php`)
- Copy dari `AkademikController.php`
- Ganti semua references dari 'akademik' menjadi 'kemahasiswaan'
- Role ID yang digunakan: 18
- Method utama: `pengajuanSurat()`, `riwayatPengajuanSurat()`, `showDetailPengajuanSuratByKemahasiswaan()`

### 2. Update SuratController (`app/Http/Controllers/SuratController.php`)

#### Tambahkan method baru:
```php
public function storeSuratPengajuanAtkByKemahasiswaan(Request $request, JenisSurat $jenisSurat)
{
    if ($jenisSurat->slug != 'surat-pengajuan-atk-kemahasiswaan') {
        return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
    }

    $request->validate([
        'name' => 'required',
        'username' => 'required',
        'email' => 'required|email',
        'pengajuan-atk' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
    ]);

    $surat = new Surat;
    $surat->pengaju_id = auth()->user()->id;
    $surat->current_user_id = $request->input('penerima'); // Langsung ke Kabag
    $surat->status = 'diproses';
    $surat->jenis_surat_id = $jenisSurat->id;
    $surat->expired_at = now()->addDays(30);

    $surat->data = [
        'nama' => $request->input('name'),
        'username' => $request->input('username'),
        'email' => $request->input('email'),
    ];

    $surat->files = [
        'pengajuanAtk' => $request->file('pengajuan-atk')->store('lampiran'),
    ];

    $surat->save();
    return redirect('/kemahasiswaan/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
}

public function storeByKemahasiswaan(Request $request, JenisSurat $jenisSurat)
{
    if ($jenisSurat->slug == 'surat-pengajuan-atk-kemahasiswaan') {
        return $this->storeSuratPengajuanAtkByKemahasiswaan($request, $jenisSurat);
    }

    return redirect()->back()->with('error', 'Jenis surat tidak tersedia');
}
```

#### Update method `create()`:
```php
if ($jenisSurat->slug == 'surat-pengajuan-atk-kemahasiswaan') {
    return view('kemahasiswaan.formsurat.form-surat-pengajuan-atk', [
        'jenisSurat' => $jenisSurat,
        'daftarProgramStudi' => ProgramStudi::all(),
        'daftarPenerima' => User::select('id', 'name', 'username')
            ->where('role_id', '=', 17) // Langsung ke Kabag
            ->orderBy('username', 'asc')
            ->get()
    ]);
}
```

### 3. Update AuthController (`app/Http/Controllers/AuthController.php`)

#### Method `authenticate()`:
```php
elseif (auth()->user()->role_id == 18) {
    return redirect('/kemahasiswaan')->with('success', 'Anda berhasil login');
}
```

#### Method `home()`:
```php
elseif (auth()->user()->role_id == 18) {
    return redirect('/kemahasiswaan');
}
```

## Routes (`routes/web.php`)

### Tambahkan route group untuk kemahasiswaan:
```php
Route::prefix('kemahasiswaan')->middleware(['userAccess:18'])->group(function () {
    // Surat masuk
    Route::get('/surat-masuk', [KemahasiswaanController::class, 'suratMasuk']);
    Route::get('/riwayat-persetujuan', [KemahasiswaanController::class, 'riwayatPersetujuan']);
    Route::get('/riwayat-persetujuan/show/{approval}', [KemahasiswaanController::class, 'showApproval'])->name('show-approval-kemahasiswaan');
    Route::get('/surat-masuk/show/{surat}', [KemahasiswaanController::class, 'showSuratMasuk'])->name('show-surat-masuk-kemahasiswaan');
    Route::post('/surat-masuk/setujui/{surat}', [KemahasiswaanController::class, 'setujuiSurat'])->name('setujui-surat-kemahasiswaan');
    Route::get('/surat-masuk/tolak/{surat}', [KemahasiswaanController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-kemahasiswaan');
    Route::post('/surat-masuk/tolak/{surat}', [KemahasiswaanController::class, 'tolakSurat'])->name('tolak-surat-kemahasiswaan');

    // Pengajuan surat
    Route::get('/pengajuan-surat', [KemahasiswaanController::class, 'pengajuanSurat'])->name('kemahasiswaan-pengajuan-surat');
    Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->name('kemahasiswaan-redirect-form-surat');
    Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('kemahasiswaan-show-form-surat');
    Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'storeByKemahasiswaan'])->name('kemahasiswaan-store-surat');
    Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-pengajuan-atk-kemahasiswaan', [SuratController::class, 'storeSuratPengajuanAtkByKemahasiswaan'])->name('kemahasiswaan-store-surat-pengajuan-atk');
    Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->name('kemahasiswaan-destroy-surat');
    Route::get('/riwayat-pengajuan-surat', [KemahasiswaanController::class, 'riwayatPengajuanSurat'])->name('kemahasiswaan-riwayat-pengajuan-surat');
    Route::get('/riwayat-pengajuan-surat/show/{surat}', [KemahasiswaanController::class, 'showDetailPengajuanSuratByKemahasiswaan'])->name('show-detail-pengajuan-surat-kemahasiswaan');

    // Profile dan dashboard
    Route::get('/', [KemahasiswaanController::class, 'dashboard']);
    Route::get('/profile', [KemahasiswaanController::class, 'profilePage']);
    Route::put('/profile/update/{user}', [KemahasiswaanController::class, 'updateProfile'])->name('update-profile-kemahasiswaan');
    Route::get('/profile/reset-password', [KemahasiswaanController::class, 'resetPasswordPage']);
    Route::put('/profile/reset-password/{user}', [KemahasiswaanController::class, 'resetPassword'])->name('reset-password-kemahasiswaan');
});
```

## Views

### 1. Dashboard (`resources/views/kemahasiswaan/dashboard.blade.php`)
- **Style SAMA PERSIS dengan mahasiswa** (copy-paste dan modifikasi link)
- Welcome banner dengan QR Code layanan pengaduan
- Statistik riwayat pengajuan dengan 6 status:
  - Selesai (hijau), Diproses (kuning), Dikirim (biru)
  - Menunggu Dibayar (pink), Ditolak (rose), Kadaluarsa (rose gelap)
- Card "Pengaturan Akun" dengan icon settings
- FAQ section dengan accordion (6 pertanyaan umum)
- **TIDAK ADA custom statistics cards** atau quick action buttons berbeda

### 6. Profile (`resources/views/kemahasiswaan/profile.blade.php`)
- **Layout 1 kolom (bukan 2 kolom)** karena field ganjil (email, nama, username)
- Form update profil dengan disabled state default
- Edit mode dengan parameter `?edit=true`
- **TIDAK ADA field stempel** (hanya untuk role yang membutuhkan)
- Email verification warning jika belum terverifikasi
- **Style SAMA PERSIS dengan mahasiswa** (copy-paste dan modifikasi)

### 7. Reset Password (`resources/views/kemahasiswaan/reset-password.blade.php`)
- **Style SAMA PERSIS dengan mahasiswa** (copy-paste dan modifikasi route)
- Form reset password 1 kolom
- 3 fields: password lama, password baru, konfirmasi password baru
- Link "Lupa kata sandi?" ke password request
- Buttons: Batal (pink) dan Perbarui (biru) dengan icon yang sama

### 2. Pengajuan Surat (`resources/views/kemahasiswaan/pengajuan-surat.blade.php`)
- Halaman pilih jenis surat
- Filter untuk jenis surat kemahasiswaan
- **Style konsisten dengan role lain**

### 3. Form Surat (`resources/views/kemahasiswaan/formsurat/form-surat-pengajuan-atk.blade.php`)
- Form pengajuan ATK (tanpa field program studi)
- Auto-fill data user (nama, username, email)
- Upload lampiran file
- **Style form sama dengan role lain**

### 4. Riwayat Pengajuan (`resources/views/kemahasiswaan/riwayat-pengajuan.blade.php`)
- Tabel riwayat pengajuan dengan filter
- Actions: Lihat, Batal (jika status diproses)
- **Style table sama dengan mahasiswa/staff**

### 5. Detail Surat (`resources/views/kemahasiswaan/show-surat.blade.php`)
- Detail surat dengan stepper
- Tidak ada tombol preview/cetak (karena surat ATK)
- **Style detail sama dengan role lain**

### 6. Profile (`resources/views/kemahasiswaan/profile.blade.php`)
- Form update profil
- Upload stempel

### 7. Reset Password (`resources/views/kemahasiswaan/reset-password.blade.php`)
- Form reset password

## Components

### Stepper Component (`resources/views/components/stepper-kemahasiswaan-pengajuan-atk.blade.php`)
- Component untuk tracking alur approval
- Alur: Kemahasiswaan → Kabag
- Status: Berhasil Mengajukan → Menunggu/Disetujui/Ditolak

## Breadcrumbs (`routes/breadcrumbs.php`)

```php
// Tambahkan breadcrumbs untuk kemahasiswaan
Breadcrumbs::for('kemahasiswaan-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/kemahasiswaan/pengajuan-surat');
});

Breadcrumbs::for('kemahasiswaan-pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('kemahasiswaan-pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('kemahasiswaan-show-form-surat', $jenisSurat));
});

Breadcrumbs::for('kemahasiswaan-riwayat-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Pengajuan Surat', '/kemahasiswaan/riwayat-pengajuan-surat');
});

Breadcrumbs::for('kemahasiswaan-show-pengajuan-surat', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('kemahasiswaan-riwayat-pengajuan-surat');
    $trail->push('Detail Pengajuan Surat', route('show-detail-pengajuan-surat-kemahasiswaan', $surat));
});
```

## Sidebar Menu (`resources/views/components/layout.blade.php`)

```php
'kemahasiswaan' => [
    [
        'link' => 'kemahasiswaan',
        'title' => 'Dashboard',
        'icon' => asset('svg/piechart.svg'),
        'dropdown' => [],
    ],
    [
        'link' => 'kemahasiswaan/pengajuan-surat',
        'title' => 'Pengajuan Surat',
        'icon' => asset('svg/letterpencil.svg'),
        'dropdown' => [],
    ],
    [
        'link' => 'kemahasiswaan/riwayat-pengajuan-surat',
        'title' => 'Riwayat Pengajuan',
        'icon' => asset('svg/letterline.svg'),
        'dropdown' => [],
    ],
    [
        'link' => 'kemahasiswaan/surat-masuk',
        'title' => 'Surat Masuk',
        'icon' => asset('svg/letter.svg'),
        'dropdown' => [],
    ],
    [
        'link' => 'kemahasiswaan/riwayat-persetujuan',
        'title' => 'Riwayat Persetujuan',
        'icon' => asset('svg/lettercheck.svg'),
        'dropdown' => [],
    ],
    [
        'link' => 'logout',
        'title' => 'Keluar',
        'icon' => asset('svg/signout.svg'),
        'dropdown' => [],
    ],
],
```

## 🎨 Style Consistency Rules (CRITICAL)

### **PRINSIP UTAMA: JANGAN NGADE BUAT STYLE BARU!**
1. **Copy-Paste-Modify approach** - Ambil dari role yang sudah ada
2. **Modifikasi hanya yang diperlukan** - Link, route, role-specific content
3. **Jangan menambahkan field tanpa alasan** - Hanya field yang benar-benar dibutuhkan
4. **Layout consistency** - Grid, spacing, colors, buttons sama persis

### **Dashboard Template Rules:**
```php
// Copy dari mahasiswa/dashboard.blade.php
// Ubah hanya link dari /mahasiswa/ menjadi /kemahasiswaan/
// Pastikan variabel statistik sama di controller:
'pengajuanSelesai', 'pengajuanDikirim', 'pengajuanDitolak',
'pengajuanDiproses', 'pengajuanMenungguDibayar', 'pengajuanKadaluarsa'
```

### **Profile Template Rules:**
```php
// Layout: 1 kolom jika field ganjil, 2 kolom jika field genap
// Fields wajib: email, nama, username
// Field opsional: role-specific (contoh: stempel untuk staff)
// Tidak ada field yang tidak diperlukan
```

### **Reset Password Template Rules:**
```php
// Copy dari mahasiswa/reset-password.blade.php
// Ubah hanya route dari reset-password-mahasiswa ke reset-password-kemahasiswaan
// Link kembali ke profile role masing-masing
```

### **Controller Data Rules:**
```php
// Dashboard variables WAJIB ada:
public function dashboard() {
    return view('role.dashboard', [
        'pengajuanSelesai' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'selesai')->get(),
        'pengajuanDikirim' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'dikirim')->get(),
        'pengajuanDitolak' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'ditolak')->get(),
        'pengajuanDiproses' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'diproses')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })->get(),
        'pengajuanMenungguDibayar' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'menunggu_pembayaran')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '>', $now);
        })->get(),
        'pengajuanKadaluarsa' => Surat::where('pengaju_id', '=', auth()->user()->id)->where('status', '=', 'diproses')->where(function ($query) {
            $now = Carbon::now();
            $query->whereNull('expired_at')->orWhere('expired_at', '<', $now);
        })->get(),
    ]);
}
```

### **Sidebar Menu Rules:**
- Hanya tambahkan menu yang benar-benar dibutuhkan
- Role yang hanya mengajuan: Dashboard, Pengajuan Surat, Riwayat Pengajuan, Keluar
- Role yang juga approve: tambahkan Surat Masuk, Riwayat Persetujuan
- Jangan tambahkan menu yang tidak ada controllernya

### **Error Prevention Checklist:**
1. ✅ Dashboard variables ada di controller
2. ✅ Route names consistent (role-name-action)
3. ✅ Link paths correct (/role-name/...)
4. ✅ No undefined variables in views
5. ✅ Layout classes consistent (1 vs 2 kolom)
6. ✅ Button colors and icons match existing patterns
7. ✅ No extra fields unless role-specific need

## Cara Menjalankan Implementation

### 1. Jalankan Migration & Seeder
```bash
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=JenisSuratSeeder
```

### 2. Clear Cache (Jika perlu)
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
composer dump-autoload
```

### 3. Test Login
- **Username:** `kemahasiswaan`
- **Password:** `password`
- **URL:** `http://localhost:8000/login`

## Alur Approval

### Kemahasiswaan → Kabag
- Kemahasiswaan mengajukan surat ATK
- Langsung masuk ke Kabag (role_id 17)
- Kabag bisa menyetujui atau menolak
- Tidak melewati Kaprodi (beda dengan Staff)

## Perbedaan dengan Role Lain

1. **Dibanding Staff:** Langsung ke Kabag (tanpa Kaprodi)
2. **Dibanding Akademik/Akademik-Fakultas:** Sama alurnya (langsung ke Kabag)
3. **Tidak ada field program studi** di form pengajuan ATK
4. **Tidak ada tombol preview/cetak** untuk surat pengajuan ATK

## Troubleshooting

### Error ERR_TOO_MANY_REDIRECTS
- Pastikan AuthController sudah ditambah untuk role_id 18
- Cek method `authenticate()` dan `home()` di AuthController

### Error Target Class Not Found
- Jalankan `composer dump-autoload`
- Restart Laravel server
- Clear semua cache

### User Not Found
- Pastikan RoleSeeder dan UserSeeder sudah dijalankan
- Cek di database apakah user kemahasiswaan ada dengan role_id 18

## Template untuk Role Baru

Untuk menambah role baru di masa depan:

1. **Database:**
   - Tambahkan role di RoleSeeder
   - Tambahkan user di UserSeeder
   - Tambahkan jenis surat di JenisSuratSeeder

2. **Controller:**
   - Copy dari existing controller
   - Ganti nama role dan role_id
   - Update method names

3. **Routes:**
   - Tambahkan route group dengan middleware userAccess
   - Sesuaikan role_id

4. **Views:**
   - Copy dan adapt existing views
   - Update breadcrumbs dan sidebar

5. **AuthController:**
   - Tambahkan kondisi untuk role_id baru di `authenticate()` dan `home()`

6. **SuratController:**
   - Tambahkan method storeSurat... untuk jenis surat baru
   - Update method `create()` untuk form rendering

7. **Filament Admin Panel:**
   - Buat Model yang extends User
   - Buat Resource dengan CRUD operations
   - Buat Pages (List, Create, Edit)
   - Tambahkan ke navigation group "Manajemen Akun"

Pattern ini bisa digunakan untuk role baru dengan fitur serupa.

## Filament Admin Panel

Untuk role baru, kita juga perlu membuatkan CRUD di admin panel Filament agar admin bisa mengelola akun-akun role tersebut.

### 1. Model (`app/Models/Kemahasiswaan.php`)
```php
<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kemahasiswaan extends User
{
    public $table = 'users';
    use HasFactory;
}
```

### 2. Filament Resource (`app/Filament/Resources/KemahasiswaanResource.php`)
```php
<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Kemahasiswaan;
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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\KemahasiswaanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KemahasiswaanResource\RelationManagers;

class KemahasiswaanResource extends Resource
{
    protected static ?string $model = Kemahasiswaan::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Kemahasiswaan';
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-kemahasiswaan';
    protected static ?int $navigationSort = 18;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    Hidden::make('role_id')
                        ->default(18),

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

                    TextInput::make('nip')
                        ->label('NIP')
                        ->placeholder('Masukkan NIP (opsional)')
                        ->nullable(),

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
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKemahasiswaans::route('/'),
            'create' => Pages\CreateKemahasiswaan::route('/create'),
            'edit' => Pages\EditKemahasiswaan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = User::where('role_id', 18);

        return $query;
    }
}
```

### 3. Pages

#### Create List Page (`app/Filament/Resources/KemahasiswaanResource/Pages/ListKemahasiswaans.php`)
```php
<?php

namespace App\Filament\Resources\KemahasiswaanResource\Pages;

use App\Filament\Resources\KemahasiswaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKemahasiswaans extends ListRecords
{
    protected static string $resource = KemahasiswaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
```

#### Create Page (`app/Filament/Resources/KemahasiswaanResource/Pages/CreateKemahasiswaan.php`)
```php
<?php

namespace App\Filament\Resources\KemahasiswaanResource\Pages;

use App\Filament\Resources\KemahasiswaanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKemahasiswaan extends CreateRecord
{
    protected static string $resource = KemahasiswaanResource::class;
}
```

#### Edit Page (`app/Filament/Resources/KemahasiswaanResource/Pages/EditKemahasiswaan.php`)
```php
<?php

namespace App\Filament\Resources\KemahasiswaanResource\Pages;

use App\Filament\Resources\KemahasiswaanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKemahasiswaan extends EditRecord
{
    protected static string $resource = KemahasiswaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
```

### 4. Direktori Structure
```
app/
├── Models/
│   └── Kemahasiswaan.php
└── Filament/
    └── Resources/
        └── KemahasiswaanResource.php
        └── Pages/
            ├── ListKemahasiswaans.php
            ├── CreateKemahasiswaan.php
            └── EditKemahasiswaan.php
```

### 5. Cara Mengakses
1. Login sebagai admin ke Filament panel
2. Menu "Manajemen Akun" → "Kemahasiswaan"
3. CRUD operations: Create, Read, Update, Delete

### 6. Template untuk Role Baru (Filament)
Untuk role baru di masa depan, ikuti pattern:

1. **Model**: Extends User, table = 'users'
2. **Resource**:
   - Ganti model class
   - Update role_id di Hidden field
   - Adjust navigation sort
   - Tambah/Remove fields sesuai kebutuhan
3. **Pages**: Copy dari template yang ada
4. **Navigation**: Tambahkan ke group "Manajemen Akun"

---

Dokumen ini akan menjadi reference lengkap untuk reproduksi fitur yang sama untuk role baru lainnya. Semua perubahan yang kita lakukan sudah terdokumentasi dengan rinci dan sistematis.

Sekarang kamu bisa gunakan dokumentasi ini untuk:
1. Debug masalah yang ada saat ini
2. Implementasi role baru di masa depan
3. Memahami pola dan pattern yang digunakan dalam sistem
4. Membuat CRUD admin panel untuk role baru

## Troubleshooting

### Error ERR_TOO_MANY_REDIRECTS
- **Penyebab:** AuthController tidak memiliki kondisi untuk role_id 18
- **Solusi:** Tambahkan kondisi di method `authenticate()` dan `home()`

### Error Target Class KemahasiswaanController Not Found
- **Penyebab:** Autoloader belum me-register class baru
- **Solusi:** Jalankan `composer dump-autoload` dan clear cache

### Error Sidebar Tidak Muncul
- **Penyebab:** Tidak ada kondisi untuk role_id 18 di layout.blade.php
- **Solusi:** Tambahkan `@elseif ($authUser->role_id == 18)` sebelum `@endif`

### Error Target Class [ControllerName] Does Not Exist
- **Penyebab:** Controller tidak di-import di `routes/web.php` meskipun file sudah ada
- **Solusi:** Tambahkan import statement di atas routes/web.php
- **Contoh:** `use App\Http\Controllers\KemahasiswaanController;`

#### Ceklist untuk Controller Baru:
1. ✅ Buat file controller di `app/Http/Controllers/`
2. ✅ Tambahkan import statement di `routes/web.php`
3. ✅ Gunakan controller di route definitions
4. ✅ Tambahkan kondisi sidebar di `layout.blade.php`
5. ✅ Tambahkan kondisi di `AuthController`

#### Contoh Fix Layout:
```php
@elseif ($authUser->role_id == 17)
    <x-sidebar :listsData="$listsData['kabag']" />
@elseif ($authUser->role_id == 18)  // <-- Tambahkan ini
    <x-sidebar :listsData="$listsData['kemahasiswaan']" />
@endif
```

## Commands untuk Troubleshooting

### Clear Cache Laravel
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### Refresh Autoloader
```bash
composer dump-autoload
```

### Optimize Clear
```bash
php artisan optimize:clear
```

### Complete Reset
```bash
php artisan cache:clear && php artisan view:clear && php artisan config:clear && php artisan route:clear && composer dump-autoload && php artisan optimize:clear
```

### Reset Database (Jika terjadi duplikasi role)
```bash
# Hapus semua tables dan migrate ulang
php artisan migrate:fresh --seed
```

## ⚠️ Critical Issues & Solutions

### Database Duplication Issue
- **Penyebab:** RoleSeeder/UserSeeder menggunakan `create()` bukan `firstOrCreate()`
- **Gejala:** Jumlah role/user membengkak (bisa 50+ role)
- **Solusi:** Reset database dan gunakan `firstOrCreate()` di seeder
- **Prevention:** Selalu gunakan `firstOrCreate()` untuk seeder yang aman di-berulang

### Role ID Mismatch Issue
- **Penyebab:** RoleSeeder tidak mengikuti urutan yang konsisten dengan UserSeeder
- **Gejala:** UserSeeder menggunakan role_id: 18 tapi role kemahasiswaan dapat ID: 17
- **Solusi:** Pastikan urutan role di RoleSeeder sesuai dengan yang diharapkan UserSeeder
- **Prevention:** Selalu cek urutan role saat menambah role baru

#### Role Order Consistency (Critical):
```php
// RoleSeeder array order determines role ID!
$roles = [
    // ... existing roles ...
    ['name' => 'akademik-fakultas', 'description' => 'Akademik Fakultas'], // ID: 16
    ['name' => 'kabag', 'description' => 'Kabag'],                         // ID: 17 ← PENTING!
    ['name' => 'kemahasiswaan', 'description' => 'Kemahasiswaan'],              // ID: 18 ← PENTING!
];

// UserSeeder harus sesuai:
'user kemahasiswaan' => [
    // ... other fields ...
    'role_id' => 18, // ← Harus match dengan RoleSeeder order!
],
```

#### Cara Cek Role ID:
```bash
php artisan tinker --execute="echo 'Role kemahasiswaan: ' . App\Models\Role::where('name', 'kemahasiswaan')->first()->id;"
```

#### Cara Fix RoleSeeder:
```php
// DARI (DANGEROUS):
foreach ($roles as $role) {
    Role::create($role);  // Akan duplikat!
}

// JADI (SAFE):
foreach ($roles as $role) {
    Role::firstOrCreate(
        ['name' => $role['name']],  // Unique key
        ['description' => $role['description']]
    );
}
```

### 4. Update KabagController (`app/Http/Controllers/KabagController.php`)

#### Method `showSuratMasuk()` - Tambahkan handling untuk kemahasiswaan ATK:
```php
if ($surat->jenisSurat->user_type == 'kemahasiswaan' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-kemahasiswaan') {
    return view('kabag.show-surat', [
        'surat' => $surat,
        'daftarPenerima' => User::select('id', 'name', 'username')
            ->where('role_id', '=', 7)
            ->get()
    ]);
}
```

### 5. Update Kabag Show-Approval View (`resources/views/kabag/show-approval.blade.php`)

#### Sembunyikan tombol cetak untuk semua jenis surat ATK:
```php
@if (
    $approval->surat->jenisSurat->slug != 'berita-acara-nilai' &&
        $approval->surat->jenisSurat->slug != 'surat-pengajuan-atk' &&
        $approval->surat->jenisSurat->slug != 'surat-pengajuan-atk-akademik' &&
        $approval->surat->jenisSurat->slug != 'surat-pengajuan-atk-akademik-fakultas' &&
        $approval->surat->jenisSurat->slug != 'surat-pengajuan-atk-kemahasiswaan')
```

**Penjelasan:** Menambahkan kondisi untuk menyembunyikan tombol cetak pada semua jenis surat pengajuan ATK dari berbagai user type (staff, akademik, akademik-fakultas, kemahasiswaan).

Semoga membantu! 🎯