@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Tata Usaha | Konfirmasi Penolakan Surat
    </x-slot:title>
    <div class="max-w-xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mt-6">
        <form action="{{ route('tolak-surat-tata-usaha', $surat->id) }}" method="POST" class="text-center">
            @csrf
            <svg class="mx-auto mb-4 text-rose-500 w-14 h-14" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-gray-200">
                Konfirmasi Penolakan Pengajuan
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                Apakah Anda yakin ingin menolak pengajuan <strong>{{ $surat->jenisSurat->name }}</strong> dari <strong>{{ $surat->data['nama'] ?? $surat->pengaju->name }}</strong>?
            </p>
            <div class="mb-6 text-left">
                <label for="catatan" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea id="catatan" name="catatan" rows="4" required
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-rose-500 focus:border-rose-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                    placeholder="Masukkan alasan penolakan surat agar pengaju dapat memperbaikinya..."></textarea>
            </div>
            <div class="flex justify-center gap-3">
                <button type="submit"
                    class="text-white bg-rose-600 hover:bg-rose-700 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                    Ya, Tolak Surat
                </button>
                <a href="{{ route('show-surat-masuk-tata-usaha', $surat->id) }}"
                    class="text-gray-700 bg-white hover:bg-gray-100 border border-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-layout>
