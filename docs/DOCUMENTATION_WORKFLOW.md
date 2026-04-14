# Dokumentasi Alur Kerja (Workflow) Surat

Dokumen ini menjelaskan alur kerja pengajuan surat di sistem **si-surat-fkip**. Setiap jenis surat memiliki urutan verifikasi yang berbeda-beda tergantung pada pengaju dan tujuan surat tersebut.

---

## 1. Alur Surat Mahasiswa (Akademik)
Digunakan untuk surat seperti: *Aktif Kuliah, Surat Keterangan Lulus (SKL), Eligible PIN, Rekomendasi MBKM, dll.*

```mermaid
graph TD
    A[Mahasiswa] -->|Mengajukan| B[Staff Prodi]
    B -->|Verifikasi 1| C[Kaprodi]
    C -->|Verifikasi 2| D[Akademik Jurusan]
    D -->|Validasi & Selesai| E((SELESAI))
    
    B -.->|Tolak| A
    C -.->|Tolak| A
    D -.->|Tolak| A
```

**Detail Langkah:**
1. **Mahasiswa**: Memulai pengajuan dan memilih Staff Prodi di prodi masing-masing.
2. **Staff Prodi (Role 3)**: Melakukan pengecekan berkas awal.
3. **Kaprodi (Role 4)**: Memberikan persetujuan tingkat program studi.
4. **Akademik (Role 6)**: Melakukan validasi akhir, memberikan nomor surat, dan menutup alur (`Selesai`).

---

## 2. Alur Berita Acara Nilai (BAN)
Digunakan oleh Staff Prodi untuk pengajuan berita acara perubahan atau input nilai.

```mermaid
graph TD
    A[Staff Prodi] -->|Mengajukan| B[Kaprodi]
    B -->|Verifikasi| C[WD 1 - Bid. Akademik]
    C -->|Persetujuan Akhir| D[Staff Nilai]
    D -->|Input & Selesai| E((SELESAI))
    
    B -.->|Tolak| A
    C -.->|Tolak| A
    D -.->|Tolak| A
```

**Detail Langkah:**
1. **Staff (Role 3)**: Mengajukan BAN ke Kaprodi.
2. **Kaprodi (Role 4)**: Menelaah urgensi perubahan nilai.
3. **WD 1 (Role 5)**: Memberikan lampu hijau secara fakultas.
4. **Staff Nilai (Role 7)**: Melakukan eksekusi input nilai ke sistem akademik dan menandai surat `Selesai`.

---

## 3. Alur Surat Tugas (Staff Prodi)
Digunakan untuk penugasan dosen atau staff baik individu maupun kelompok.

```mermaid
graph TD
    A[Staff Prodi] -->|Mengajukan| B[Kaprodi]
    B -->|Verifikasi| C[Staff Dekan]
    C -->|Drafting / Penomoran| D[Pimpinan - Dekan/WD]
    D -->|Tanda Tangan & Selesai| E((SELESAI))
```

**Detail Langkah:**
1. **Staff (Role 3)**: Mengajukan surat tugas ke Kaprodi.
2. **Kaprodi (Role 4)**: Menyetujui penugasan.
3. **Staff Dekan (Role 14)**: Memproses administrasi dan meneruskan ke pimpinan yang relevan.
4. **Pimpinan (Role 5/8/9/10)**: Menandatangani secara digital dan surat dinyatakan `Selesai`.

---

## 4. Alur Pengajuan ATK (Alat Tulis Kantor)
Alur ini lebih singkat dan bermuara pada bagian logistik/umum.

```mermaid
graph TD
    A[Staff Prodi] -->|Penerima: Kaprodi| B[Kaprodi]
    B --> C[Kabag TU]
    
    D[Unit Nonprodi] -->|Penerima: Kabag| C[Kabag TU]
    
    C -->|Validasi & Selesai| E((SELESAI))
```

**Detail Jalur:**
* **Jalur Prodi**: Staff -> Kaprodi -> Kabag.
* **Jalur Unit (TU/Akademik/dll)**: Pengaju -> Kabag secara langsung.
* **Tujuan Akhir**: **Kabag (Role 17)** memverifikasi ketersediaan stok dan menyelesaikan pengajuan.

---

## 5. Alur Legalisir Ijazah
Alur khusus untuk alumni/mahasiswa yang membutuhkan validasi dokumen fisik/digital.

```mermaid
graph TD
    A[Alumni / Mahasiswa] -->|Melalui LegalisirController| B[Akademik]
    B -->|Proses| C{Diambil atau Dikirim?}
    C -->|Diambil| D((SELESAI))
    C -->|Dikirim| E[Pengirim Legalisir]
    E -->|Update Resi| D
```

**Detail Langkah:**
1. **Pengaju**: Melakukan permohonan legalisir.
2. **Akademik (Role 6)**: Menyiapkan dokumen legalisir.
3. **Pilihan**:
    * Jika **Ambil sendiri**, Akademik langsung menutup alur.
    * Jika **Dikirim**, diteruskan ke **Pengirim Legalisir (Role 15)** untuk proses kurir.

---

## 6. Alur Surat Dekanat (Surat Keluar)
Surat yang dibuat oleh internal dekanat untuk pihak luar atau penugasan internal.

```mermaid
graph TD
    A[Staff Dekan] -->|Drafting| B[Pimpinan - Dekan/WD]
    B -->|Tanda Tangan & Selesai| C((SELESAI))
```

**Detail Langkah:**
1. **Staff Dekan (Role 14)**: Membuat draf surat keluar.
2. **Pimpinan (Role 5, 8, 9, atau 10)**: Melakukan review dan tanda tangan final.

---

## Ringkasan Peran Utama (Role ID)
*   **Role 3 (Staff Prodi)**: Pengaju / Verifikator Awal Mahasiswa.
*   **Role 4 (Kaprodi)**: Verifikator tingkat Prodi.
*   **Role 6 (Akademik)**: Verifikator tingkat Fakultas / Tujuan Akhir Mhs.
*   **Role 17 (Kabag)**: Verifikator Akhir Keuangan & ATK.
*   **Role 8 (Dekan) / 5, 9, 10 (WD)**: Otoritas tertinggi penandatangan.
