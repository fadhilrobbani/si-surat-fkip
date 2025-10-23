# Fitur Pengajuan ATK untuk Akademik dan Akademik Fakultas

## Deskripsi

Menambahkan fitur pengajuan Surat ATK untuk pengguna dengan role akademik dan akademik-fakultas. Fitur ini memungkinkan kedua role tersebut untuk mengajukan surat ATK yang akan langsung diproses oleh Kabag (tanpa melalui Kaprodi seperti Staff).

## Tanggal Implementasi

21-22 Oktober 2025

## Perubahan yang Dilakukan

### 1. Perubahan Database

#### Migrasi

| File | Deskripsi | Perintah |
|------|-----------|----------|
| `database/migrations/2025_10_21_130205_add_akademik_to_user_type_enum_in_jenis_surat_table.php` | Menambahkan "akademik" ke dalam enum user_type di tabel jenis_surat_tables. | `php artisan migrate` |
| `database/migrations/2025_10_21_140236_add_akademik_fakultas_to_user_type_enum_in_jenis_surat_table.php` | Menambahkan "akademik_fakultas" ke dalam enum user_type di tabel jenis_surat_tables. | `php artisan migrate` |

#### Seeder

| File | Deskripsi | Perintah |
|------|-----------|----------|
| `database/seeders/JenisSuratSeeder.php` | Menambahkan jenis surat baru: surat-pengajuan-atk-akademik (user_type: "akademik") dan surat-pengajuan-atk-akademik-fakultas (user_type: "akademik_fakultas"). Mengubah dari create() menjadi firstOrCreate() untuk menghindari duplikasi. | `php artisan db:seed --class=JenisSuratSeeder` |

### 2. Routes

**Lokasi:** `routes/web.php`

#### Routes untuk Role Akademik

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

#### Routes untuk Role Akademik Fakultas

```php
// Routes for pengajuan surat by akademik-fakultas
Route::get('/pengajuan-surat', [AkademikFakultasController::class, 'pengajuanSurat'])->name('akademik-fakultas-pengajuan-surat');
Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->name('akademik-fakultas-redirect-form-surat');
Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('akademik-fakultas-show-form-surat');
Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'storeByAkademikFakultas'])->name('akademik-fakultas-store-surat');
Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-pengajuan-atk-akademik-fakultas', [SuratController::class, 'storeSuratPengajuanAtkByAkademikFakultas'])->name('akademik-fakultas-store-surat-pengajuan-atk');
Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->name('akademik-fakultas-destroy-surat');
Route::get('/riwayat-pengajuan-surat', [AkademikFakultasController::class, 'riwayatPengajuanSurat'])->name('akademik-fakultas-riwayat-pengajuan-surat');
Route::get('/riwayat-pengajuan-surat/show/{surat}', [AkademikFakultasController::class, 'showDetailPengajuanSuratByAkademikFakultas'])->name('show-detail-pengajuan-surat-akademik-fakultas');
```

### 3. Controller Updates

| Controller | Method Ditambahkan | Method Diperbarui/Dimodifikasi |
|------------|-------------------|------------------------------|
| `AkademikController.php` | `pengajuanSurat()`, `riwayatPengajuanSurat()`, `showDetailPengajuanSuratByAkademik()` | N/A |
| `AkademikFakultasController.php` | `pengajuanSurat()`, `riwayatPengajuanSurat()`, `showDetailPengajuanSuratByAkademikFakultas()` | N/A |
| `SuratController.php` | `storeSuratPengajuanAtkByAkademik()`, `storeSuratPengajuanAtkByAkademikFakultas()`, `storeByAkademik()`, `storeByAkademikFakultas()` | `create()`: Menambahkan kondisi untuk menampilkan form surat ATK akademik/fakultas. |
| `KabagController.php` | N/A | `showSuratMasuk()`: Menambahkan kondisi untuk menampilkan surat pengajuan ATK akademik-fakultas. |
| `JenisSuratController.php` | N/A | `redirectToFormSurat()`: Menggunakan `auth()->user()->role->name` bukan `jenisSurat->user_type` untuk membuat URL yang benar. |

### 4. Views

#### View Baru (Akademik)

- **Form Pengajuan ATK:** `resources/views/akademik/formsurat/form-surat-pengajuan-atk.blade.php` (Style konsisten, tanpa field Program Studi).
- **Halaman Pengajuan:** `resources/views/akademik/pengajuan-surat.blade.php`
- **Riwayat Pengajuan:** `resources/views/akademik/riwayat-pengajuan.blade.php`
- **Detail Surat:** `resources/views/akademik/show-surat.blade.php` (Tidak ada tombol preview/cetak).

#### View Baru (Akademik Fakultas)

- **Form Pengajuan ATK:** `resources/views/akademik-fakultas/formsurat/form-surat-pengajuan-atk.blade.php` (Style konsisten, tanpa field Program Studi).
- **Halaman Pengajuan:** `resources/views/akademik-fakultas/pengajuan-surat.blade.php`
- **Riwayat Pengajuan:** `resources/views/akademik-fakultas/riwayat-pengajuan.blade.php`
- **Detail Surat:** `resources/views/akademik-fakultas/show-surat.blade.php` (Tidak ada tombol preview/cetak).

#### Component Baru (Stepper)

- **Stepper Akademik:** `resources/views/components/stepper-akademik-pengajuan-atk.blade.php` (Alur: Akademik → Kabag).
- **Stepper Akademik Fakultas:** `resources/views/components/stepper-akademik-fakultas-pengajuan-atk.blade.php` (Alur: Akademik Fakultas → Kabag).

#### View Diperbarui (Kabag)

- **Show Surat:** `resources/views/kabag/show-surat.blade.php` (Menambahkan kondisi untuk menampilkan stepper akademik/fakultas).
- **Show Approval:** `resources/views/kabag/show-approval.blade.php` (Menambahkan kondisi untuk menampilkan stepper akademik/fakultas).

### 5. Sidebar Update

**Lokasi:** `resources/views/components/layout.blade.php`

#### Menu Ditambahkan (Akademik)

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

#### Menu Ditambahkan (Akademik Fakultas)

```php
[
    'link' => 'akademik-fakultas/pengajuan-surat',
    'title' => 'Pengajuan Surat',
    'icon' => asset('svg/letterpencil.svg'),
    'dropdown' => [],
],
[
    'link' => 'akademik-fakultas/riwayat-pengajuan-surat',
    'title' => 'Riwayat Pengajuan',
    'icon' => asset('svg/letterline.svg'),
    'dropdown' => [],
],
```

### 6. Breadcrumbs

**Lokasi:** `routes/breadcrumbs.php`

#### Breadcrumbs Akademik

```php
Breadcrumbs::for('akademik-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/akademik/pengajuan-surat');
});

Breadcrumbs::for('akademik-pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('akademik-pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('akademik-show-form-surat', $jenisSurat));
});

// ... Riwayat and Show Detail
```

#### Breadcrumbs Akademik Fakultas

```php
Breadcrumbs::for('akademik-fakultas-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/akademik-fakultas/pengajuan-surat');
});

Breadcrumbs::for('akademik-fakultas-pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('akademik-fakultas-pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('akademik-fakultas-show-form-surat', $jenisSurat));
});

// ... Riwayat and Show Detail
```

## Alur Approval dan Perbedaan

### Alur Approval

| Role | Alur | Tindakan Kabag |
|------|------|----------------|
| Akademik | Akademik → Kabag (role_id 17) | Menyetujui atau menolak pengajuan. |
| Akademik Fakultas | Akademik-fakultas → Kabag (role_id 17) | Menyetujui atau menolak pengajuan. |

### Perbedaan dengan Staff

- **Alur Approval:** Akademik/Akademik Fakultas langsung ke Kabag, sedangkan Staff melalui Kaprodi terlebih dahulu.
- **Program Studi:** Form pengajuan ATK untuk Akademik/Akademik Fakultas tidak memiliki field program studi.
- **Preview/Cetak:** Surat pengajuan ATK Akademik/Akademik Fakultas tidak memiliki tombol preview/cetak.

## Masalah yang Diperbaiki

1. **Error "Attempt to read property 'id' on null":** Diperbaiki dengan menghapus field program studi yang tidak relevan.
2. **Style Tidak Konsisten & Stepper Tidak Konsisten:** Diperbaiki dengan menggunakan style yang sama dengan halaman Staff.
3. **Tombol Preview/Cetak:** Dihapus untuk surat pengajuan ATK yang tidak memiliki fitur preview/cetak.
4. **URL Salah:** Diperbaiki di JenisSuratController dengan menggunakan `auth()->user()->role->name`.
5. **Stepper Tidak Muncul di Kabag:** Diperbaiki dengan menambahkan kondisi tampilan stepper di view Kabag.
6. **Status "Ditolak" Tidak Ditampilkan:** Diperbaiki dengan memindahkan pengecekan status "ditolak" ke atas dalam stepper.

## Cara Mereproduksi Fitur

### Jalankan Migrasi & Seeder:

```bash
php artisan migrate
php artisan db:seed --class=JenisSuratSeeder
```

### Uji Role Akademik:

1. Login sebagai pengguna dengan role akademik.
2. Akses menu "Pengajuan Surat" di sidebar.
3. Pilih "Surat Pengajuan ATK", isi formulir, dan ajukan.

### Uji Role Akademik Fakultas:

1. Login sebagai pengguna dengan role akademik-fakultas.
2. Akses menu "Pengajuan Surat" di sidebar.
3. Pilih "Surat Pengajuan ATK", isi formulir, dan ajukan.

## Catatan Tambahan

- Pastikan pengguna akademik dan akademik-fakultas memiliki data yang lengkap.
- Surat akan kadaluarsa dalam 30 hari jika tidak diproses.
- Akademik dan akademik-fakultas dapat membatalkan pengajuan selama status masih "diproses".
