# Panduan Menambahkan Jenis Surat Mahasiswa Baru

Dokumen ini adalah panduan teknis langkah-demi-langkah untuk menambahkan jenis surat baru ke dalam sistem **si-surat-fkip** khusus untuk pengaju **Mahasiswa**, dengan alur verifikasi standar:

```
Mahasiswa → Staff Prodi → Kaprodi → Akademik (SELESAI)
```

Sebagai studi kasus, panduan ini menggunakan penambahan **Surat Permohonan Izin Observasi Pembelajaran Mahasiswa** (`surat-permohonan-izin-observasi-pembelajaran-mahasiswa`).

---

## Gambaran Umum File yang Perlu Diubah

| # | File | Jenis Perubahan |
|---|---|---|
| 1 | `database/seeders/JenisSuratSeeder.php` | Tambah entri jenis surat baru |
| 2 | `app/Http/Controllers/SuratController.php` | Tambah logika `create()` & `store()` |
| 3 | `app/Http/Controllers/PDFController.php` | Tambah logika preview/cetak PDF |
| 4 | `resources/views/mahasiswa/formsurat/` | Buat file form pengajuan (Blade) |
| 5 | `resources/views/template/` | Buat file template PDF (Blade) |
| 6 | Database (manual/seeder) | Daftarkan jenis surat di tabel |

---

## Langkah 1: Daftarkan Jenis Surat di Seeder

Buka `database/seeders/JenisSuratSeeder.php` dan tambahkan entri baru di dalam array `$daftarJenisSurat`:

```php
[
    'name' => 'Surat Permohonan Izin Observasi Pembelajaran Mahasiswa',
    'slug' => 'surat-permohonan-izin-observasi-pembelajaran-mahasiswa',
    'user_type' => 'mahasiswa'
],
```

> [!IMPORTANT]
> `slug` adalah identifier unik yang digunakan di seluruh sistem. Gunakan format `kebab-case` dan pastikan tidak ada duplikasi. `user_type` untuk surat mahasiswa selalu `'mahasiswa'`.

### Tips Penamaan Key Data (`$surat->data`)

Nama key di array `data` yang disimpan ke database akan **otomatis menjadi label** di halaman detail surat (`show-surat.blade.php`) menggunakan mekanisme `preg_split('/(?=[A-Z])/', $key)`. Contoh:

| Key Data (camelCase) | Label Otomatis di UI |
|---|---|
| `tempatObservasi` | "Tempat Observasi" ✅ |
| `waktuMulaiObservasi` | "Waktu Mulai Observasi" ✅ |
| `tempatPenelitian` | "Tempat Penelitian" ❌ (salah konteks) |

**Kesimpulan:** Pilih nama key yang semantik sesuai konteks surat agar label tampil benar tanpa perlu modifikasi view.

---

## Langkah 2: Update `SuratController.php`

### A. Method `create()` — Menampilkan Form

Tambahkan blok `if` baru sebelum `return abort(404)` (sekitar baris 280-an):

```php
if ($jenisSurat->slug == 'surat-permohonan-izin-observasi-pembelajaran-mahasiswa') {
    return view('mahasiswa.formsurat.form-permohonan-izin-observasi-pembelajaran', [
        'jenisSurat' => $jenisSurat,
        'daftarProgramStudi' => ProgramStudi::all(),
        'daftarPenerima' => User::select('id', 'name', 'username')
            ->where('role_id', '=', 3)            // 3 = Staff Prodi
            ->where('program_studi_id', '=', auth()->user()->program_studi_id)
            ->get()
    ]);
}
```

> [!NOTE]
> `role_id = 3` adalah **Staff Prodi** — penerima pertama di alur surat mahasiswa. Sesuaikan jika alur berbeda.

### B. Method `store()` — Menyimpan Data

Tambahkan blok `elseif` baru di method `store()`. Pola umumnya:

```php
} elseif ($jenisSurat->slug == 'surat-permohonan-izin-observasi-pembelajaran-mahasiswa') {

    $request->validate([
        'name'                      => 'required',
        'username'                  => 'required',
        'program-studi'             => 'required',
        'email'                     => 'required|email',
        'tujuan1'                   => 'required',
        'tujuan2'                   => '',
        'tujuan3'                   => '',
        'judul-skripsi'             => 'required',
        'tempat-observasi'          => 'required',        // nama input HTML
        'waktu-mulai-observasi'     => 'required|date',
        'waktu-selesai-observasi'   => 'required|date',
        'berkas-proposal'           => 'required|file|mimes:pdf|max:10240',
    ]);

    $programStudi = ProgramStudi::select('name')
        ->where('id', '=', $request->input('program-studi'))
        ->first();

    $surat = new Surat;
    $surat->pengaju_id    = auth()->user()->id;
    $surat->current_user_id = $request->input('penerima');
    $surat->status        = 'diproses';
    $surat->jenis_surat_id = $jenisSurat->id;
    $surat->expired_at    = now()->addDays(30);
    $surat->data = [
        'nama'               => $request->input('name'),
        'npm'                => $request->input('username'),
        'programStudi'       => $programStudi->name,
        'email'              => $request->input('email'),
        'tujuan1'            => $request->input('tujuan1'),
        'tujuan2'            => $request->input('tujuan2'),
        'tujuan3'            => $request->input('tujuan3'),
        'judulSkripsi'       => $request->input('judul-skripsi'),
        // ⬇ Gunakan key yang semantik agar label UI otomatis tepat
        'tempatObservasi'        => $request->input('tempat-observasi'),
        'waktuMulaiObservasi'    => formatTimestampToOnlyDateIndonesian($request->input('waktu-mulai-observasi')),
        'waktuSelesaiObservasi'  => formatTimestampToOnlyDateIndonesian($request->input('waktu-selesai-observasi')),
    ];

    if ($request->hasFile('berkas-proposal')) {
        $surat->files = [
            'berkasProposal' => $request->file('berkas-proposal')->store('lampiran')
        ];
    }

    // Cegah duplikasi pengajuan aktif
    if (
        Surat::where('jenis_surat_id', $jenisSurat->id)
        ->where('pengaju_id', auth()->user()->id)
        ->where('status', 'diproses')
        ->where('created_at', '>=', now()->subDays(30))
        ->count() > 0
    ) {
        return redirect()->back()->with('deleted', 'Anda masih memiliki surat dengan jenis ini yang sedang diproses.');
    }

    $surat->save();
    return redirect('/mahasiswa/riwayat-pengajuan-surat')->with('success', 'Surat berhasil diajukan');
}
```

---

## Langkah 3: Buat Form Pengajuan (View)

Buat file baru: `resources/views/mahasiswa/formsurat/form-permohonan-izin-observasi-pembelajaran.blade.php`

Struktur dasarnya identik dengan form surat lain di folder yang sama. Poin kuncinya:

```blade
<p class="font-bold text-lg mx-auto text-center mb-2">Surat Permohonan Izin Observasi Pembelajaran</p>
<form action="{{ route('store-surat', $jenisSurat->slug) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('post')

    {{-- Field Nama, NPM, Program Studi, Email (readonly dari data auth user) --}}
    {{-- Field Tujuan Surat (1, 2, 3 — opsional) --}}
    {{-- Field Judul Skripsi --}}

    {{-- Ganti nama label sesuai konteks surat --}}
    <input type="text" name="tempat-observasi" ...>     {{-- bukan tempat-penelitian --}}
    <input type="date" name="waktu-mulai-observasi" ...>
    <input type="date" name="waktu-selesai-observasi" ...>

    {{-- Upload lampiran --}}
    <input type="file" name="berkas-proposal" accept=".pdf" ...>

    {{-- Modal konfirmasi penerima --}}
    <x-modal-send :daftarPenerima='$daftarPenerima' />
    <button ...>Ajukan Surat</button>
</form>
```

> [!TIP]
> Salin file form surat yang paling mirip (mis. `form-permohonan-izin-penelitian.blade.php`) sebagai template awal, lalu ubah:
> - Judul heading `<p>` di atas form
> - Label dan `name` attribute pada field-field spesifik

---

## Langkah 4: Buat Template PDF (View)

Buat file baru: `resources/views/template/surat-permohonan-izin-observasi-pembelajaran-mahasiswa.blade.php`

Struktur sama dengan template surat mahasiswa lainnya. Poin penting:

```blade
{{-- Perihal --}}
<td>: Permohonan Izin Observasi Pembelajaran</td>

{{-- Kalimat pengantar --}}
<p>Sehubungan dengan kegiatan observasi pembelajaran dan penulisan skripsi/tesis/disertasi mahasiswa
   berikut, Kami mohon bantuan Bapak/Ibu untuk dapat memberikan izin melakukan observasi pembelajaran
   kepada:</p>

{{-- Tabel data mahasiswa — gunakan key yang SAMA dengan yang disimpan di $surat->data --}}
<tr>
    <td>Tempat Observasi</td>
    <td>: {{ $surat->data['tempatObservasi'] }}</td>
</tr>
<tr>
    <td>Waktu Observasi</td>
    <td>: {{ $surat->data['waktuMulaiObservasi'] . ' - ' . $surat->data['waktuSelesaiObservasi'] }}</td>
</tr>
```

> [!WARNING]
> Pastikan nama key di `$surat->data[...]` dalam template **sama persis** dengan key yang disimpan di `SuratController::store()`. Ketidakcocokan akan menyebabkan error saat PDF di-preview/cetak.

---

## Langkah 5: Daftarkan di `PDFController.php`

Tambahkan blok `if` baru di method `previewSurat()`:

```php
if ($surat->jenisSurat->slug == 'surat-permohonan-izin-observasi-pembelajaran-mahasiswa') {
    $pdf = Pdf::loadview(
        'template.surat-permohonan-izin-observasi-pembelajaran-mahasiswa',
        ['surat' => $surat]
    )->setPaper('a4', 'potrait')->setOptions([
        'tempDir' => public_path(),
        'chroot'  => public_path()
    ]);
}
```

---

## Langkah 6: Daftarkan ke Database

### Opsi A — via Seeder (Development)

```bash
./vendor/bin/sail artisan db:seed --class=JenisSuratSeeder
```

> [!NOTE]
> Seeder menggunakan `firstOrCreate` sehingga aman dijalankan berulang kali — tidak akan menduplikasi data yang sudah ada.

### Opsi B — via Filament Admin (Production / Staging)

1. Login ke `/admin`
2. Buka menu **Jenis Surat**
3. Klik **Buat Baru**, isi:
   - **Name**: `Surat Permohonan Izin Observasi Pembelajaran Mahasiswa`
   - **Slug**: `surat-permohonan-izin-observasi-pembelajaran-mahasiswa`
   - **User Type**: `mahasiswa`

### Opsi C — Hapus Data Lama (jika rename slug)

Jika slug pernah berubah (rename), hapus data lama dari database agar tidak muncul di daftar pengajuan:

```bash
./vendor/bin/sail artisan tinker --execute="
    App\Models\JenisSurat::where('slug', 'slug-lama-yang-salah')->delete();
    echo 'Deleted.';
"
```

---

## Checklist Cepat

- [ ] Tambah entri di `JenisSuratSeeder.php`
- [ ] Tambah blok `if` di `SuratController::create()`
- [ ] Tambah blok `elseif` di `SuratController::store()` dengan key data semantik
- [ ] Buat file form: `resources/views/mahasiswa/formsurat/form-[slug].blade.php`
- [ ] Buat file template PDF: `resources/views/template/[slug].blade.php`
- [ ] Tambah blok `if` di `PDFController::previewSurat()`
- [ ] Jalankan seeder atau tambah manual via Filament Admin

---

## Catatan: Mekanisme Label Otomatis di Halaman Detail

Halaman `resources/views/mahasiswa/show-surat.blade.php` menampilkan semua data surat secara otomatis menggunakan logika berikut:

```php
// Baris ~96 di show-surat.blade.php
{{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}
```

Artinya: **nama key camelCase di array `$surat->data` langsung menjadi label di UI** tanpa perlu modifikasi view. Oleh karena itu, penamaan key data harus cermat dan semantik sesuai konteks surat yang dibuat.
