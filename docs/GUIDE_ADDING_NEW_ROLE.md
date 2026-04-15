# Panduan Menambahkan Role dan Workflow Baru (ATK)

Dokumen ini merupakan panduan teknis langkah-demi-langkah untuk menambahkan role baru ke dalam sistem **si-surat-fkip**, dengan studi kasus penambahan role **Lab PMIPA** yang memiliki alur pengajuan ATK langsung ke Kabag.

---

## 1. Persiapan Database (Seeder & Migration)

Setiap role baru membutuhkan identitas di database agar sistem dapat mengenali hak aksesnya.

### A. RoleSeeder
Tambahkan role baru ke dalam array `$roles` di `database/seeders/RoleSeeder.php`. Contoh:
```php
['id' => 21, 'name' => 'lab-pmipa'],
```

### B. UserSeeder
Tambahkan user default untuk role tersebut di `database/seeders/UserSeeder.php`:
```php
User::create([
    'name' => 'Lab PMIPA',
    'username' => 'lab_pmipa',
    'email' => 'lab_pmipa@unib.ac.id',
    'password' => Hash::make('password'),
    'role_id' => 21,
]);
```

### C. Migration Enum (User Type)
Penting! Tabel `jenis_surat_tables` menggunakan ENUM untuk kolom `user_type`. Anda harus membuat migration baru untuk menambahkan role baru ke list ENUM tersebut.
```bash
php artisan make:migration add_new_role_to_user_type_enum
```
Isi migration:
```php
DB::statement("ALTER TABLE jenis_surat_tables MODIFY COLUMN user_type ENUM(..., 'new-role') NOT NULL");
```

### D. JenisSuratSeeder
Daftarkan jenis surat yang bisa diajukan oleh role tersebut di `database/seeders/JenisSuratSeeder.php`.

---

## 2. Controller & Logika Bisnis

### A. Buat Controller Role Baru
Buat controller khusus (misal `LabPmipaController.php`) untuk menangani halaman Dashboard, Profil, dan Riwayat Pengajuan. Copy logika dari `UnitKerjasamaController` jika alurnya identik.

### B. Update SuratController.php
- Tambahkan logic di method `create()` untuk mengarahkan ke form Blade yang tepat berdasarkan `slug` surat.
- Tambahkan method `storeByRole()` dan `storeSuratPengajuanAtkByRole()` (jika spesifik ATK) untuk menangani proses simpan ke database.

### C. Update Admin/Penerima Tertentu (Contoh: KabagController)
Jika surat dari role baru ditujukan kepada role lain (contoh: Kabag untuk ATK), pastikan role penerima juga mengenali jenis surat tersebut. Buka controller penerima (misal `app/Http/Controllers/KabagController.php`):
- Di method `showSuratMasuk()`, tambahkan kondisi `if` untuk menampilkan detail surat.
- Contoh:
```php
if ($surat->jenisSurat->user_type == 'new-role' && $surat->jenisSurat->slug == 'surat-baru-anda') {
    return view('kabag.show-surat', [ ... ]);
}
```

### D. Update PDFController.php (Jika Perlu Preview/Cetak PDF)
Jika jenis surat baru memerlukan fitur preview atau cetak PDF (seperti Surat Tugas atau Surat Keterangan), Anda harus mendaftarkannya di `app/Http/Controllers/PDFController.php`.
- Di dalam method `previewSurat()`, tambahkan kondisi `if` baru berdasarkan `slug` surat.
- Tentukan view template yang akan digunakan (biasanya di `resources/views/template/`).
- Contoh:
```php
if ($surat->jenisSurat->slug == 'surat-baru-anda') {
    $pdf = Pdf::loadview('template.surat-baru-anda', ['surat' => $surat])->setPaper('a4', 'potrait');
}
```

### E. Update AuthController.php
Update method `authenticate()` dan `home()` untuk menangani redirect ke dashboard yang sesuai setelah login berdasarkan `role_id`.

---

## 3. Routing & Breadcrumbs

### A. web.php
Buat Route Group dengan middleware `userAccess` sesuai ID role-nya.
```php
Route::prefix('lab-pmipa')->middleware(['userAccess:21'])->group(function () {
    Route::get('/', [LabPmipaController.php, 'dashboard']);
    // ... routes lainnya
});
```

### B. breadcrumbs.php
Definisikan jalur navigasi agar user tidak tersesat. Ikuti pola role yang sudah ada.

---

## 4. User Interface (Views)

### A. Buat Folder View Baru
Buat folder di `resources/views/nama-role/`. Minimal file yang dibutuhkan:
- `dashboard.blade.php`
- `pengajuan-surat.blade.php`
- `riwayat-pengajuan.blade.php`
- `show-surat.blade.php`
- `profile.blade.php`
- `reset-password.blade.php`
- `formsurat/form-surat-pengajuan-atk.blade.php`

### B. Update Sidebar (layout.blade.php)
- Tambahkan menu navigasi di array `$listsData` di `layout.blade.php`.
- Tambahkan kondisi `@elseif ($authUser->role_id == ID)` untuk menampilkan sidebar tersebut.

---

## 5. Manajemen Admin (Filament)

Agar admin bisa mengelola akun role tersebut, buat Filament Resource:
```bash
php artisan make:filament-resource NewRoleResource
```
- Set `$modelLabel` dan `navigationGroup`.
- Filter query di method `table()` agar hanya menampilkan user dengan `role_id` yang relevan.

### F. Penyesuaian View Riwayat (Penerima)
Jika jenis surat tersebut tidak menghasilkan file PDF (hanya formulir digital), Anda perlu menyembunyikan tombol **Cetak** dan **Preview** di halaman riwayat agar tidak terjadi error saat diklik. Contohnya di `resources/views/kabag/show-approval.blade.php`:
- Cari blok `@if` yang membungkus tombol cetak/preview.
- Tambahkan pengecekan `slug` agar tidak menampilkan tombol untuk jenis surat tersebut.
- Contoh:
```php
@if ($approval->surat->jenisSurat->slug != 'surat-baru-anda')
    <!-- Tombol Cetak/Preview -->
@endif
```

---

## 6. Prosedur Deployment & Setup Produksi (SSH)

Deploy ke server produksi (melalui SSH) memerlukan ketelitian agar data tidak hilang. Berikut adalah urutan yang direkomendasikan:

### 1. Build Aset di Lokal
Karena server seringkali tidak memiliki `npm` atau `node`, lakukan build aset di laptop Anda:
```bash
pnpm build
```

### 2. Upload Aset via FTP
Unggah folder `public/build` dari laptop ke server menggunakan FTP/SFTP. Pastikan folder `build` di server terupdate dengan hasil build terbaru Anda.

### 3. SSH & Git Pull
Masuk ke terminal server dan tarik kode terbaru:
```bash
ssh user@ip-server
cd path/to/project
git pull origin main
```

### 4. Database Migration
Jalankan migrasi untuk memperbarui struktur tabel (seperti penambahan ENUM):
```bash
php artisan migrate
```
> [!NOTE]
> Perintah `migrate` aman digunakan di produksi karena hanya mengubah skema (Struktur), bukan menghapus data.

### 5. Setup Data Manual (Rekomendasi Aman)
Menjalankan `db:seed` di produksi sangat berisiko karena dapat menyebabkan duplikasi data atau error ("spamming" akun). Sangat disarankan untuk menginput data role/user/jenis surat baru secara manual melalui dashboard admin:
1. Login ke panel **Filament Admin** (`/admin`).
2. Masuk ke menu **Manajemen Role**: Tambahkan role baru (misal: `lab-pmipa`).
3. Masuk ke menu **Manajemen Akun**: Buat akun baru untuk role tersebut.
4. Masuk ke menu **Jenis Surat**: Tambahkan jenis surat baru (misal: `surat-pengajuan-atk-lab-pmipa`) dan arahkan `user_type`-nya ke role yang baru dibuat.

### 6. Optimasi Akhir
Pastikan cache diperbarui agar route dan config baru terbaca:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Troubleshooting
- **404 Not Found pada Lampiran**: Periksa symlink storage. Jika rusak, jalankan `rm public/storage && php artisan storage:link`.
- **Gagal Login**: Pastikan `role_id` di database sesuai dengan logika yang Anda tulis di `AuthController`.
- **Halaman Detail Kosong (Kabag/Admin)**: Periksa apakah `user_type` dan `slug` surat sudah didaftarkan di Controller penerima (`showSuratMasuk`).
