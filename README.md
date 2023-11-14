## Overview
AKUN FILAMENT (Karena menyesuaikan dengan format login sistem yang sudah ada, maka login menggunakan akun yang sudah dimasukkan lewat seeder saja dan tidak bisa lewat `php artisan make:filament-user`)

username: admin
password: admin

Ini adalah proyek sistem surat menyurat untuk FKIP UNIB berbasis website dengan framework laravel.

## Cara install

1. Clone repository ini. Lalu buat database MySQL baru, misal lewat xampp dan misal beri nama `si_surat_fkip`
2. Buat file `.env` di level direktori paling atas (selevel dengan `.env.example`), lalu copy isi file `.env.example` ke dalam `.env`
3. Ganti nilai `DB_DATABASE` di file `.env` sesuai database yang dibuat, misal `si_surat_fkip`
4. Masuk ke direktori `si-surat-fkip` atau nama folder projek ini di terminal
5. Jalankan `composer install`
6. Jalankan `npm install` (pastikan nodejs sudah terinstall)
7. Jalankan `npm run build`
8. Jalankan `npm run dev`
9. Buka terminal baru dengan direktori yang masih sama
10. Jalankan `php artisan key:generate`
11. Jalankan `php artisan migrate --seed`
12. Jalankan `php artisan serve`. Lalu buka alamatnya di browser (biasanya di `http://127.0.0.1:8000/`)
