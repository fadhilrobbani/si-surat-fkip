# Tutorial Update e-Surat di Server (SSO + Filament 3.3)

Ikuti langkah dari atas ke bawah. Jangan lompat. Kerjakan di **staging dulu**, lalu ulangi persis sama di **production**.

Update kali ini membawa tiga perubahan sekaligus:

1. **SSO**: e-Surat menjadi tempat login untuk e-Skripsi (Laravel Passport, ada tabel baru).
2. **Filament 3.0 → 3.3**: wajib, karena Livewire sudah 3.8. Tanpa ini login admin terpental.
3. **`vendor/` tidak lagi disimpan di git.** Akibatnya, `git pull` akan **menghapus folder `vendor/` di server**, dan situs mati sampai `vendor/` dipasang ulang (langkah 4).

> Server e-Surat adalah **shared hosting** (user `u716715624`, PHP 8.3 di `/opt/alt/php83`). Tidak ada `sudo`, `chown`, maupun `systemctl`, dan semua file aplikasi sudah milik user kamu sendiri, jadi perintah semacam itu memang tidak diperlukan. Pengaturan PHP (ekstensi, restart) dilakukan dari hPanel.

---

## Bagian A: Persiapan di laptop

### A1. Build aset frontend

Server tidak punya Node, jadi aset dibangun di laptop.

```bash
cd ~/Programming/FKIP/si-surat-fkip
git checkout main && git pull

docker exec -u sail si-surat-fkip-laravel.test-1 composer install   # tema Filament butuh vendor/ terbaru
rm -f public/hot            # file ini tidak boleh ikut terunggah
pnpm install --frozen-lockfile
pnpm run build
```

Hasilnya ada di `public/build/` (folder `assets/` + `manifest.json`). Diunggah di langkah 6.

### A2. (Hanya bila server TIDAK punya composer) Siapkan vendor

Cek dulu di server: `composer --version`. Bila ada, **lewati A2**.

```bash
# di laptop, pada commit yang sama dengan yang akan dideploy
docker exec -u sail si-surat-fkip-laravel.test-1 composer install --no-dev --optimize-autoloader
zip -qr vendor.zip vendor
docker exec -u sail si-surat-fkip-laravel.test-1 composer install   # kembalikan paket dev untuk lokal
```

Composer dijalankan di container karena PHP-nya 8.3, sama dengan server. Unggah `vendor.zip` ke server, di luar folder aplikasi (mis. `~/vendor.zip`).

---

## Bagian B: Di server

Login SSH lalu masuk ke folder aplikasi. Semua perintah dijalankan sebagai user pemilik aplikasi, **bukan root**.

```bash
cd /path/ke/si-surat-fkip
```

### Langkah 1. Cek PHP

```bash
php -v                                              # harus 8.3.x
php -m | grep -Ei '^(gd|bcmath|sodium|iconv|intl)$' # kelimanya harus muncul
```

Bila ada yang kurang, pasang dulu. **Jangan lanjut** sebelum lengkap.

### Langkah 2. Backup

```bash
git rev-parse HEAD > ~/esurat-commit-lama.txt      # commit lama, untuk rollback
cp -a vendor ~/vendor-lama                         # vendor lama, untuk rollback
# staging (user dan nama database sama)
mysqldump --single-transaction -u u716715624_esurat_staging -p u716715624_esurat_staging > ~/backup-esurat-staging-$(date +%F).sql

# production: hapus akhiran _staging
mysqldump --single-transaction -u u716715624_esurat -p u716715624_esurat > ~/backup-esurat-$(date +%F).sql
```

Password database ada di `.env` server (`DB_PASSWORD`).

### Langkah 3. Maintenance lalu tarik kode

```bash
php artisan down

# cek perubahan lokal di server
git status --short
```

Cara membaca hasilnya:

| Awalan | Arti | Tindakan |
|---|---|---|
| `??` | File tidak dilacak git (mis. `.htaccess` dan `index.php` di root, `default.php`, `public/build.zip`, `public/ca_dompdf_img_*`) | **Biarkan.** `git pull` tidak menyentuhnya. Jangan dihapus: `.htaccess`/`index.php` di root dipakai hosting untuk mengarahkan domain ke `public/` |
| ` D vendor/...` | File vendor sudah hilang dari disk (biasanya paket dev yang terhapus saat dulu `composer install --no-dev`) | Aman, ikut dibereskan perintah di bawah |
| ` M vendor/...` | File vendor berubah karena dulu `composer install` di server | Aman, ikut dibereskan perintah di bawah |
| ` M`/` D` di luar `vendor/` | Ada edit langsung di server | **Berhenti.** Periksa dengan `git diff <file>` dan selamatkan dulu bila perlu |

Untuk melihat hanya baris yang perlu diperiksa (selain `??` dan `vendor/`):

```bash
git status --short | grep -v '^??' | grep -v ' vendor/'
```

Bila perintah ini **tidak menampilkan apa-apa**, lanjut.

`public/ca_dompdf_img_*` adalah file sementara dompdf saat mencetak PDF; boleh dihapus kapan saja (`rm -f public/ca_dompdf_img_*`), tetapi tidak wajib untuk update ini.

Bila yang berubah **hanya file `vendor/`** (biasanya `vendor/composer/autoload_*.php`, `installed.json`, `installed.php`, karena dulu pernah `composer install` di server), buang perubahannya. Aman, karena vendor sudah dibackup di langkah 2 dan akan dipasang ulang di langkah 4:

```bash
git checkout -- vendor
git pull origin main
```

Tanpa langkah ini pull gagal dengan pesan `Your local changes to the following files would be overwritten by merge: vendor/composer/...`.

Setelah ini `vendor/` terhapus sebagian. **Itu normal.** Jangan jalankan `php artisan` apa pun sampai langkah 4 selesai.

Bila pull gagal `Permission denied`, ada file yang bukan milik user hosting (jarang terjadi di shared hosting). Jangan coba `sudo`; hubungi support hosting untuk mengembalikan kepemilikan file ke user `u716715624`, lalu jalankan `git checkout -f main` dan ulangi pull.

### Langkah 4. Pasang ulang vendor

Pilih **salah satu**.

**4a. Server punya composer**

```bash
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
php artisan package:discover --ansi
php artisan filament:upgrade
```

Pakai `install`, **jangan** `update`.

`--no-scripts` wajib di shared hosting: fungsi `proc_open` dimatikan, sehingga composer tidak bisa menjalankan script `post-autoload-dump` dan berhenti dengan error `The Process class relies on proc_open`. Dua perintah `php artisan` di bawahnya menjalankan script itu secara manual.

**4b. Server tidak punya composer** (pakai `vendor.zip` dari A2)

```bash
rm -rf vendor
unzip -q ~/vendor.zip -d .
```

Cek: `ls vendor/autoload.php` harus ada.

### Langkah 5. Database

```bash
php artisan migrate:status     # lihat migrasi yang "Pending"
php artisan migrate --pretend  # lihat SQL-nya tanpa dijalankan
php artisan migrate --force
```

Isi `--pretend` seharusnya hanya:

- `create table` untuk `oauth_auth_codes`, `oauth_access_tokens`, `oauth_refresh_tokens`, `oauth_clients`, `oauth_personal_access_clients`, `oauth_client_roles`
- `alter table oauth_clients ...`

Tidak ada yang menyentuh tabel lama (surat, user, prodi). `--force` hanya melewati pertanyaan "yakin?" di production, jadi aman. **Jangan** pakai `migrate:fresh`, `migrate:refresh`, atau `db:wipe`.

Bila muncul error `table already exists`, data tidak hilang. Berhenti dan cek dulu sebelum lanjut.

### Langkah 6. Unggah aset

Lewat FTP, unggah **seluruh** isi `public/build/` dari laptop (A1) ke `public/build/` di server, **termasuk `manifest.json`**. Pastikan di server **tidak ada** file `public/hot`.

### Langkah 7. Kunci dan klien SSO (hanya sekali, saat pertama deploy SSO)

```bash
php artisan passport:keys      # membuat storage/oauth-*.key; jangan salin dari laptop
```

Tambahkan ke `.env` server (contoh staging, lihat `.env.example`):

```dotenv
SSO_CLIENT_NAME="Sistem Bimbingan Skripsi (Staging)"
SSO_SKRIPSI_REDIRECT_URI=https://staging-skripsi.fkipunib.my.id/auth/esurat/callback
```

Buat klien SSO untuk e-Skripsi:

```bash
php artisan db:seed --class=SsoClientSeeder --force
```

**Client ID dan secret hanya tampil sekali di sini.** Salin langsung ke `.env` e-Skripsi. Bila hilang, pakai **Regenerate Secret** di panel admin (SSO Clients).

### Langkah 8. Cache, izin file, nyalakan

```bash
php artisan filament:upgrade
php artisan optimize:clear
php artisan optimize
php artisan up
```

Bila perubahan belum terasa (OPcache), restart PHP dari hPanel: **Advanced → PHP Configuration**, ubah lalu simpan pengaturan apa saja (atau pilih ulang versi PHP 8.3).

### Langkah 9. Cek hasil

```bash
composer show filament/filament | grep versions        # 3.3.x (lewati bila tanpa composer)
grep -o 'd73b2798' public/vendor/livewire/manifest.json # harus muncul
```

Di browser:

- [ ] `/admin/login`: login admin berhasil, **tidak terpental**
- [ ] Buka satu resource admin, simpan satu perubahan
- [ ] Buka e-Surat langsung: muncul **login biasa** e-Surat, bukan login SSO
- [ ] Login beberapa role (mahasiswa, staf); alur surat dan cetak PDF normal
- [ ] Dari e-Skripsi, login lewat e-Surat: kembali ke e-Skripsi
- [ ] Panel admin, SSO Clients, klien e-Skripsi: nyalakan **Lewati izin** agar mahasiswa tidak melihat halaman "Izinkan"

Semua lolos? Hapus cadangan beberapa hari kemudian (`~/vendor-lama`, `~/vendor.zip`). Staging lancar, lalu ulangi Bagian B di production.

---

## Bila gagal: Rollback

```bash
php artisan down
mv vendor ~/vendor-gagal                          # singkirkan vendor baru dulu, kalau tidak reset gagal
git reset --hard $(cat ~/esurat-commit-lama.txt)
rm -rf vendor && cp -a ~/vendor-lama vendor
php artisan optimize:clear
php artisan up
```

- **Aset**: unggah ulang `public/build` hasil build commit lama dari laptop.
- **Database**: tidak perlu `migrate:rollback`. Tabel `oauth_*` tidak dibaca kode lama, aman dibiarkan. Bila memang harus dikembalikan, restore backup SQL dari langkah 2.
- **Hanya SSO yang bermasalah** tapi admin normal: tidak perlu rollback. Matikan klien di SSO Clients, set **Aktif = off**.

---

## Masalah yang mungkin muncul

| Gejala | Penyebab | Perbaikan |
|---|---|---|
| Situs 500, `vendor/autoload.php: No such file` | Langkah 4 belum dikerjakan | Kerjakan langkah 4 |
| `git pull` gagal `Your local changes ... would be overwritten by merge: vendor/composer/...` | `vendor/` pernah diubah `composer` di server saat masih dilacak git | `git checkout -- vendor`, lalu `git pull origin main` (langkah 3) |
| `composer install` berhenti `The Process class relies on proc_open` | Hosting mematikan `proc_open`, script composer gagal (paket sudah terpasang) | Jalankan manual `php artisan package:discover --ansi` dan `php artisan filament:upgrade`; berikutnya pakai `--no-scripts` (langkah 4a) |
| `git pull`/`checkout` gagal `Permission denied` | Ada file milik root atau user lain | Minta support hosting mengembalikan kepemilikan ke user hosting, lalu `git checkout -f main` dan ulangi |
| Login admin terpental tanpa pesan | Filament masih 3.0 (dipasang dengan `composer update`, atau lock file tidak ikut) | Ulangi langkah 4 dengan `composer install` |
| Admin tampil tanpa style | `public/build` belum diunggah, atau di-build dengan vendor lama | Ulangi A1 dan langkah 6 |
| Semua halaman tanpa CSS, request ke `localhost:5173` | `public/hot` ikut terunggah | Hapus `public/hot` di server |
| `Unable to locate file in Vite manifest` | `manifest.json` tidak ikut | Unggah ulang seluruh `public/build` |
| 500 `storage/framework/views ... Permission denied` | Izin folder salah | `chmod -R u+rwX storage bootstrap/cache`, lalu `php artisan optimize:clear` |
| `composer install` gagal `requires ext-...` | Ekstensi PHP kurang | Pasang ekstensi (langkah 1) |
| SSO 500 di `/oauth/token`, "invalid key" | `passport:keys` belum dijalankan | Langkah 7 |
| Perubahan tidak terasa | Cache | `php artisan optimize:clear`, restart PHP dari hPanel |

---

## Catatan lokal (Docker/sail)

Error `unable to unlink ... Permission denied` saat `git checkout` di laptop terjadi karena file dibuat container sebagai root. Perbaiki dengan `sudo chown -R $USER:$USER .` lalu `git checkout -f <branch>`. Supaya tidak terulang, jalankan artisan dan composer di container sebagai `sail`: `docker exec -u sail si-surat-fkip-laravel.test-1 ...`.
