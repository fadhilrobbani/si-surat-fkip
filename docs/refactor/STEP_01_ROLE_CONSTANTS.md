# Refactor Step 01: Implementasi Role Constants

**Status**: [RENCANA/DRAFT]
**Tanggal Direncanakan**: 2026-04-15
**PIC**: AI Assistant & Developer

---

## 1. Masalah (Problem)
Saat ini sistem menggunakan ID numerik yang di-hardcode (contoh: `role_id == 21`) di banyak tempat (Controller, View, Seeder, Auth). 
- **Risiko**: Jika data role di database dihapus dan diinput ulang dengan ID berbeda, sistem akan pecah.
- **Keterbacaan**: Sangat sulit memahami peran apa yang dimaksud hanya dengan melihat angka.

## 2. Solusi (Solution)
Mendefinisikan konstanta (Constants) di dalam model `User` untuk mewakili ID role yang bersifat kritikal.

### Rencana Perubahan:
1.  Buka `app/Models/User.php`.
2.  Tambahkan konstanta sesuai tabel `roles`.
    ```php
    const ROLE_ADMIN = 1;
    const ROLE_MAHASISWA = 2;
    const ROLE_KABAG = 17;
    const ROLE_LAB_PMIPA = 21;
    // ...dst
    ```
3.  Lakukan penggantian bertahap pada kode:
    - **Sebelum**: `where('role_id', 17)`
    - **Sesudah**: `where('role_id', User::ROLE_KABAG)`

## 3. Manfaat (Benefit)
- Kode lebih *self-documenting*.
- Jika ID berubah di masa depan, kita hanya perlu mengubah di satu tempat (Model User).
- Mengurangi bug saat integrasi role baru oleh AI Agent (AI lebih paham konteks kata daripada angka).

## 4. Log Eksekusi (Execution Log)
*Belum dieksekusi.*

---

## 5. Daftar File Terdampak (Affected Files)
- `app/Models/User.php` (Define)
- `app/Http/Controllers/SuratController.php` (Update usage)
- `app/Http/Controllers/KabagController.php` (Update usage)
- `resources/views/components/layout.blade.php` (Sidebar checks)
