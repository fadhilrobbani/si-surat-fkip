@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Bendahara | Tolak Pengajuan
    </x-slot:title>

    <div class="max-w-xl mx-auto p-6 bg-white rounded-lg shadow dark:bg-gray-800 mt-6">
        <h1 class="text-lg font-bold text-red-600 mb-2">Konfirmasi Penolakan Pengajuan Dana</h1>
        <p class="text-sm text-gray-600 mb-4">
            Anda akan menolak pengajuan <b>{{ $surat->jenisSurat->name }}</b> dari <b>{{ $surat->pengaju->name }}</b>.
        </p>

        <form action="{{ route('tolak-surat-bendahara', $surat->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alasan Penolakan (Wajib Diisi)</label>
                <textarea id="note" name="note" rows="4" required
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300"
                    placeholder="Contoh: Berkas RAB tidak lengkap / Pagu anggaran prodi telah habis..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('show-surat-masuk-bendahara', $surat->id) }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</x-layout>
