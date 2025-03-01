<x-mail::message>
# ⚠️ Segera Lakukan Pembayaran untuk Pengajuan Legalisir Anda!

**Kepada Yth. {{ $surat->pengaju->name }},**

Kami ingin menginformasikan bahwa Anda telah melakukan pengajuan **Legalisir Ijazah** pada **E-Surat FKIP UNIB**. Untuk melanjutkan proses, harap segera melakukan pembayaran sesuai dengan nominal yang tertera di bawah ini:

---
### 🧾 Detail Pembayaran
- **Nama:** {{ $surat->pengaju->name }}
- **NPM:** {{ $surat->data['npm'] }}
- **Jumlah yang dibayarkan:** **Rp {{ number_format($surat->data['totalHarga'], 0, ',', '.') }}**

### 🏦 Rekening Pembayaran
- **Nama Bank:** MANDIRI
- **No. Rekening:** `1234567890`
- **Atas Nama:** FKIP Universitas Bengkulu

⏳ **Batas Waktu Pembayaran:** {{ formatTimestampToIndonesian($surat->expired_at) }}

---

📌 **Setelah melakukan pembayaran**, segera unggah bukti pembayaran melalui portal E-Surat FKIP UNIB pada bagian **Riwayat Pengajuan**:

[🔗 Cek Status Pengajuan]("{{ route('lihat-surat-mahasiswa', $surat) }}")

> ❗ *Abaikan pesan ini jika Anda telah melakukan pembayaran.*

Terima kasih atas perhatian dan kerja samanya.

**Hormat kami,**
**Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu**

<x-mail::button :url="'https://esurat-fkip.unib.ac.id'" color="primary">
Kunjungi E-Surat FKIP UNIB
</x-mail::button>
</x-mail::message>
