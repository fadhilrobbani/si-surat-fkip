# Dokumentasi Implementasi Role Tata Usaha (ID 19)

## 📋 Gambaran Umum
Role **Tata Usaha** (ID 19) telah berhasil diimplementasikan dengan fitur lengkap pengajuan Surat Pengajuan ATK yang sama persis dengan role Kemahasiswaan. Implementasi mengikuti pola yang sudah ada untuk memastikan konsistensi dan maintainability.

## 🎯 Fitur Utama
- **Dashboard** dengan statistik pengajuan surat
- **Pengajuan Surat** khusus untuk Surat Pengajuan ATK
- **Riwayat Pengajuan** dengan fitur filter dan pencarian
- **Surat Masuk** untuk approval
- **Riwayat Persetujuan** untuk tracking
- **Profile Management** dengan update data dan reset password

## 🔐 Access Credentials
- **Username**: `tata_usaha`
- **Default Password**: `password`
- **Email**: `tatausaha@email.com`
- **Role**: `tata-usaha` (ID 19)

## 🛣️ URL Routes

### Dashboard & Main Features
- `/tata-usaha` - Dashboard utama
- `/tata-usaha/pengajuan-surat` - Halaman pemilihan jenis surat
- `/tata-usaha/pengajuan-surat/surat-pengajuan-atk-tata-usaha` - Form pengajuan ATK
- `/tata-usaha/riwayat-pengajuan-surat` - Riwayat pengajuan surat
- `/tata-usaha/riwayat-pengajuan-surat/show/{surat}` - Detail pengajuan surat

### Surat Masuk & Approval
- `/tata-usaha/surat-masuk` - Daftar surat masuk untuk approval
- `/tata-usaha/surat-masuk/show/{surat}` - Detail surat masuk
- `/tata-usaha/surat-masuk/setujui/{surat}` - Approve surat
- `/tata-usaha/surat-masuk/tolak/{surat}` - Reject surat
- `/tata-usaha/riwayat-persetujuan` - Riwayat approval
- `/tata-usaha/riwayat-persetujuan/show/{approval}` - Detail approval

### Profile Management
- `/tata-usaha/profile` - Halaman profile
- `/tata-usaha/profile/update/{user}` - Update profile
- `/tata-usaha/profile/reset-password` - Reset password
- `/tata-usaha/profile/reset-password/{user}` - Proses reset password

## 📁 File yang Diubah/Ditambah

### 1. Database Migrations & Seeders
**File**: `database/migrations/2025_10_23_134825_add_tata_usaha_to_user_type_enum_in_jenis_surat_table.php`
```php
// Menambahkan 'tata-usaha' ke ENUM user_type
ALTER TABLE jenis_surat_tables MODIFY COLUMN user_type ENUM('mahasiswa', 'staff', 'staff-dekan', 'akademik', 'akademik_fakultas', 'kemahasiswaan', 'tata-usaha') NOT NULL
```

**File**: `database/seeders/RoleSeeder.php`
```php
// Role untuk tata-usaha
[
    'name' => 'tata-usaha',
    'description' => 'Tata Usaha'
]
```

**File**: `database/seeders/UserSeeder.php`
```php
// User tata_usaha
[
    'username' => 'tata_usaha',
    'name' => 'Tata Usaha',
    'email' => 'tatausaha@email.com',
    'password' => bcrypt('password'),
    'role_id' => 19,
    'nip' => null,
    'jurusan_id' => null,
    'program_studi_id' => null,
    'email_verified_at' => now()
]
```

**File**: `database/seeders/JenisSuratSeeder.php`
```php
// Jenis surat untuk tata-usaha
[
    'name' => 'Surat Pengajuan ATK',
    'slug' => 'surat-pengajuan-atk-tata-usaha',
    'user_type' => 'tata-usaha'
]
```

### 2. Controller
**File**: `app/Http/Controllers/TataUsahaController.php`
- Controller lengkap untuk semua fitur tata-usaha
- Meng-copy pola dari KemahasiswaanController
- Mengganti semua referensi dari 'kemahasiswaan' ke 'tata-usaha'
- Menggunakan role_id 19 untuk auth middleware

### 3. SuratController Updates
**File**: `app/Http/Controllers/SuratController.php`

**Tambahkan method**: `create()` condition
```php
if ($jenisSurat->slug == 'surat-pengajuan-atk-tata-usaha') {
    return view('tata-usaha.formsurat.form-surat-pengajuan-atk', [
        'jenisSurat' => $jenisSurat,
        'daftarProgramStudi' => ProgramStudi::all(),
        'daftarPenerima' => User::select('id', 'name', 'username')
            ->where('role_id', '=', 17) // Langsung ke Kabag
            ->orderBy('username', 'asc')
            ->get()
    ]);
}
```

**Tambahkan method**: `storeSuratPengajuanAtkByTataUsaha()`
```php
public function storeSuratPengajuanAtkByTataUsaha(Request $request, JenisSurat $jenisSurat)
{
    if ($jenisSurat->slug != 'surat-pengajuan-atk-tata-usaha') {
        return redirect()->back()->with('error', 'Jenis surat tidak sesuai');
    }
    // ... validation dan surat creation logic
    return redirect('/tata-usaha/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
}
```

**Tambahkan method**: `storeByTataUsaha()`
```php
public function storeByTataUsaha(Request $request, JenisSurat $jenisSurat)
{
    if ($jenisSurat->slug == 'surat-pengajuan-atk-tata-usaha') {
        return $this->storeSuratPengajuanAtkByTataUsaha($request, $jenisSurat);
    }
    return redirect()->back()->with('error', 'Jenis surat tidak tersedia');
}
```

### 4. AuthController Updates
**File**: `app/Http/Controllers/AuthController.php`

**Update method**: `authenticate()`
```php
} elseif (auth()->user()->role_id == 19) {
    return redirect('/tata-usaha')->with('success', 'Anda berhasil login');
}
```

**Update method**: `home()`
```php
} elseif (auth()->user()->role_id == 19) {
    return redirect('/tata-usaha')->with('success', 'Anda berhasil login');
}
```

### 5. Routes Configuration
**File**: `routes/web.php`

**Tambahkan import**: `use App\Http\Controllers\TataUsahaController;`

**Tambahkan route group**:
```php
Route::prefix('tata-usaha')->middleware(['userAccess:19'])->group(function () {
    // Routes for pengajuan surat by tata-usaha
    Route::get('/pengajuan-surat', [TataUsahaController::class, 'pengajuanSurat'])->name('tata-usaha-pengajuan-surat');
    Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->name('tata-usaha-redirect-form-surat');
    Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('tata-usaha-show-form-surat');
    Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'storeByTataUsaha'])->name('tata-usaha-store-surat');
    Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-pengajuan-atk-tata-usaha', [SuratController::class, 'storeSuratPengajuanAtkByTataUsaha'])->name('tata-usaha-store-surat-pengajuan-atk');

    // Routes untuk surat masuk, approval, riwayat, dll.
    // ... (lengkap semua routes)
});
```

### 6. Views Layout
**File**: `resources/views/components/layout.blade.php`

**Tambahkan sidebar configuration**:
```php
'tata-usaha' => [
    ['link' => 'tata-usaha', 'title' => 'Dashboard', 'icon' => asset('svg/piechart.svg')],
    ['link' => 'tata-usaha/pengajuan-surat', 'title' => 'Pengajuan Surat', 'icon' => asset('svg/letterpencil.svg')],
    ['link' => 'tata-usaha/riwayat-pengajuan-surat', 'title' => 'Riwayat Pengajuan', 'icon' => asset('svg/letterline.svg')],
    ['link' => 'logout', 'title' => 'Keluar', 'icon' => asset('svg/signout.svg')],
],
```

**Update auth condition**:
```php
@elseif (auth()->user()->role_id == 19)
    @include('components.sidebar-tata-usaha')
```

### 7. Views Tata Usaha
**Direktori**: `resources/views/tata-usaha/`

Semua file views di-copy dari kemahasiswaan dan di-update:
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

**Tambahkan condition untuk tata-usaha**:
```php
if ($surat->jenisSurat->user_type == 'tata-usaha' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-tata-usaha') {
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
$approval->surat->jenisSurat->slug != 'surat-pengajuan-atk-tata-usaha'
```

**Tambahkan stepper condition**:
```php
elseif($surat->jenisSurat->user_type == 'tata-usaha' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-tata-usaha')
    <x-stepper-tata-usaha-pengajuan-atk :surat='$surat' />
```

### 10. Stepper Component
**File**: `resources/views/components/stepper-tata-usaha-pengajuan-atk.blade.php`
- Component untuk tracking status pengajuan ATK
- Di-copy dari kemahasiswaan dan di-update heading

### 11. Breadcrumbs Configuration
**File**: `routes/breadcrumbs.php`

**Tambahkan breadcrumb routes**:
```php
Breadcrumbs::for('tata-usaha-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/tata-usaha/pengajuan-surat');
});

Breadcrumbs::for('tata-usaha-pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('tata-usaha-pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('tata-usaha-show-form-surat', $jenisSurat));
});

Breadcrumbs::for('tata-usaha-riwayat-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Pengajuan Surat', '/tata-usaha/riwayat-pengajuan-surat');
});

Breadcrumbs::for('tata-usaha-show-pengajuan-surat', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('tata-usaha-riwayat-pengajuan-surat');
    $trail->push('Detail Pengajuan Surat', route('show-detail-pengajuan-surat-tata-usaha', $surat));
});
```

### 12. Filament Admin Resource
**File**: `app/Filament/Resources/TataUsahaResource.php`

```php
class TataUsahaResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Tata Usaha';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $slug = 'akun-tata-usaha';
    protected static ?int $navigationSort = 19;

    // Form configuration
    // Table configuration dengan role_id 19 filter
    // Complete CRUD operations
}
```

## 🔧 Konfigurasi Middleware
Role Tata Usaha menggunakan middleware `userAccess:19` untuk mengontrol akses ke semua routes.

## 📊 Dashboard Statistics
Dashboard menampilkan 6 jenis statistik:
- `pengajuanSelesai` - Surat dengan status selesai
- `pengajuanDikirim` - Surat dengan status dikirim
- `pengajuanDitolak` - Surat dengan status ditolak
- `pengajuanDiproses` - Surat diproses yang belum expired
- `pengajuanMenungguDibayar` - Surat menunggu pembayaran
- `pengajuanKadaluarsa` - Surat diproses yang sudah expired

## 🔄 Alur Approval
1. **Tata Usaha** → Submit pengajuan ATK
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

## 🔧 Troubleshooting Common Issues

### 1. 404 Error pada Form Pengajuan
**Issue**: URL `/tata-usaha/pengajuan-surat/surat-pengajuan-atk-tata-usaha` not found
**Solution**: Pastikan:
- Record 'surat-pengajuan-atk-tata-usaha' ada di jenis_surat table
- Route tata-usaha-show-form-surat ada di routes/web.php
- SuratController::create() method memiliki condition untuk tata-usaha

### 2. Undefined Variable Errors
**Issue**: `$daftarJenisSurat` undefined
**Solution**: Update TataUsahaController::riwayatPengajuanSurat() untuk include:
- `'daftarJenisSurat' => JenisSurat::where('user_type', '=', 'tata-usaha')->get()`
- `'daftarStatus' => ['diproses', 'ditolak', 'selesai']`

### 3. Breadcrumb Not Found
**Issue**: `Breadcrumb not found with name "tata-usaha-pengajuan-surat-form"`
**Solution**: Tambahkan semua breadcrumb routes di routes/breadcrumbs.php

## 🔐 Security Considerations
- Semua routes dilindungi middleware `userAccess:19`
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
- [x] Login dengan tata_usaha credentials
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

## 🗂️ Summary of Changes

### Database Level
- [x] Role 'tata-usaha' (ID 19) created
- [x] User 'tata_usaha' created with role_id 19
- [x] JenisSurat 'surat-pengajuan-atk-tata-usaha' created
- [x] ENUM user_type updated to include 'tata-usaha'

### Backend Logic
- [x] TataUsahaController complete implementation
- [x] SuratController ATK submission methods
- [x] AuthController authentication logic
- [x] KabagController approval handling
- [x] Complete routing with middleware protection

### Frontend Views
- [x] Complete tata-usaha views directory
- [x] Form pengajuan ATK with validation
- [x] Riwayat with search & filters
- [x] Dashboard with statistics
- [x] Stepper component for tracking
- [x] Breadcrumb navigation
- [x] Responsive sidebar menu

### Admin Panel
- [x] Filament TataUsahaResource
- [x] Navigation configuration
- [x] CRUD operations for tata-usaha users

Implementasi Tata Usaha ini sekarang fully functional dan siap digunakan dengan semua fitur yang sama persis dengan role Kemahasiswaan, mengikuti best practices dan maintainability standards yang ada.