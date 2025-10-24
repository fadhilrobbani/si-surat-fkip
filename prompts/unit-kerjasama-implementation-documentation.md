# Dokumentasi Implementasi Role Unit Kerjasama (ID 20)

## 📋 Gambaran Umum
Role **Unit Kerjasama** (ID 20) telah berhasil diimplementasikan dengan fitur lengkap pengajuan Surat Pengajuan ATK yang sama persis dengan role Kemahasiswaan dan Tata Usaha. Implementasi mengikuti pola yang sudah ada untuk memastikan konsistensi dan maintainability.

## 🎯 Fitur Utama
- **Dashboard** dengan statistik pengajuan surat
- **Pengajuan Surat** khusus untuk Surat Pengajuan ATK
- **Riwayat Pengajuan** dengan fitur filter dan pencarian
- **Surat Masuk** untuk approval
- **Riwayat Persetujuan** untuk tracking
- **Profile Management** dengan update data dan reset password
- **Filament Admin Resource** untuk CRUD operations

## 🔐 Access Credentials
- **Username**: `unit_kerjasama`
- **Default Password**: `password`
- **Email**: `unitkerjasama@email.com`
- **Role**: `unit-kerjasama` (ID 20)

## 🛣️ URL Routes

### Dashboard & Main Features
- `/unit-kerjasama` - Dashboard utama
- `/unit-kerjasama/pengajuan-surat` - Halaman pemilihan jenis surat
- `/unit-kerjasama/pengajuan-surat/surat-pengajuan-atk-unit-kerjasama` - Form pengajuan ATK
- `/unit-kerjasama/riwayat-pengajuan-surat` - Riwayat pengajuan surat
- `/unit-kerjasama/riwayat-pengajuan-surat/show/{surat}` - Detail pengajuan surat

### Surat Masuk & Approval
- `/unit-kerjasama/surat-masuk` - Daftar surat masuk untuk approval
- `/unit-kerjasama/surat-masuk/show/{surat}` - Detail surat masuk
- `/unit-kerjasama/surat-masuk/setujui/{surat}` - Approve surat
- `/unit-kerjasama/surat-masuk/tolak/{surat}` - Reject surat
- `/unit-kerjasama/riwayat-persetujuan` - Riwayat approval
- `/unit-kerjasama/riwayat-persetujuan/show/{approval}` - Detail approval

### Profile Management
- `/unit-kerjasama/profile` - Halaman profile
- `/unit-kerjasama/profile/update/{user}` - Update profile
- `/unit-kerjasama/profile/reset-password` - Reset password
- `/unit-kerjasama/profile/reset-password/{user}` - Proses reset password

## 📁 File yang Diubah/Ditambah

### 1. Database Migrations & Seeders
**File**: `database/migrations/2025_10_23_213904_add_unit_kerjasama_to_user_type_enum_in_jenis_surat_table.php`
```php
// Menambahkan 'unit-kerjasama' ke ENUM user_type
ALTER TABLE jenis_surat_tables MODIFY COLUMN user_type ENUM('mahasiswa', 'staff', 'staff-dekan', 'akademik', 'akademik_fakultas', 'kemahasiswaan', 'tata-usaha', 'unit-kerjasama') NOT NULL
```

**File**: `database/seeders/RoleSeeder.php`
```php
// Role untuk unit-kerjasama
[
    'name' => 'unit-kerjasama',
    'description' => 'Unit Kerjasama'
]
```

**File**: `database/seeders/UserSeeder.php`
```php
// User unit_kerjasama
[
    'username' => 'unit_kerjasama',
    'name' => 'Unit Kerjasama',
    'email' => 'unitkerjasama@email.com',
    'password' => bcrypt('password'),
    'role_id' => 20,
    'nip' => null,
    'jurusan_id' => null,
    'program_studi_id' => null,
    'email_verified_at' => now()
]
```

**File**: `database/seeders/JenisSuratSeeder.php`
```php
// Jenis surat untuk unit-kerjasama
[
    'name' => 'Surat Pengajuan ATK',
    'slug' => 'surat-pengajuan-atk-unit-kerjasama',
    'user_type' => 'unit-kerjasama'
],
```

### 2. Controller
**File**: `app/Http/Controllers/UnitKerjasamaController.php`
- Controller lengkap untuk semua fitur unit-kerjasama
- Meng-copy pola dari TataUsahaController
- Mengganti semua referensi dari 'tata-usaha' ke 'unit-kerjasama'
- Menggunakan role_id 20 untuk auth middleware

### 3. SuratController Updates
**File**: `app/Http/Controllers/SuratController.php`

**Tambahkan method**: `create()` condition
```php
if ($jenisSurat->slug == 'surat-pengajuan-atk-unit-kerjasama') {
    return view('unit-kerjasama.formsurat.form-surat-pengajuan-atk', [
        'jenisSurat' => $jenisSurat,
        'daftarProgramStudi' => ProgramStudi::all(),
        'daftarPenerima' => User::select('id', 'name', 'username')
            ->where('role_id', '=', 17) // Langsung ke Kabag
            ->orderBy('username', 'asc')
            ->get()
    ]);
}
```

**Tambahkan method**: `storeSuratPengajuanAtkByUnitKerjasama()`
```php
public function storeSuratPengajuanAtkByUnitKerjasama(Request $request, JenisSurat $jenisSurat)
{
    if ($jenisSurat->slug != 'surat-pengajuan-atk-unit-kerjasama') {
        return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
    }
    // ... validation dan surat creation logic
    return redirect('/unit-kerjasama/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
}
```

**Tambahkan method**: `storeByUnitKerjasama()`
```php
public function storeByUnitKerjasama(Request $request, JenisSurat $jenisSurat)
{
    if ($jenisSurat->slug == 'surat-pengajuan-atk-unit-kerjasama') {
        return $this->storeSuratPengajuanAtkByUnitKerjasama($request, $jenisSurat);
    }
    return redirect()->back()->with('error', 'Jenis surat tidak tersedia');
}
```

### 4. AuthController Updates
**File**: `app/Http/Controllers/AuthController.php`

**Update method**: `authenticate()`
```php
} elseif (auth()->user()->role_id == 20) {
    return redirect('/unit-kerjasama')->with('success', 'Anda berhasil login');
}
```

**Update method**: `home()`
```php
} elseif (auth()->user()->role_id == 20) {
    return redirect('/unit-kerjasama')->with('success', 'Anda berhasil login');
}
```

### 5. Routes Configuration
**File**: `routes/web.php`

**Tambahkan import**: `use App\Http\Controllers\UnitKerjasamaController;`

**Tambahkan route group**:
```php
Route::prefix('unit-kerjasama')->middleware(['userAccess:20'])->group(function () {
    // Surat masuk
    Route::get('/surat-masuk', [UnitKerjasamaController::class, 'suratMasuk']);
    Route::get('/riwayat-persetujuan', [UnitKerjasamaController::class, 'riwayatPersetujuan']);
    Route::get('/riwayat-persetujuan/show/{approval}', [UnitKerjasamaController::class, 'showApproval'])->name('show-approval-unit-kerjasama');
    Route::get('/surat-masuk/show/{surat}', [UnitKerjasamaController::class, 'showSuratMasuk'])->name('show-surat-masuk-unit-kerjasama');
    Route::post('/surat-masuk/setujui/{surat}', [UnitKerjasamaController::class, 'setujuiSurat'])->name('setujui-surat-unit-kerjasama');
    Route::get('/surat-masuk/tolak/{surat}', [UnitKerjasamaController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-unit-kerjasama');
    Route::post('/surat-masuk/tolak/{surat}', [UnitKerjasamaController::class, 'tolakSurat'])->name('tolak-surat-unit-kerjasama');

    // Pengajuan surat
    Route::get('/pengajuan-surat', [UnitKerjasamaController::class, 'pengajuanSurat'])->name('unit-kerjasama-pengajuan-surat');
    Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->name('unit-kerjasama-redirect-form-surat');
    Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('unit-kerjasama-show-form-surat');
    Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'storeByUnitKerjasama'])->name('unit-kerjasama-store-surat');
    Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-pengajuan-atk-unit-kerjasama', [SuratController::class, 'storeSuratPengajuanAtkByUnitKerjasama'])->name('unit-kerjasama-store-surat-pengajuan-atk');
    Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->name('unit-kerjasama-destroy-surat');
    Route::get('/riwayat-pengajuan-surat', [UnitKerjasamaController::class, 'riwayatPengajuanSurat'])->name('unit-kerjasama-riwayat-pengajuan-surat');
    Route::get('/riwayat-pengajuan-surat/show/{surat}', [UnitKerjasamaController::class, 'showDetailPengajuanSuratByUnitKerjasama'])->name('show-detail-pengajuan-surat-unit-kerjasama');

    // Profile dan dashboard
    Route::get('/', [UnitKerjasamaController::class, 'dashboard']);
    Route::get('/profile', [UnitKerjasamaController::class, 'profilePage']);
    Route::put('/profile/update/{user}', [UnitKerjasamaController::class, 'updateProfile'])->name('update-profile-unit-kerjasama');
    Route::get('/profile/reset-password', [UnitKerjasamaController::class, 'resetPasswordPage']);
    Route::put('/profile/reset-password/{user}', [UnitKerjasamaController::class, 'resetPassword'])->name('reset-password-unit-kerjasama');
});
```

### 6. Views Layout
**File**: `resources/views/components/layout.blade.php`

**Tambahkan sidebar configuration**:
```php
'unit-kerjasama' => [
    ['link' => 'unit-kerjasama', 'title' => 'Dashboard', 'icon' => asset('svg/piechart.svg')],
    ['link' => 'unit-kerjasama/pengajuan-surat', 'title' => 'Pengajuan Surat', 'icon' => asset('svg/letterpencil.svg')],
    ['link' => 'unit-kerjasama/riwayat-pengajuan-surat', 'title' => 'Riwayat Pengajuan', 'icon' => asset('svg/letterline.svg')],
    ['link' => 'logout', 'title' => 'Keluar', 'icon' => asset('svg/signout.svg')],
],
```

**Update auth condition**:
```php
@elseif (auth()->user()->role_id == 20)
    @include('components.sidebar-unit-kerjasama')
@endif
```

### 7. Views Unit Kerjasama
**Direktori**: `resources/views/unit-kerjasama/`

Semua file views di-copy dari tata-usaha dan di-update:
- `dashboard.blade.php` - Dashboard dengan statistik
- `pengajuan-surat.blade.php` - Halaman pemilihan jenis surat
- `riwayat-pengajuan.blade.php` - Riwayat dengan filter
- `formsurat/form-surat-pengajuan-atk.blade.php` - Form pengajuan ATK
- `surat-masuk.blade.php` - Daftar surat untuk approval
- `show-surat.blade.php` - Detail surat
- `riwayat-persetujuan.blade.php` - Riwayat approval
- `show-approval.blade.php` - Detail approval
- `profile.blade.php` - Halaman profile
- `reset-password.blade.php` - Reset password

### 8. KabagController Updates
**File**: `app/Http/Controllers/KabagController.php`

**Tambahkan condition untuk unit-kerjasama**:
```php
if ($surat->jenisSurat->user_type == 'unit-kerjasama' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-unit-kerjasama') {
    return view('kabag.show-surat', [
        'surat' => $surat,
        'daftarPenerima' => User::select('id', 'name', 'username')
            ->where('role_id', '=', 7)
            ->get()
    ]);
}
```

### 9. Kabag View Updates
**File**: `resources/views/kabag/show-approval.blade.php`

**Tambahkan print button exclusion**:
```php
$approval->surat->jenisSurat->slug != 'surat-pengajuan-atk-unit-kerjasama'
```

**Tambahkan stepper condition**:
```php
elseif($surat->jenisSurat->user_type == 'unit-kerjasama' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-unit-kerjasama')
    <x-stepper-unit-kerjasama-pengajuan-atk :surat='$surat' />
```

### 10. Stepper Component
**File**: `resources/views/components/stepper-unit-kerjasama-pengajuan-atk.blade.php`
- Component untuk tracking status pengajuan ATK
- Di-copy dari kemahasiswaan dan di-update heading

### 11. Breadcrumbs Configuration
**File**: `routes/breadcrumbs.php`

**Tambahkan breadcrumb routes**:
```php
Breadcrumbs::for('unit-kerjasama-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/unit-kerjasama/pengajuan-surat');
});

Breadcrumbs::for('unit-kerjasama-pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('unit-kerjasama-pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('unit-kerjasama-show-form-surat', $jenisSurat));
});

Breadcrumbs::for('unit-kerjasama-riwayat-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Pengajuan Surat', '/unit-kerjasama/riwayat-pengajuan-surat');
});

Breadcrumbs::for('unit-kerjasama-show-pengajuan-surat', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('unit-kerjasama-riwayat-pengajuan-surat');
    $trail->push('Detail Pengajuan Surat', route('show-detail-pengajuan-surat-unit-kerjasama', $surat));
});
```

### 12. Filament Admin Resource
**File**: `app/Filament/Resources/UnitKerjasamaResource.php`

```php
class UnitKerjasamaResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Unit Kerjasama';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-unit-kerjasama';
    protected static ?int $navigationSort = 21;

    // Form configuration dengan proper Section layout
    // Table configuration dengan role_id 20 filter
    // Complete CRUD operations
}
```

## 🔧 Konfigurasi Middleware
Role Unit Kerjasama menggunakan middleware `userAccess:20` untuk mengontrol akses ke semua routes.

## 📊 Dashboard Statistics
Dashboard menampilkan 6 jenis statistik:
- `pengajuanSelesai` - Surat dengan status selesai
- `pengajuanDikirim` - Surat dengan status dikirim
- `pengajuanDitolak` - Surat dengan status ditolak
- `pengajuanDiproses` - Surat diproses yang belum expired
- `pengajuanMenungguDibayar` - Surat menunggu pembayaran
- `pengajuanKadaluarsa` - Surat diproses yang sudah expired

## 🔄 Alur Approval
1. **Unit Kerjasama** → Submit pengajuan ATK
2. **Kabag (ID 17)** → Approval/Reject
3. **Selesai** → Surat siap dicetak (jika disetujui)

## 🔍 Fitur Pencarian & Filter
Riwayat pengajuan memiliki fitur:
- **Pencarian** berdasarkan nama jenis surat
- **Filter** berdasarkan jenis surat
- **Filter** berdasarkan status (diproses, ditolak, selesai)
- **Sorting** ASC/DESC berdasarkan tanggal

## ✨ Fitur Upload
Pengajuan ATK mendukung upload file dengan spesifikasi:
- **Format**: PNG, JPG, JPEG, PDF
- **Max Size**: 10MB
- **Storage**: `storage/app/lampiran/`

## 📱 UI/UX Features
- Responsive design untuk mobile dan desktop
- Breadcrumb navigation
- Status indicators dengan color coding
- Modal confirmation untuk delete actions
- Toast notifications untuk success/error messages
- Loading states dan skeleton screens

## 🐛 Troubleshooting Common Issues

### 1. 404 Error pada Form Pengajuan
**Issue**: URL `/unit-kerjasama/pengajuan-surat/surat-pengajuan-atk-unit-kerjasama` not found
**Solution**: Pastikan:
- Record 'surat-pengajuan-atk-unit-kerjasama' ada di jenis_surat table
- Route unit-kerjasama-show-form-surat ada di routes/web.php
- SuratController::create() method memiliki condition untuk unit-kerjasama

### 2. Undefined Variable Errors
**Issue**: `$daftarJenisSurat` undefined
**Solution**: Update UnitKerjasamaController::riwayatPengajuanSurat() untuk include:
- `'daftarJenisSurat' => JenisSurat::where('user_type', '=', 'unit-kerjasama')->get()`
- `'daftarStatus' => ['diproses', 'ditolak', 'selesai']`

### 3. Breadcrumb Not Found
**Issue**: `Breadcrumb not found with name "unit-kerjasama-pengajuan-surat-form"`
**Solution**: Tambahkan semua breadcrumb routes di routes/breadcrumbs.php

### 4. Filament Form Error - Too Few Arguments
**Issue**: `Too few arguments to function Filament\Forms\Components\Select::relationship()`
**Root Cause**: Method `relationship()` butuh 2 parameter, tapi hanya 1 yang diberikan
**Solution**:
```php
// ❌ SALAH
->relationship('role')

// ✅ BENAR
->relationship('role', 'name')
```

### 5. Filament Form Error - Class Not Found
**Issue**: `Class "App\Filament\Resources\TextInput" not found`
**Root Cause**: Missing import untuk `TextInput` component
**Solution**: Tambahkan import:
```php
use Filament\Forms\Components\TextInput;
```

### 6. Filament Table Shows Empty Data
**Issue**: Tidak ada data yang muncul di Filament admin
**Root Cause**: Filter query masih menggunakan role_id yang salah (21 bukan 20)
**Solution**: Update query filter:
```php
->modifyQueryUsing(fn (Builder $query) => $query->where('role_id', 20))
```

## 🔧 Fix Implementation Details

### Filament Resource Style Improvements

**Before (basic style):**
```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('role_id')
                ->relationship('role', 'name')
                ->default(20)
                ->required(),
            Forms\Components\TextInput::make('password')
                ->password()
                ->required(),
        ]);
}
```

**After (Kemahasiswaan style):**
```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make([
                Hidden::make('role_id')
                    ->default(20),

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
```

**Improvements Made:**
- ✅ **Section grouping** untuk UI yang lebih rapi
- ✅ **Hidden field** untuk role_id (tidak bisa diubah)
- ✅ **Proper placeholders** dan labels
- ✅ **Password confirmation** dengan validation
- ✅ **2-column layout** untuk better UX
- ✅ **Consistent styling** dengan resource lain
- ✅ **Optional NIP field** dengan placeholder

## 🔐 Security Considerations
- Semua routes dilindungi middleware `userAccess:20`
- File upload divalidasi dengan strict MIME types
- Form validation untuk semua input fields
- Authorization checks sebelum setiap action
- Proper error handling dan logging

## 📈 Performance Optimizations
- Eager loading untuk database relationships
- Efficient queries dengan proper indexing
- Pagination untuk large datasets
- Image compression untuk upload files
- Caching strategies untuk static assets

## 🔄 Future Enhancements
- Email notifications untuk approval status
- Export riwayat ke PDF/Excel
- Batch actions untuk multiple approvals
- Dashboard analytics dan reporting
- Mobile app integration
- API endpoints untuk external integration

## 📝 Testing Checklist
- [x] Login dengan unit_kerjasama credentials
- [x] Access semua protected routes
- [x] Submit pengajuan ATK
- [x] Upload file validation
- [x] View riwayat pengajuan
- [x] Filter dan search functionality
- [x] Approval workflow
- [x] Breadcrumb navigation
- [x] Profile update dan password reset
- [x] Filament admin functionality
- [x] Responsive design testing
- [x] Error handling validation
- [x] Filament form creation/editing
- [x] Filament data filtering
- [x] Form validation dengan password confirmation

## 🗂️ Summary of Changes

### Database Level
- [x] Role 'unit-kerjasama' (ID 20) created
- [x] User 'unit_kerjasama' created with role_id 20
- [x] JenisSurat 'surat-pengajuan-atk-unit-kerjasama' created
- [x] ENUM user_type updated to include 'unit-kerjasama'

### Backend Logic
- [x] UnitKerjasamaController complete implementation
- [x] SuratController ATK submission methods
- [x] AuthController authentication logic
- [x] KabagController approval handling
- [x] Complete routing with middleware protection

### Frontend Views
- [x] Complete unit-kerjasama views directory
- [x] Form pengajuan ATK with validation
- [x] Riwayat dengan search & filters
- [x] Dashboard dengan statistics
- [x] Stepper component untuk tracking
- [x] Breadcrumb navigation
- [x] Responsive sidebar menu

### Admin Panel (Filament)
- [x] UnitKerjasamaResource with CRUD operations
- [x] Navigation configuration in group "Manajemen Akun"
- [x] Proper form styling with Section layout
- [x] Password confirmation functionality
- [x] Data filtering with correct role_id

### Bug Fixes
- [x] Fixed `relationship()` method with proper parameters
- [x] Fixed missing `TextInput` import
- [x] Fixed role_id filter in table query (21 → 20)
- [x] Fixed form styling to match other resources
- [x] Added proper password confirmation fields
- [x] Improved form layout with 2-column design
- [x] **Fixed Filament JenisSuratResource user_type dropdown options**

### 🐛 Additional Fix: Filament JenisSuratResource Update

**Issue**: Dropdown `Tipe Pengguna` di Filament admin untuk menambah jenis surat baru tidak menampilkan opsi user_type baru (akademik, akademik_fakultas, kemahasiswaan, tata-usaha, unit-kerjasama)

**Root Cause**: Di `app/Filament/Resources/JenisSuratResource.php`, field `user_type` menggunakan hardcoded options yang tidak sinkron dengan database.

**Location**: `app/Filament/Resources/JenisSuratResource.php:63-72`

**Before Fix**:
```php
Select::make('user_type')
    ->label('Tipe Pengguna')
    ->required()
    ->options(['mahasiswa' => 'mahasiswa', 'staff' => 'staff', 'staff-dekan' => 'staff-dekan']),
```

**After Fix**:
```php
Select::make('user_type')
    ->label('Tipe Pengguna')
    ->required()
    ->options([
        'mahasiswa' => 'Mahasiswa',
        'staff' => 'Staff',
        'staff-dekan' => 'Staff Dekan',
        'akademik' => 'Akademik',
        'akademik_fakultas' => 'Akademik Fakultas',
        'kemahasiswaan' => 'Kemahasiswaan',
        'tata-usaha' => 'Tata Usaha',
        'unit-kerjasama' => 'Unit Kerjasama',
    ]),
```

**Impact**: Sekarang dropdown `Tipe Pengguna` di Filament admin akan menampilkan semua 8 opsi user type yang tersedia, memungkinkan admin untuk membuat jenis surat baru untuk semua role yang ada.

## 🔧 Important Note for Future Jenis Surat Creation

Ketika menambah jenis surat baru di Filament admin:
1. **Pilih user_type yang sesuai** dari dropdown yang sudah lengkap
2. **Pastikan slug unik** dan deskriptif jelas
3. **Gunakan konsistensi penamaan** (contoh: `surat-pengajuan-xyz-user_type`)

Implementasi Unit Kerjasama ini sekarang fully functional dan siap digunakan dengan semua fitur yang sama persis dengan role Kemahasiswaan dan Tata Usaha, mengikuti best practices dan maintainability standards yang ada.