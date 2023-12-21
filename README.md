## Overview

Ini adalah proyek sistem surat menyurat untuk FKIP UNIB berbasis website dengan framework laravel.

## Requirement

Pastikan sudah terinstall komponen berikut:

1. PHP 8.1^ (versi 8.1 ke atas)
2. nodeJS 16.^ (versi 16 ke atas)
3. Composer
4. MySQL

## Cara install

1. Clone repository ini `git clone https://github.com/fadhilrobbani/si-surat-fkip.git`.
2. Masuk ke direktori si-surat-fkip. Buat file `.env` di level direktori paling atas (selevel dengan `.env.example`), lalu copy isi file `.env.example` ke dalam `.env`. Atau jika menggunakan bash/powershell gunakan perintah `cp .env.example .env`
3. Ganti nilai `DB_DATABASE` di file `.env` sesuai nama yang diinginkan, misal `si_surat_fkip`
4. Pastikan server mysql sudah menyala di port 3306, bisa via XAMPP, Laragon, dan sebagainya.
5. Jalankan `composer install`
6. Jalankan `npm install` (pastikan nodejs sudah terinstall)
7. Jalankan `npm run build`
8. Jalankan `npm run dev`
9. Buka terminal baru dengan direktori yang masih sama
10. Jalankan `php artisan key:generate`
11. Jalankan `php artisan migrate --seed`
12. Jalankan `php artisan serve`. Lalu buka alamatnya di browser (biasanya di `http://127.0.0.1:8000/`)

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
