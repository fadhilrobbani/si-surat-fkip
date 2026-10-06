@props(['authUser'])

@php
    $signerName = trim((string) $authUser->name);
    $signerNip = trim((string) $authUser->nip);
    $placeholderName = $signerName === '' || preg_match('/^Kaprodi\b/i', $signerName) === 1;
    $placeholderNip = $signerNip === '';
    $needsAttention = $placeholderName || $placeholderNip;
@endphp

<div class="w-full max-w-[560px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-3">
    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Penandatangan Surat &mdash; Koordinator Program Studi</p>
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Nama &amp; NIP berikut akan tercetak pada surat dan QR verifikasi. Periksa kembali sebelum menyetujui.</p>

    @if ($needsAttention)
        <div class="mb-3 rounded-lg border border-amber-300 bg-amber-50 dark:bg-amber-900/30 px-3 py-2 text-xs text-amber-800 dark:text-amber-200">
            <b>Perhatian:</b> Nama/NIP akun Anda masih berupa data default (placeholder). Mohon perbaiki di bawah ini agar surat tercetak dengan identitas yang benar.
            <a href="{{ url('/kaprodi/profile?edit=true') }}" class="underline font-semibold">Buka halaman Profil</a>
        </div>
    @endif

    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label for="nama_kaprodi" class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Nama Lengkap &amp; Gelar <span class="text-red-500">*</span></label>
            <input type="text" id="nama_kaprodi" name="nama_kaprodi" required
                value="{{ old('nama_kaprodi', $authUser->name) }}"
                placeholder="Contoh: Dr. Nama Lengkap, M.Pd."
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            @error('nama_kaprodi')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="nip_kaprodi" class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">NIP <span class="text-red-500">*</span></label>
            <input type="text" id="nip_kaprodi" name="nip_kaprodi" required
                value="{{ old('nip_kaprodi', $authUser->nip) }}"
                placeholder="Masukkan NIP Anda"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
            @error('nip_kaprodi')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <label class="mt-3 inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
        <input type="checkbox" name="simpan_ke_profil" value="1"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            {{ old('simpan_ke_profil') ? 'checked' : '' }}>
        Simpan nama &amp; NIP ini sebagai default profil saya
    </label>
</div>
