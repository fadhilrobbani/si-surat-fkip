## Overview

Ini adalah proyek sistem informasi tata persuratan dan disposisi digital untuk FKIP Universitas Bengkulu berbasis website dengan framework Laravel 10.

## Requirement

Pastikan sudah terinstall komponen berikut di lingkungan pengembangan:

1. PHP 8.2^ (atau PHP 8.1 ke atas)
2. NodeJS 18.^ / 16.^ & PNPM / NPM
3. Composer
4. MySQL / MariaDB
5. Docker (opsional, jika menggunakan Laravel Sail)

---

## Cara Install

Ada dua metode instalasi: menggunakan Docker (Laravel Sail) atau menggunakan lingkungan *native* (XAMPP/Laragon). **Direkomendasikan menggunakan Docker** untuk menghindari konflik/masalah versi PHP.

### Metode 1: Menggunakan Docker (Laravel Sail - Direkomendasikan)

Pastikan **Docker Desktop** atau **Docker Engine** sudah menyala di perangkat Anda.

1. Clone repository ini:
   ```bash
   git clone https://github.com/fadhilrobbani/si-surat-fkip.git
   cd si-surat-fkip
   ```
2. Salin `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
3. Jalankan *container sementara* untuk menginstall dependensi composer awal secara aman:
   ```bash
   docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
   ```
4. Jalankan kontainer utama di mode background:
   ```bash
   ./vendor/bin/sail up -d
   ```
5. Susun *app key*, basis data, dan asset frontend:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate --seed
   ./vendor/bin/sail pnpm install
   ./vendor/bin/sail pnpm build
   ```
   *Catatan: Jika koneksi MySQL di dalam Docker konflik dengan port host lokal, sesuaikan `FORWARD_DB_PORT` di `.env` ke port alternatif (contoh: 3307), dan pastikan `DB_HOST=mysql`.*
6. Buka *website* di `http://localhost/`.

---

### Metode 2: Manual / Native

1. Clone repository ini:
   ```bash
   git clone https://github.com/fadhilrobbani/si-surat-fkip.git
   cd si-surat-fkip
   ```
2. Buat file `.env` dengan menyalin isi `.env.example`:
   ```bash
   cp .env.example .env
   ```
3. Sesuaikan konfigurasi database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) di file `.env`.
4. Pastikan server MySQL sudah aktif (via XAMPP, Laragon, atau native).
5. Jalankan instalasi dependensi backend & frontend:
   ```bash
   composer install
   pnpm install
   pnpm build
   ```
6. Generate app key & jalankan migrasi database lokal:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
7. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
8. Buka alamat aplikasi di browser (biasanya `http://127.0.0.1:8000/`).

---

## Troubleshooting & Symlink Storage

Saat mengembangkan menggunakan **Laravel Sail (Docker)** terutama di Windows (WSL), link `public/storage` terkadang rusak atau berubah menjadi file teks biasa, sehingga berkas lampiran menghasilkan error 404.

**Cara Memperbaiki:**
```bash
# Hapus link lama yang rusak
rm public/storage

# Buat ulang symlink via artisan
./vendor/bin/sail php artisan storage:link
```
Pastikan status link sudah benar dengan menjalankan `ls -la public/storage`. Link yang benar haruslah menunjuk ke folder `storage/app/public`.

---

## Prosedur Deployment Aman ke Server Production

> [!CAUTION]
> **PERINGATAN KERAS**: **JANGAN PERNAH** menjalankan `php artisan db:wipe`, `php artisan migrate:fresh`, atau `php artisan migrate:refresh` di server production! Perintah tersebut akan menghapus seluruh data pengguna dan riwayat surat yang ada!

### Langkah Update di Server Production:

1. **Tarik Kode Terbaru**:
   ```bash
   git pull origin main
   ```

2. **Perbarui Dependensi & Build Asset Frontend**:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

3. **Jalankan Migrasi Database Aman**:
   ```bash
   php artisan migrate --force
   ```
   *(Perintah `migrate` hanya menambah/mengubah skema baru tanpa menghapus data yang ada).*

4. **Konfigurasi Akun & Layanan Baru**:
   - **Metode Terbaik (Zero-Script via Filament Admin)**: Login ke `https://domain-anda/admin`, lalu kelola role, jenis surat, dan akun pimpinan/pejabat baru (misal Bendahara) langsung di halaman admin. Password diinput secara rahasia dan langsung di-hash tanpa pernah terekspos di file git.
   - **Metode Seeder (Idempoten)**: Jika menggunakan seeder di server:
     ```bash
     php artisan db:seed --class=RoleSeeder
     php artisan db:seed --class=JenisSuratSeeder
     php artisan db:seed --class=UserSeeder
     ```

5. **Optimasi & Bersihkan Cache**:
   ```bash
   php artisan optimize:clear
   php artisan optimize
   ```

6. **Konfigurasi Email / SMTP (di `.env` Server Production)**:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp-relay.brevo.com
   MAIL_PORT=587
   MAIL_USERNAME=youremail@example.com
   MAIL_PASSWORD=your_secure_smtp_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=youremail@example.com
   MAIL_FROM_NAME="FKIP UNIB"
   ```
   *(Gunakan nilai dummy / placeholder di repositori publik, isi kredensial asli HANYA pada file `.env` di server production).*

---

## Panduan Akun Default (Dev/Testing)

1. Akun untuk pengujian di lingkungan pengembangan lokal dikonfigurasi melalui seeder database.
2. Seluruh pejabat fakultas, staff prodi, dan mahasiswa login melalui halaman portal utama `/login`.
3. Administrator sistem login melalui panel Filament Admin di `/admin`.

---

## Indeks Dokumentasi

Untuk detail arsitektur sistem, alur surat, dan panduan teknis mendalam:
1. [**AGENTS.md**](AGENTS.md) — Panduan komprehensif arsitektur, 22 role, workflow lengkap, sistem penomoran WYSIWYG, dan testing.
2. [**Catatan Fitur & Walkthrough**](docs/WALKTHROUGH_FITUR_BARU.md) — Riwayat 20 perbaikan bug, keluhan visual, tabel RAB, penomoran surat, dan Filament Bendahara.
3. [**Panduan Alur Kerja (Workflow) Surat**](docs/DOCUMENTATION_WORKFLOW.md) — Diagram alur kerja surat versi awal.
4. [**Panduan Menambahkan Role Baru**](docs/GUIDE_ADDING_NEW_ROLE.md) — Langkah teknis pembuatan role dan hak akses baru.
5. [**Panduan Menambahkan Jenis Surat Mahasiswa**](docs/GUIDE_ADDING_NEW_MAHASISWA_LETTER.md) — Cara menambahkan surat akademik baru.
6. [**Panduan Migrasi Storage Cloudflare R2**](docs/MIGRATION_STORAGE_R2.md) — Arsitektur dual-fallback storage dan perintah sinkronisasi.
