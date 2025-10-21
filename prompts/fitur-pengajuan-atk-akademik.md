# Fitur Pengajuan ATK untuk Akademik

## Deskripsi
Menambahkan fitur pengajuan surat ATK untuk pengguna dengan role akademik. Fitur ini memungkinkan akademik untuk mengajukan surat ATK yang akan langsung diproses oleh Kabag (tanpa melalui Kaprodi seperti staff).

## Tanggal Implementasi
21 Oktober 2025

## Perubahan yang Dilakukan

### 1. Database Changes

#### Migrasi
- **File**: `database/migrations/2025_10_21_130205_add_akademik_to_user_type_enum_in_jenis_surat_table.php`
- **Deskripsi**: Menambahkan "akademik" ke dalam enum user_type di tabel jenis_surat_tables
- **Perintah**: `php artisan migrate`

#### Seeder
- **File**: `database/seeders/JenisSuratSeeder.php`
- **Deskripsi**: Menambahkan jenis surat baru "surat-pengajuan-atk-akademik" dengan user_type "akademik"
- **Perubahan**: Mengubah dari `create()` menjadi `firstOrCreate()` untuk menghindari duplikasi
- **Perintah**: `php artisan db:seed --class=JenisSuratSeeder`

### 2. Routes

#### Lokasi
- **File**: `routes/web.php`
- **Deskripsi**: Menambahkan routes baru untuk pengajuan surat ATK akademik

#### Routes yang Ditambahkan
```php
// Routes for pengajuan surat by akademik
Route::get('/pengajuan-surat', [AkademikController::class, 'pengajuanSurat'])->name('akademik-pengajuan-surat');
Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->name('akademik-redirect-form-surat');
Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('akademik-show-form-surat');
Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'storeByAkademik'])->name('akademik-store-surat');
Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-pengajuan-atk-akademik', [SuratController::class, 'storeSuratPengajuanAtkByAkademik'])->name('akademik-store-surat-pengajuan-atk');
Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->name('akademik-destroy-surat');
Route::get('/riwayat-pengajuan-surat', [AkademikController::class, 'riwayatPengajuanSurat'])->name('akademik-riwayat-pengajuan-surat');
Route::get('/riwayat-pengajuan-surat/show/{surat}', [AkademikController::class, 'showDetailPengajuanSuratByAkademik'])->name('show-detail-pengajuan-surat-akademik');
```

### 3. Controller Updates

#### AkademikController
- **File**: `app/Http/Controllers/AkademikController.php`
- **Method yang Ditambahkan**:
  - `pengajuanSurat()`: Menampilkan daftar jenis surat yang bisa diajukan oleh akademik
  - `riwayatPengajuanSurat()`: Menampilkan riwayat pengajuan surat akademik dengan filter
  - `showDetailPengajuanSuratByAkademik()`: Menampilkan detail surat yang diajukan oleh akademik

#### SuratController
- **File**: `app/Http/Controllers/SuratController.php`
- **Method yang Ditambahkan**:
  - `storeSuratPengajuanAtkByAkademik()`: Menyimpan pengajuan surat ATK dari akademik
  - `storeByAkademik()`: Method umum untuk menyimpan pengajuan surat dari akademik
- **Perubahan pada method `create()`**: Menambahkan kondisi untuk menampilkan form surat pengajuan ATK akademik

### 4. Views

#### View yang Dibuat
1. **Form Pengajuan**: `resources/views/akademik/formsurat/form-surat-pengajuan-atk.blade.php`
   - Form untuk mengajukan surat ATK
   - Tidak ada field program studi karena akademik tidak mewakili program studi apapun
   - Style konsisten dengan form staff

2. **Halaman Pengajuan**: `resources/views/akademik/pengajuan-surat.blade.php`
   - Halaman untuk memilih jenis surat yang akan diajukan
   - Style konsisten dengan halaman staff

3. **Riwayat Pengajuan**: `resources/views/akademik/riwayat-pengajuan.blade.php`
   - Halaman untuk menampilkan riwayat pengajuan surat
   - Style konsisten dengan halaman staff

4. **Detail Surat**: `resources/views/akademik/show-surat.blade.php`
   - Halaman untuk menampilkan detail surat yang diajukan
   - Style konsisten dengan halaman staff
   - Tidak ada tombol preview/cetak untuk surat pengajuan ATK

#### Component yang Dibuat
- **Stepper**: `resources/views/components/stepper-akademik-pengajuan-atk.blade.php`
  - Component untuk menampilkan alur persetujuan surat
  - Style konsisten dengan stepper-staff-pengajuan-atk
  - Alur: Akademik → Kabag

### 5. Sidebar Update

#### Lokasi
- **File**: `resources/views/components/layout.blade.php`
- **Deskripsi**: Menambahkan menu "Pengajuan Surat" dan "Riwayat Pengajuan" untuk akademik

#### Menu yang Ditambahkan
```php
[
    'link' => 'akademik/pengajuan-surat',
    'title' => 'Pengajuan Surat',
    'icon' => asset('svg/letterpencil.svg'),
    'dropdown' => [],
],
[
    'link' => 'akademik/riwayat-pengajuan-surat',
    'title' => 'Riwayat Pengajuan',
    'icon' => asset('svg/letterline.svg'),
    'dropdown' => [],
],
```

### 6. Breadcrumbs

#### Lokasi
- **File**: `routes/breadcrumbs.php`
- **Deskripsi**: Menambahkan breadcrumbs untuk navigasi di halaman akademik

#### Breadcrumbs yang Ditambahkan
```php
Breadcrumbs::for('akademik-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/akademik/pengajuan-surat');
});

Breadcrumbs::for('akademik-pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('akademik-pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('akademik-show-form-surat', $jenisSurat));
});

Breadcrumbs::for('akademik-riwayat-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Pengajuan Surat', '/akademik/riwayat-pengajuan-surat');
});

Breadcrumbs::for('akademik-show-pengajuan-surat', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('akademik-riwayat-pengajuan-surat');
    $trail->push('Detail Pengajuan Surat', route('show-detail-pengajuan-surat-akademik', $surat));
});
```

## Alur Approval

1. Akademik mengajukan surat ATK
2. Langsung menuju ke Kabag (role_id 17) tanpa melalui Kaprodi seperti staff
3. Kabag dapat menyetujui atau menolak pengajuan

## Perbedaan dengan Staff

1. **Alur Approval**: Akademik langsung ke Kabag, sedangkan Staff melalui Kaprodi terlebih dahulu
2. **Program Studi**: Form akademik tidak ada field program studi karena akademik tidak mewakili program studi apapun
3. **Preview/Cetak**: Surat pengajuan ATK akademik tidak memiliki tombol preview/cetak

## Masalah yang Diperbaiki

1. **Error "Attempt to read property 'id' on null"**: Diperbaiki dengan menghapus field program studi yang tidak relevan untuk akademik
2. **Style Tidak Konsisten**: Diperbaiki dengan menggunakan style yang sama dengan halaman staff
3. **Stepper Tidak Konsisten**: Diperbaiki dengan menggunakan style yang sama dengan stepper-staff-pengajuan-atk
4. **Tombol Preview/Cetak**: Dihapus untuk surat pengajuan ATK yang tidak memiliki preview/cetak

## Cara Mereproduksi Fitur

1. Jalankan migrasi: `php artisan migrate`
2. Jalankan seeder: `php artisan db:seed --class=JenisSuratSeeder`
3. Login sebagai pengguna dengan role akademik
4. Akses menu "Pengajuan Surat" di sidebar
5. Pilih "Surat Pengajuan ATK"
6. Isi form dan ajukan surat

## Catatan Tambahan

- Pastikan pengguna akademik memiliki data yang lengkap (nama, username, email)
- Surat akan kadaluarsa dalam 30 hari jika tidak diproses
- Akademik dapat membatalkan pengajuan selama status masih "diproses"
