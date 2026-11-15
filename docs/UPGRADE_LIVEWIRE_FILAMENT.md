# Upgrade Livewire 3.8 + Filament 3.3 dan Perbaikan Login

Panduan deploy aman untuk perubahan dependensi di branch `feat/sso-provider` sebelum digabung ke `main`. Baca seluruhnya sebelum deploy ke staging maupun production.

> **Ringkas:** pasangan versi baru (Livewire 3.8.11 + Filament 3.3.57) sudah diuji dan aman. Yang berbahaya adalah **deploy setengah-setengah**, misalnya Livewire naik tetapi Filament tidak, atau aset `public/` dan tema tidak ikut diperbarui. Ikuti urutan di §5 apa adanya.

---

## 1. Apa yang berubah

### 1.1 Versi paket

| Paket | `main` (production saat ini) | Branch ini |
|---|---|---|
| `laravel/framework` | 10.31.0 | 10.50.4 |
| `livewire/livewire` | 3.1.0 | **3.8.11** |
| `filament/filament` (+ `filament/*`) | 3.0.0 | **3.3.57** |
| `laravel/passport` | — | 12.4.3 (baru, untuk SSO) |
| `danharrin/livewire-rate-limiting` | 1.3.1 | 2.3.0 |

Paket lain yang ikut naik sebagai dependensi Filament: `spatie/invade`, `ryangjchandler/blade-capture-directive`, `kirschbaum-development/eloquent-power-joins`, `symfony/html-sanitizer`, ditambah paket baru `league/csv`, `openspout/openspout`, dan `anourvalar/eloquent-serialize`.

Di `composer.json`, constraint Filament diubah dari `"3.0-stable"` menjadi `"^3.3"`.

### 1.2 Kenapa Livewire dinaikkan

Livewire 3.1 memiliki advisory keamanan, termasuk celah serius yang baru diperbaiki di 3.6.4. Menurunkan Livewire kembali **tidak** disarankan.

### 1.3 Kenapa Filament wajib ikut naik

Livewire 3.6+ memeriksa *release token* di setiap request `/livewire/update`. Filament 3.0.0 mendaftarkan komponen Livewire-nya terlambat, sehingga pemeriksaan itu gagal: **setiap aksi di panel admin ditolak dengan 419**. Gejalanya:

- Login admin "terpental" kembali ke halaman login tanpa pesan.
- Tombol simpan, filter tabel, dan aksi lain di panel tidak jalan.

Filament 3.3 sudah kompatibel. **Livewire 3.8 + Filament 3.0 adalah kombinasi rusak**: jangan pernah deploy salah satunya saja.

### 1.4 Perbaikan login yang ikut di branch ini

| Masalah | Penyebab | Perbaikan |
|---|---|---|
| Membuka e-Surat langsung malah menampilkan halaman login SSO e-Skripsi | Tampilan dipilih dari `url.intended` di session, yang tertinggal dari percobaan SSO yang tidak selesai | `app/Exceptions/Handler.php` menandai URL login dengan `?sso=<client_id>` hanya bila datang dari `/oauth/authorize`; `AuthController::login()` membuang tujuan SSO basi bila penanda tidak ada |
| Login biasa e-Surat bisa dilempar ke e-Skripsi | Sama: `redirect()->intended()` memakai tujuan `/oauth/authorize` yang tertinggal | Sama seperti di atas |
| Admin terpental setelah browser yang sama dipakai akun lain (mis. mahasiswa via SSO) | `AuthenticateSession` menyimpan hash password akun lama; Laravel 10 tidak memperbaruinya saat login | `EventServiceProvider` memperbarui `password_hash_<guard>` pada event `Login` |

Test terkait: `tests/Feature/SsoMahasiswaTest.php` dan `tests/Feature/AdminLoginSetelahAkunLainTest.php`.

### 1.5 File yang wajib ikut ter-commit

| Path | Alasan |
|---|---|
| `composer.json`, `composer.lock` | Mengunci pasangan versi yang sudah diuji |
| `public/vendor/livewire/*` | Aset JS Livewire **dilacak git**. Versi lama di git = JS 3.1 bertemu PHP 3.8 |
| `public/js/filament/*`, `public/css/filament/*` | Aset Filament hasil `filament:upgrade`, juga dilacak git |

Cek sebelum merge:

```bash
cat public/vendor/livewire/manifest.json
# harus: {"/livewire.js":"d73b2798"}  (bukan c4077c56 = versi 3.1)
```

---

## 2. Prasyarat server

### 2.1 Versi PHP

`composer.json` mengunci platform ke PHP **8.3** (`config.platform.php = 8.3.0`). Server harus PHP 8.3.x. Jangan deploy ke PHP 8.4/8.5 tanpa menguji ulang.

### 2.2 Ekstensi PHP wajib

```bash
php -m | grep -Ei '^(gd|bcmath|sodium|iconv|intl|mbstring|zip|pdo_mysql)$'
```

| Ekstensi | Dipakai oleh |
|---|---|
| `gd` | QR code surat (`simplesoftwareio/simple-qrcode`), dompdf |
| `bcmath` | `dragon-code/support` (via `laravel-lang`) |
| `sodium` | `lcobucci/jwt` → **Laravel Passport (SSO)**. Ini baru sejak SSO |
| `iconv` | `bacon/bacon-qr-code` |
| `intl` | Filament (format angka/tanggal) |

Bila salah satu tidak ada, `composer install` gagal dengan pesan `requires ext-xxx`. **Pasang ekstensinya**, jangan memakai `--ignore-platform-req` di server.

### 2.3 Aset frontend (server tanpa Node)

Server tidak punya Node.js, jadi `public/build` di-build **di laptop** lalu diunggah (FTP/SFTP). `public/build` tidak dilacak git.

Tema admin (`resources/css/filament/admin/theme.css`) meng-import CSS dari `vendor/filament`. Akibatnya:

- Setelah Filament naik, **tema wajib di-build ulang**.
- `vendor/` di laptop **harus sama versinya** dengan yang akan terpasang di server. Jalankan `composer install` di laptop dari commit yang akan dideploy **sebelum** `npm run build`. Build dengan `vendor` lama menghasilkan tema Filament lama.

Langkah lengkap ada di §5.4.

---

## 3. Aturan emas

1. **`composer install`, bukan `composer update`** di server. `install` memasang persis isi `composer.lock` yang sudah diuji; `update` bisa menarik versi lain.
2. **Backup database** sebelum `migrate --force`.
3. **Staging dulu**, baru production (§5 dan §6).
4. Jalankan artisan dengan **user yang sama dengan PHP-FPM** (mis. `www-data`), bukan `root`. File cache milik root membuat halaman error 500 (`Permission denied` di `storage/framework/views`).
5. **Jangan** `migrate:fresh`, `migrate:refresh`, atau `db:wipe`.

---

## 4. Uji di lokal sebelum merge

```bash
# di container sail
php artisan test                      # harus: 95+ passed, 0 failed
npm run build                         # tema Filament harus ter-build tanpa error
```

Uji manual:

- [ ] Login admin (`/admin/login`) berhasil dan **tidak terpental**
- [ ] Buka beberapa resource admin (Akun, SSO Clients), lalu **simpan** satu perubahan
- [ ] Filter/sort/paginasi tabel admin jalan
- [ ] Login portal biasa (`/`) untuk beberapa role, termasuk mahasiswa
- [ ] Buka e-Surat langsung, harus muncul **login biasa** (bukan login SSO)
- [ ] Mulai SSO dari e-Skripsi, batalkan, lalu buka e-Surat langsung: tetap login biasa, dan setelah login **tetap di e-Surat**
- [ ] Login SSO dari e-Skripsi sampai selesai: kembali ke e-Skripsi
- [ ] Login sebagai mahasiswa (SSO), lalu login admin di browser yang sama: admin masuk tanpa terpental
- [ ] Alur surat utama (ajukan, verifikasi, cetak PDF dengan QR) normal

---

## 5. Deploy ke STAGING

### 5.1 Persiapan

```bash
# catat commit yang sedang jalan, untuk rollback
git rev-parse HEAD > ~/esurat-commit-sebelum-upgrade.txt

# backup database
mysqldump --single-transaction -u <user> -p <database> > ~/backup-esurat-$(date +%F-%H%M).sql

# cek ekstensi (§2.2)
php -m | grep -Ei '^(gd|bcmath|sodium|iconv|intl)$'
```

### 5.2 Mode maintenance (opsional, disarankan)

```bash
php artisan down --refresh=15
```

### 5.3 Ambil kode dan pasang dependensi

```bash
git fetch origin
git checkout <branch-atau-main-yang-akan-dideploy>
git pull

composer install --no-dev --optimize-autoloader --no-interaction
```

`composer install` otomatis menjalankan `php artisan filament:upgrade` lewat script `post-autoload-dump`. Bila script dimatikan di server, jalankan manual:

```bash
php artisan filament:upgrade
```

Aset Livewire sudah ikut dari git. Bila server tidak mengambil `public/vendor` dari git, publish ulang:

```bash
php artisan vendor:publish --tag=livewire:assets --force
```

### 5.4 Build aset di laptop lalu unggah

Di **laptop**, dari commit yang sama persis dengan yang dideploy:

```bash
git checkout <commit-yang-akan-dideploy>
composer install                      # vendor/filament harus 3.3.57 (tema meng-import dari sini)

# matikan `npm run dev` bila sedang jalan, lalu pastikan file hot tidak ada
rm -f public/hot

npm ci
npm run build

# cek hasil
ls public/hot 2>/dev/null && echo "BAHAYA: public/hot masih ada"
cat public/build/manifest.json | head
```

> **Jangan pernah mengunggah `public/hot`.** File ini dibuat oleh `npm run dev`. Bila ikut terunggah, Laravel memuat aset dari `localhost:5173` dan seluruh halaman tampil tanpa CSS/JS. Bila terlanjur, hapus `public/hot` di server.

> Nilai `VITE_*` di `.env` laptop ikut tertanam di hasil build. Pastikan isinya sesuai environment tujuan.

Unggah ke server:

1. Unggah **seluruh isi** `public/build/` (folder `assets/` **dan** `manifest.json`) ke `public/build/` di server.
2. **Jangan hapus file build lama dulu.** Nama file ber-hash, jadi file lama dan baru bisa berdampingan. `manifest.json` baru langsung menunjuk ke file baru.
3. File lama dihapus setelah deploy dinyatakan berhasil (§6). Sampai saat itu, file lama berguna untuk rollback.

Cek di server setelah unggah:

```bash
ls public/hot 2>/dev/null && echo "HAPUS public/hot!"
grep -o '"file": "assets/theme-[^"]*"' public/build/manifest.json
```

### 5.5 Migrasi dan cache

```bash
php artisan migrate --force           # aditif: tabel oauth_* dan kolom SSO
php artisan optimize:clear
php artisan optimize                  # config + route + view cache
```

Khusus SSO, bila belum pernah di server ini:

```bash
php artisan passport:keys             # SEKALI SAJA. Backup storage/oauth-*.key
```

### 5.6 Perbaiki kepemilikan file

```bash
sudo chown -R www-data:www-data storage bootstrap/cache   # sesuaikan user PHP-FPM
```

### 5.7 Keluar dari maintenance dan restart worker

```bash
php artisan up
php artisan queue:restart             # bila memakai queue worker
sudo systemctl reload php8.3-fpm      # agar OPcache memuat kode baru
```

### 5.8 Verifikasi staging

```bash
cat public/vendor/livewire/manifest.json          # {"/livewire.js":"d73b2798"}
composer show livewire/livewire | grep versions   # v3.8.11
composer show filament/filament | grep versions   # v3.3.57
```

Lalu jalankan seluruh checklist §4 (uji manual) di host staging. Tambahan:

- [ ] Tidak ada error 419 atau 500 di `storage/logs/laravel.log`
- [ ] Console browser di `/admin` bebas error JS
- [ ] `scripts/sso-smoke.php` lulus terhadap host staging (lihat `docs/SSO_MAHASISWA_OAUTH.md`)

**Jangan lanjut ke production sebelum semua centang di atas hijau.**

---

## 6. Deploy ke PRODUCTION

Langkah sama persis dengan §5 (termasuk build di laptop dan unggah `public/build`), dengan tambahan:

1. Pilih **jam sepi** (malam atau akhir pekan). Pengguna yang sedang membuka panel saat deploy akan melihat "halaman kedaluwarsa" sekali; itu normal, cukup refresh.
2. Backup database **dan** `storage/oauth-*.key` (bila sudah ada).
3. Setelah deploy, segera lakukan uji asap:
   - [ ] Login admin, buka 2–3 resource, simpan satu perubahan kecil
   - [ ] Login portal sebagai staf dan sebagai mahasiswa
   - [ ] Buka satu surat dan cetak PDF-nya
   - [ ] (Bila SSO sudah aktif) login mahasiswa dari e-Skripsi
4. Pantau `storage/logs/laravel.log` selama 15–30 menit pertama.
5. Setelah semuanya normal, hapus file build lama di `public/build/assets/` yang tidak lagi disebut di `manifest.json`.

### 6.1 Setelah deploy: matikan halaman izin untuk e-Skripsi

Klien SSO yang dibuat sebelum kolom `skip_consent` ada masih bernilai `false`. Karena e-Skripsi adalah aplikasi milik FKIP sendiri, halaman "Izinkan" tidak perlu:

**Filament → SSO Clients → (klien e-Skripsi) → nyalakan "Lewati izin" → Simpan.**

Jangan nyalakan untuk aplikasi pihak luar.

---

## 7. Rollback

Gunakan bila panel admin atau login rusak setelah deploy dan tidak bisa diperbaiki cepat.

```bash
php artisan down

git checkout $(cat ~/esurat-commit-sebelum-upgrade.txt)
composer install --no-dev --optimize-autoloader --no-interaction
# aset: unggah ulang public/build hasil build commit lama dari laptop
# (atau kembalikan manifest.json lama bila file build lama belum dihapus)

php artisan optimize:clear
php artisan optimize
sudo chown -R www-data:www-data storage bootstrap/cache
sudo systemctl reload php8.3-fpm

php artisan up
```

Catatan:

- Kembali ke commit lama otomatis mengembalikan pasangan **Livewire 3.1 + Filament 3.0** (cocok satu sama lain) beserta aset `public/` versi lama dari git.
- **Tidak perlu** `migrate:rollback`. Migrasi SSO hanya menambah tabel dan kolom; kode lama tidak membacanya sehingga aman dibiarkan.
- Bila hanya SSO yang bermasalah (panel admin normal), cukup nonaktifkan klien di **Filament → SSO Clients → Aktif = off**. Tidak perlu rollback kode.
- Bila memang harus membalik migrasi, restore dari backup §5.1. Jangan menebak jumlah `--step`.

---

## 8. Troubleshooting

| Gejala | Kemungkinan penyebab | Tindakan |
|---|---|---|
| Login admin terpental tanpa pesan, request `/livewire/update` 419 | Livewire 3.8 berjalan dengan Filament 3.0 (lock file tidak ikut, atau `composer update` di server) | Pastikan `composer.lock` dari branch ini, jalankan `composer install`, cek versi (§5.8) |
| Panel admin tampil tanpa style / berantakan | Tema belum di-build ulang, atau di-build dengan `vendor` Filament lama | Di laptop: `composer install` dari commit yang sama, `npm run build`, unggah ulang seluruh `public/build` |
| Semua halaman tanpa CSS/JS, request ke `localhost:5173` | `public/hot` ikut terunggah | Hapus `public/hot` di server |
| Error `Unable to locate file in Vite manifest` | `manifest.json` tidak ikut terunggah atau tidak cocok dengan isi `assets/` | Unggah ulang seluruh `public/build` |
| Error JS Livewire di console, aksi tidak jalan | `public/vendor/livewire` masih versi lama | `php artisan vendor:publish --tag=livewire:assets --force` |
| Error 500 `file_put_contents(... storage/framework/views ...) Permission denied` | Artisan dijalankan sebagai root | `chown -R www-data:www-data storage bootstrap/cache` |
| `composer install` gagal `requires ext-...` | Ekstensi PHP belum terpasang | Pasang ekstensi (§2.2), jangan `--ignore-platform-req` |
| Membuka e-Surat menampilkan login SSO e-Skripsi | Kode lama (sebelum perbaikan §1.4) | Pastikan commit perbaikan ikut ter-deploy, lalu `optimize:clear` |
| SSO gagal "invalid key" / 500 di `/oauth/token` | `passport:keys` belum dijalankan | `php artisan passport:keys` (sekali) |
| Perubahan kode tidak terasa | OPcache atau cache config/route | `php artisan optimize:clear`, reload PHP-FPM |

---

## 9. Referensi

- `docs/SSO_MAHASISWA_OAUTH.md`: arsitektur dan runbook deploy SSO
- `docs/SSO_CLIENT_INTEGRATION.md`: integrasi sisi aplikasi klien (e-Skripsi)
- Panduan upgrade Filament 3: https://filamentphp.com/docs/3.x/panels/upgrade-guide
