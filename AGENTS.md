# AGENTS.md — Panduan Komprehensif Arsitektur, Workflow, & Konvensi Sistem si-surat-fkip

> **Tujuan Dokumen**: Dokumen ini dirancang sebagai sumber kebenaran utama (*Single Source of Truth*) dan ringkasan End-to-End (E2E) bagi AI Agent maupun developer berikutnya untuk memahami seluruh konteks proyek, arsitektur role & workflow surat, aturan penomoran, sistem storage, prosedur deployment aman, serta strategi testing tanpa perlu melakukan penelusuran berulang dari nol.

---

## 1. Ikhtisar Proyek & Tech Stack

**si-surat-fkip** adalah Sistem Informasi Pelayanan Tata Persuratan & Disposisi Digital Fakultas Keguruan dan Ilmu Pendidikan (FKIP) Universitas Bengkulu. Sistem ini menangani siklus hidup surat dari pengajuan (oleh Mahasiswa, Staff, atau Unit Kerja), alur verifikasi/disposisi berjenjang antarpimpinan/pejabat, pembubuhan stempel & tanda tangan digital ber-QR Code, hingga pencairan anggaran dan penerbitan berkas dinas resmi.

### Stack Teknologi:
- **Framework Utama**: Laravel 10 (PHP 8.2)
- **Database**: MySQL / MariaDB (Driver `mysql`)
- **Admin Panel**: Filament v3 (`/admin`) dengan Livewire 3
- **Frontend / Views**: Blade Templates, TailwindCSS, Flowbite, Vanilla CSS (`public/styles/`), Alpine.js
- **PDF Generation**: `barryvdh/laravel-dompdf` (Dompdf engine)
- **Object Storage**: Cloudflare R2 (S3-compatible) dengan arsitektur **Dual-Fallback** lokal via `App\Services\StorageHelper`
- **Lingkungan Dev**: Laravel Sail (Docker Compose: `laravel.test`, `mysql`)
- **Testing Engine**: PHPUnit Feature & Unit Tests (eksekusi via Sail: `./vendor/bin/sail artisan test`)

---

## 2. Peta 22 Role & Arsitektur Hak Akses

Sistem menggunakan model `App\Models\User` terpadu dengan kolom `role_id` integer. Untuk integrasi Filament dan kemudahan relasi, digunakan *Single Table Inheritance Proxy Models* di `app/Models/` yang meng-extend `App\Models\User`.

### Tabel Referensi Role ID:

| Role ID | Nama Model / Key | Deskripsi Jabatan / Fungsi | Prefix URL |
|---|---|---|---|
| **1** | `Admin` | Administrator Sistem & Developer | `/admin` (Filament) |
| **2** | `Mahasiswa` | Mahasiswa aktif / Ormawa pengaju surat akademik & kegiatan | `/mahasiswa` |
| **3** | `Staff` | Staff Program Studi (pengaju dinas prodi & verifikator awal mhs) | `/staff` |
| **4** | `Kaprodi` | Koordinator Program Studi (verifikator tingkat prodi) | `/kaprodi` |
| **5** | `WD` / `WD1` | Wakil Dekan Bidang Akademik | `/wd` |
| **6** | `Akademik` | Pengelola Administrasi Akademik Jurusan | `/akademik` |
| **7** | `StaffNilai` | Staff Input Nilai (eksekutor Berita Acara Nilai) | `/staff-nilai` |
| **8** | `Dekan` | Dekan FKIP (penandatangan akhir surat dinas tertentu) | `/dekan` |
| **9** | `WD2` | Wakil Dekan Bidang Keuangan dan Umum | `/wd2` |
| **10** | `WD3` | Wakil Dekan Bidang Kemahasiswaan dan Alumni | `/wd3` |
| **11** | `StaffWD1` | Staff Administrasi Wakil Dekan I | `/staff-wd1` |
| **12** | `StaffWD2` | Staff Administrasi Wakil Dekan II | `/staff-wd2` |
| **13** | `StaffWD3` | Staff Administrasi Wakil Dekan III | `/staff-wd3` |
| **14** | `StaffDekan` | Staff Administrasi Dekanat (penomoran & drafting surat keluar) | `/staff-dekan` |
| **15** | `PengirimLegalisir`| Bagian Logistik & Ekspedisi Pengiriman Ijazah Legalisir | `/pengirim-legalisir` |
| **16** | `AkademikFakultas` | Unit Administrasi Akademik Tingkat Fakultas | `/akademik-fakultas` |
| **17** | `Kabag` | Kepala Bagian Tata Usaha (verifikator ATK & anggaran keuangan) | `/kabag` |
| **18** | `Kemahasiswaan` | Bagian Administrasi Kemahasiswaan Fakultas | `/kemahasiswaan` |
| **19** | `TataUsaha` | Subbagian Umum & Tata Usaha (peminjaman ruang dinas) | `/tata-usaha` |
| **20** | `UnitKerjasama` | Unit Kerjasama Fakultas | `/unit-kerjasama` |
| **21** | `LabPmipa` | Unit Laboratorium Pendidikan MIPA | `/lab-pmipa` |
| **22** | `Bendahara` | Bendahara Keuangan Fakultas (verifikasi akhir & pencairan dana) | `/bendahara` |

### Aturan Proteksi Route & Otentikasi:
- **Web App**: Diproteksi middleware `userAccess:{role_id}` (misal: `Route::prefix('bendahara')->middleware(['userAccess:22'])`).
- **Filament Admin Panel**: Hanya user dengan `role_id == 1` yang memiliki izin akses (`canAccessPanel` di `User.php`).
- **Akun Default Dev**: Akun pengujian lokal dikonfigurasi melalui database seeder.

---

## 3. Struktur Data Surat & Sistem Disposisi (State Machine)

Tabel utama persuratan adalah `surats` dengan skema:
- `id` (UUID, Primary Key)
- `jenis_surat_id` (Foreign Key ke `jenis_surat_tables`)
- `pengaju_id` (Foreign Key ke `users.id`)
- `current_user_id` (Foreign Key ke `users.id` pemegang surat saat ini)
- `penerima_id` (Foreign Key opsional ke target akhir)
- `status`: ENUM (`'menunggu'`, `'diproses'`, `'selesai'`, `'ditolak'`)
- `data`: JSON (di-cast ke `array` di Eloquent model)
- `files`: JSON array (daftar path lampiran pendukung)
- `expired_at`: Datetime opsional (masa berlaku link unduh berkas)

### Konvensi Penyimpanan Array `data`:
1. **Atribut Publik**: Menggunakan gaya `camelCase` (misal: `namaKegiatan`, `noSurat`, `nomorRekening`, `namaBank`, `rab`, dll). Komponen view blade otomatis memisahkan kata berhuruf kapital (`preg_split('/(?=[A-Z])/')`) untuk menampilkan label form secara otomatis.
2. **Stepper Dinamis (`$surat->data['private']['stepper']`)**:
   - Seluruh riwayat persetujuan dicatat ke array integer role ID: `[3, 4, 10, 9, 17, 22]`.
   - Komponen `<x-stepper-flexible :surat="$surat" />` otomatis merender lintasan stepper ini:
     - Hijau centang: Pihak yang telah menyetujui.
     - Biru/Kuning: Pejabat yang sedang memegang berkas (`$surat->current_user`).
     - Merah silang: Pejabat yang menolak berkas.

---

## 4. Katalog Alur Kerja (Workflow) Surat

### A. Alur Surat Lama (Legacy Workflows)
1. **Surat Akademik Mahasiswa** (*Aktif Kuliah, SKL, Rekomendasi, dll*):
   $$\text{Mahasiswa} \longrightarrow \text{Staff Prodi} \longrightarrow \text{Kaprodi} \longrightarrow \text{Akademik Jurusan (SELESAI)}$$
2. **Berita Acara Nilai (BAN)**:
   $$\text{Staff Prodi} \longrightarrow \text{Kaprodi} \longrightarrow \text{WD 1} \longrightarrow \text{Staff Nilai (SELESAI)}$$
3. **Surat Tugas Dosen/Staff**:
   $$\text{Staff Prodi} \longrightarrow \text{Kaprodi} \longrightarrow \text{Staff Dekan} \longrightarrow \text{Dekan / WD (SELESAI)}$$
4. **Pengajuan ATK**:
   - Jalur Prodi: $\text{Staff} \longrightarrow \text{Kaprodi} \longrightarrow \text{Kabag TU (SELESAI)}$
   - Jalur Unit Nonprodi: $\text{Unit (TU/Lab/dll)} \longrightarrow \text{Kabag TU (SELESAI)}$
5. **Legalisir Ijazah**:
   $$\text{Alumni/Mhs} \longrightarrow \text{Akademik} \longrightarrow \begin{cases} \text{Diambil langsung (SELESAI)} \\ \text{Dikirim via Pengirim Legalisir (SELESAI)} \end{cases}$$
6. **Surat Keluar Dekanat**:
   $$\text{Staff Dekan (Drafting)} \longrightarrow \text{Dekan / WD (Tanda Tangan \& SELESAI)}$$

### B. Alur 3 Surat Baru (Fitur Terbaru 2026)
1. **Surat Usulan Pengajuan Dana** (`surat-pencairan-dana` / `surat-pencairan-dana-mahasiswa`):
   - **Jalur Staff**:
     $$\text{Staff Prodi} \longrightarrow \text{Kaprodi} \longrightarrow \text{WD 2 (Keuangan)} \longrightarrow \text{Kabag TU} \longrightarrow \text{Bendahara (Pencairan \& SELESAI)}$$
   - **Jalur Mahasiswa / Ormawa**:
     $$\text{Mahasiswa} \longrightarrow \text{Kaprodi} \longrightarrow \text{WD 3 (Kemhs)} \longrightarrow \text{WD 2} \longrightarrow \text{Kabag TU} \longrightarrow \text{Bendahara (SELESAI)}$$
   - *Catatan Penting*: Penandatangan resmi di dokumen cetak PDF adalah **Koordinator Program Studi** (nama dan NIP Kaprodi yang menyetujui), bukan WD 2. Kolom rekening bank **tidak dicantumkan** di dokumen PDF resmi (hanya ada di internal sistem untuk verifikasi pencairan oleh Bendahara).
2. **Surat Peminjaman Ruang** (`surat-peminjaman-ruang` / `surat-peminjaman-ruang-mahasiswa`):
   - **Jalur Staff**:
     $$\text{Staff Prodi} \longrightarrow \text{Kaprodi} \longrightarrow \text{WD 2} \longrightarrow \text{Tata Usaha (SELESAI)}$$
   - **Jalur Mahasiswa**:
     $$\text{Mahasiswa} \longrightarrow \text{Kaprodi} \longrightarrow \text{WD 3} \longrightarrow \text{WD 2} \longrightarrow \text{Tata Usaha (SELESAI)}$$
3. **Surat Permohonan Menjadi Narasumber** (`surat-permohonan-narasumber`):
   $$\text{Staff Prodi} \longrightarrow \text{Kaprodi} \longrightarrow \text{Staff Dekan (Penomoran \& SELESAI)}$$

---

## 5. Aturan Penomoran Surat & Standar Cetak PDF (WYSIWYG)

1. **Prinsip Murni WYSIWYG & Bebas Hardcode**:
   - Jangan pernah menambahkan suffix kode surat statis di dalam file blade template PDF (seperti `.../UN30.7/...`).
   - Jika nomor surat kosong/belum terisi: Cetak murni **titik-titik panjang dinas** (`....................................................`).
   - Jika nomor surat diawali tanda slash `/` (misal staf hanya mengisi kode seperti ` /DST/UN30.7.11/KU.01.02/2026` karena nomor urut angka masih kosong): Sistem via helper `formatNomorSurat()` secara otomatis membubuhkan titik-titik dinas di depannya (`......../DST/...`) sebagai tempat pengisian manual atau cap nomor.
   - Jika nomor surat diisi lengkap dengan angka (misal `123/UN30...`): Cetak apa adanya sesuai input pengguna.
2. **Tombol Auto-Fill Standar di Form Persetujuan**:
   - Disediakan tombol pembantu `[📋 Gunakan Format: /...]` pada form verifikasi Staff Dekan, Tata Usaha, dan form staf pengajuan dana.
   - Suffix acuan per jenis surat:
     - Ajuan Dana: `/UN30.7.11/KU.01.02/{tahun}`
     - Peminjaman Ruang: `/UN30.7.11/PP/{tahun}`
     - Permohonan Narasumber: `/UN30.7.10/DT.06/{tahun}`
     - Surat Tugas: `/UN30.7/KP/{tahun}`
     - Surat Keluar: `/UN30.7/PP/{tahun}`
3. **Validasi Strict Format Nomor**:
   - Jika verifikator memilih untuk mengisi nomor surat, input wajib memuat karakter slash (`/`) agar tidak menghasilkan nomor gundul tanpa kode instansi.
4. **Backward Compatibility Arsip Lama**:
   - Template `surat-tugas.blade.php`, `surat-tugas-kelompok.blade.php`, `surat-keluar.blade.php`, serta preview QR (`show-surat-qr.blade.php`) memiliki fallback cerdas: Jika nomor lama tidak memiliki slash, sistem secara otomatis melampirkan suffix kode agar cetakan arsip masa lalu tetap utuh.

---

## 6. Universal RAB & Hierarki Subkegiatan Pengajuan Dana

Form pengajuan dana staf (`resources/views/staff/formsurat/form-surat-pencairan-dana.blade.php`) menggunakan Alpine.js builder yang mendukung 2 skema data:
1. **Mode Flat**: Mengisi Uraian Kegiatan dan Jumlah Total (Rp) secara langsung.
2. **Mode Subkegiatan**: Mengisi Kegiatan Utama, lalu menambah rincian subkegiatan:
   - Uraian Subkegiatan (misal: "Honorarium Narasumber")
   - Volume & Satuan (misal: "2 Orang/Jam")
   - Harga Satuan (Rp)
   - Otomatis menghitung Subtotal per item dan Akumulasi Grand Total.
3. **Mata Anggaran Kegiatan (MAK)**:
   - Kolom MAK bersifat opsional dan diposisikan di **sebelah kanan** kolom Uraian Kegiatan/Subkegiatan.
   - Di cetakan PDF (`surat-ajuan-dana.blade.php`), kolom MAK hanya muncul jika ada minimal 1 baris yang mengisinya.
4. **Format & Ukuran Font Tabel**:
   - Subkegiatan dicetak berindentasi rapi tanpa subnomor `1.1` (menggunakan tanda strip `-`).
   - Ukuran font header, baris isi, dan footer tabel distandarisasi tepat **12pt** (sama dengan teks badan surat dinas).

---

## 7. Sistem Penyimpanan Berkas & Dual-Fallback Storage

Aplikasi menggunakan wrapper service `App\Services\StorageHelper`:
```
Permintaan File
      │
      ▼
Cek Auth & Signature Valid? ──(Tidak)──► 403 / 401 Unauthorized
      │ (Ya)
      ▼
Ada di Cloudflare R2? ───────(Ada)────► Stream via S3 API
      │ (Belum Ada)
      ▼
Ada di Disk Lokal Hosting? ──(Ada)────► Stream File Lokal
      │ (Tidak Ada)
      ▼
404 Not Found
```
- **Pemberian Tautan**: Selalu gunakan `URL::signedRoute('show-file...', ['surat' => $id, 'filename' => $file])`. **JANGAN** menggunakan `asset('storage/...')` langsung, karena berkas di R2 bersifat private dan akan menghasilkan error 404 jika diakses tanpa stream backend.
- **Migrasi Berkas**: Disediakan perintah artisan `php artisan storage:migrate-to-r2` untuk menyalin berkas lokal ke bucket R2.

---

## 8. Panduan Filament Admin (`/admin`)

Untuk mengelola user dan konfigurasi sistem:
- **URL Admin**: `/admin` (Login khusus `role_id = 1`).
- **Resource Akun**: Terletak di grup navigasi `Manajemen Akun`.
- **Resource Bendahara**:
  - Model: `App\Models\Bendahara` (Proxy `User`)
  - Resource: `App\Filament\Resources\BendaharaResource`
  - URL Slug: `/admin/akun-bendahara`
  - Navigation Sort: `23`, Icon: `heroicon-o-banknotes`
  - Aksi: Full CRUD (List, Create, Edit, Delete). Password otomatis di-hash Bcrypt dan role ID otomatis diset ke `22`.

---

## 9. Prosedur Deployment & Setup Server Production

> [!CAUTION]
> **PERINGATAN KERAS**: **JANGAN PERNAH** menjalankan `php artisan migrate:fresh` atau `php artisan migrate:refresh` di server production. Perintah tersebut akan menghapus total database yang sudah berjalan!

### Opsi 1: Setup Manual Melalui Filament Admin (Paling Aman, Zero-Script, & Password Terlindungi)
Metode ini **sangat direkomendasikan** untuk menghindari risiko salah eksekusi seeder dan menjaga kerahasiaan kata sandi:
1. Masuk ke server production dan tarik kode terbaru:
   ```bash
   git pull origin main
   php artisan optimize:clear
   php artisan optimize
   ```
2. Login ke Dashboard Filament Admin: `https://domain-anda/admin`.
3. Buka menu **Developer Tools** $\rightarrow$ **Role**: Buat role `bendahara` (Deskripsi: `Bendahara`).
4. Buka menu **Manajemen Akun** $\rightarrow$ **Bendahara**: Klik **New Bendahara**, isi data pejabat dan **masukkan password kuat langsung di form**. Password akan langsung di-hash tanpa pernah tersimpan plaintext di git.
5. Buka menu **Developer Tools** $\rightarrow$ **Jenis Surat**: Daftarkan jenis surat baru jika belum ada (`surat-pencairan-dana`, `surat-permohonan-narasumber`, `surat-peminjaman-ruang`).

### Opsi 2: Menggunakan Artisan Seeder Idempoten
Jika menggunakan seeder di server:
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=JenisSuratSeeder
php artisan db:seed --class=UserSeeder
php artisan optimize:clear
php artisan optimize
```
*Catatan*: Seeder menggunakan `firstOrCreate`, sehingga aman dan tidak akan menimpa data yang telah ada.

---

## 10. Panduan Pengujian (Testing Strategy)

Proyek ini memiliki suite automated test berbasis PHPUnit yang komprehensif. **Seluruh fitur baru wajib lulus 100% sebelum dirilis**.

### Cara Menjalankan Test Suite (via Laravel Sail):
```bash
# Menjalankan seluruh test suite (40 tests, 377 assertions)
./vendor/bin/sail artisan test

# Menjalankan test modul spesifik
./vendor/bin/sail artisan test --filter=BendaharaFilamentResourceTest
./vendor/bin/sail artisan test --filter=SuratPencairanDanaTest
./vendor/bin/sail artisan test --filter=SuratPeminjamanRuangTest
./vendor/bin/sail artisan test --filter=SuratPermohonanNarasumberTest
./vendor/bin/sail artisan test --filter=SuratNumberingAndUniversalBudgetTest
./vendor/bin/sail artisan test --filter=SuratPdfPreviewTest
./vendor/bin/sail artisan test --filter=LegacySuratRegressionTest
```

### File-File Test Kunci & Cakupannya:
1. `tests/Feature/BendaharaFilamentResourceTest.php`: Proteksi hak akses admin, rendering list, formulir Livewire Create/Edit/Delete Bendahara di Filament.
2. `tests/Feature/SuratPencairanDanaTest.php`: Alur lengkap pengajuan dana (Staff & Mahasiswa), approval chain Kaprodi $\rightarrow$ WD $\rightarrow$ Kabag $\rightarrow$ Bendahara, tombol pencairan, dan penolakan.
3. `tests/Feature/SuratPeminjamanRuangTest.php`: Alur peminjaman ruang ke Tata Usaha dan validasi tanggal kegiatan.
4. `tests/Feature/SuratPermohonanNarasumberTest.php`: Alur permohonan narasumber ke Staff Dekan dan form validation.
5. `tests/Feature/SuratNumberingAndUniversalBudgetTest.php`: Validasi nomor bergaris miring, titik-titik dinas, dan struktur hierarki RAB.
6. `tests/Feature/SuratPdfPreviewTest.php`: Rendering Dompdf untuk memastikan tidak ada tag blade rusak atau error 500 saat cetak.
7. `tests/Feature/LegacySuratRegressionTest.php`: Menjamin alur surat lama tetap berjalan normal tanpa efek samping.

---

## 11. Indeks Dokumen Terkait (`docs/`)

Jika membutuhkan rincian teknis yang sangat spesifik, rujuk dokumen berikut:
- **[`docs/WALKTHROUGH_FITUR_BARU.md`](docs/WALKTHROUGH_FITUR_BARU.md)**: Riwayat lengkap 20 perbaikan bug, keluhan visual, tabel RAB, penomoran surat, dan Filament Bendahara.
- **[`docs/DOCUMENTATION_WORKFLOW.md`](docs/DOCUMENTATION_WORKFLOW.md)**: Diagram alur kerja surat versi awal.
- **[`docs/GUIDE_ADDING_NEW_ROLE.md`](docs/GUIDE_ADDING_NEW_ROLE.md)**: Panduan langkah demi langkah saat menambahkan role baru di sistem.
- **[`docs/GUIDE_ADDING_NEW_MAHASISWA_LETTER.md`](docs/GUIDE_ADDING_NEW_MAHASISWA_LETTER.md)**: Panduan menambahkan jenis surat khusus mahasiswa.
- **[`docs/MIGRATION_STORAGE_R2.md`](docs/MIGRATION_STORAGE_R2.md)**: Arsitektur Cloudflare R2 dan penggunaan command migrasi storage.
