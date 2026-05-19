# Refactor Step 00: Strategi & Roadmap Global

**Status**: [LETAK DASAR]
**Tujuan**: Mengurangi *Technical Debt* secara bertahap tanpa merombak total sistem sekaligus (Incremental Refactoring).

---

## Filosofi Refactor
Karena keterbatasan waktu dan biaya, refactoring dilakukan dengan prinsip **"Boy Scout Rule"**: 
> *"Tinggalkan kode sedikit lebih bersih daripada saat Anda pertama kali datang."*

Jangan memaksakan satu kali commit besar untuk mengubah segalanya, melainkan cicil setiap kali ada tugas baru (seperti penambahan role baru).

---

## Roadmap Refactoring

### Tahap 1: Penghentian Angka Gaib (Magic Numbers)
- **Target**: Menghilangkan ketergantungan pada `role_id` yang di-hardcode (misal: 17, 21).
- **Solusi**: Pindah ke **Role Constants** di Model `User`.
- **Status**: Siap eksekusi (Lihat [STEP_01_ROLE_CONSTANTS.md](file:///home/fadhilrobbani/Programming/Demo/si-surat-fkip/docs/refactor/STEP_01_ROLE_CONSTANTS.md)).

### Tahap 2: Dekonsentrasi Controller (Lean Controller)
- **Target**: Merampingkan `SuratController` dan per-role Controller yang membengkak.
- **Solusi**: Pindahkan logika pemilihan view atau validasi ke dalam Model `JenisSurat` atau `SuratService`. Controller hanya berfungsi sebagai "polisi lalu lintas".

### Tahap 3: Standarisasi Tampilan (Reusable Components)
- **Target**: Menghilangkan copy-paste view folder setiap ada role baru.
- **Solusi**: Manfaatkan **Blade Components** untuk elemen yang berulang (seperti Form ATK, Header Surat, dan Badge Status).

### Tahap 4: Dinamisasi Alur (Workflow Engine Ringan)
- **Target**: Menghilangkan hardcode alur verifikasi ("Kenapa surat ini harus ke Kabag?").
- **Solusi**: Simpan alur verifikasi di database (`target_role_id` pada `jenis_surat`). Biarkan admin menentukan alur via Filament.

### Tahap 5: Modernisasi State (Optional)
- **Target**: Membuat sistem terasa lebih responsif dan state-driven.
- **Solusi**: Pertimbangkan penggunaan **Inertia.js** atau **Livewire** untuk bagian-bagian yang sangat interaktif.

---

## Cara Menggunakan Dokumentasi Ini
1. Setiap kali akan melakukan refactor, buat file baru dengan format `STEP_XX_JUDUL.md`.
2. Tulis rencana dan file yang terdampak.
3. Setelah selesai, update bagian **Log Eksekusi** di file tersebut dan tandai di Roadmap ini.
