## Overview

Ini adalah proyek sistem surat menyurat untuk FKIP UNIB berbasis website dengan framework laravel.

## Requirement

Pastikan sudah terinstall komponen berikut:

1. PHP 8.1^ (versi 8.1 ke atas)
2. nodeJS 16.^ (versi 16 ke atas)
3. Composer
4. MySQL

## Cara Install

Ada dua metode instalasi: menggunakan lingkungan *native* (XAMPP/Laragon) atau menggunakan Docker (Laravel Sail). **Direkomendasikan menggunakan Docker** untuk menghindari konflik/masalah versi PHP.

### Metode 1: Menggunakan Docker (Laravel Sail - Direkomendasikan)

Pastikan **Docker Desktop** atau **Docker Engine** sudah menyala di laptopmu.

1. Clone repository ini `git clone https://github.com/fadhilrobbani/si-surat-fkip.git`.
2. Masuk ke direktori `si-surat-fkip` dan copy `.env.example` menjadi `.env`.
3. Jalankan *container sementara* untuk menginstall dependensi composer awal secara aman:
   ```bash
   docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
   ```
4. Jalankan kontainer utama di mode background:
   ```bash
   ./vendor/bin/sail up -d
   ```
5. Susun *key*, basis data, dan modul eksternal:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate --seed
   ./vendor/bin/sail pnpm install
   ./vendor/bin/sail pnpm build
   ```
   *Catatan: Jika koneksi MySQL di dalam Docker konflik dengan host lokal, pastikan `FORWARD_DB_PORT` di `.env` diset ke port aman (contoh: 3307), dan ganti `DB_HOST=mysql`.*
6. Buka *website* di `http://localhost/`.
 
 ## 7. Sistem Environment & Troubleshooting (Laravel Sail)
 
 Saat mengembangkan menggunakan **Laravel Sail (Docker)** terutama di lingkungan Windows (WSL), terdapat beberapa masalah umum terkait *Symbolic Link* (Penyimpanan).
 
 ### Masalah Symlink `public/storage`
 Jika Anda baru saja melakukan *clone* repository atau berpindah branch, link `public/storage` seringkali rusak atau berubah menjadi file teks biasa (bukan folder alias). Hal ini menyebabkan file lampiran muncul sebagai **404 Not Found** atau URL mengandung `/file-tidak-ditemukan/`.
 
 **Cara Memperbaiki:**
 Jalankan perintah berikut di terminal (di luar container atau via sail):
 ```bash
 # Hapus link lama yang rusak
 rm public/storage
 
 # Buat ulang link di dalam container
 ./vendor/bin/sail php artisan storage:link
 ```
 
 Pastikan status link sudah benar dengan menjalankan `ls -la public/storage`. Link yang benar haruslah menunjuk ke `/var/www/html/storage/app/public`.
 
 ### Metode 2: Manual / Native

1. Clone repository ini `git clone https://github.com/fadhilrobbani/si-surat-fkip.git`.
2. Masuk ke direktori si-surat-fkip. Buat file `.env` di level direktori paling atas lalu *copy* isi file `.env.example` ke dalam `.env`.
3. Ganti nilai `DB_DATABASE` di file `.env` sesuai nama database yang kamu siapkan.
4. Pastikan server mysql sudah menyala di port 3306 (via XAMPP, dsb).
5. Jalankan `composer install`
6. Jalankan `pnpm install` (pastikan nodejs sudah terinstall)
7. Jalankan `pnpm build`
8. Jalankan `php artisan key:generate`
9. Jalankan `php artisan migrate --seed`
10. Jalankan server lokal: `php artisan serve` & `pnpm dev`.
11. Buka alamatnya di browser (biasanya di `http://127.0.0.1:8000/`)

## catatan untuk deployment

0. update dengan `git pull`
1. atur server smtp untuk layanan email

```
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=fkipunivbengkulu@gmail.com
MAIL_PASSWORD=(rahasia)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=fkipunivbengkulu@gmail.com
MAIL_FROM_NAME="FKIP UNIB"
```

2. jalankan `npm install` lagi
3. Jalankan perintah `npm run build` untuk meletakkan hasil build vite ke dalam folder public
4. jalankan `php artisan db:wipe`
5. jalankan `php artisan:migrate --seed`
6.

## Panduan

1. Daftar akun sementara ada pada direktori database/seeders/UserSeeder.php
2. Mahasiswa, Staff, Kaprodi, Wakil Dekan, dan Akademik login melalui halaman utama `/` sedangkan admin melalui halaman `/admin`.

## Dokumentasi

1. [Panduan Alur Kerja (Workflow) Surat](docs/DOCUMENTATION_WORKFLOW.md)
2. [Panduan Menambahkan Role & Workflow Baru](docs/GUIDE_ADDING_NEW_ROLE.md)
