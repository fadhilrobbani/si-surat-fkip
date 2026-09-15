@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Bendahara | Detail Persetujuan
    </x-slot:title>

    <div class="max-w-4xl mx-auto p-4 bg-white rounded-lg shadow dark:bg-gray-800">
        <h1 class="text-xl font-bold text-center text-gray-900 dark:text-white mb-4">
            Detail Riwayat Persetujuan
        </h1>

        <div class="bg-gray-50 p-4 rounded-lg space-y-3 text-sm mb-4">
            <div class="flex justify-between">
                <span class="text-gray-500">Jenis Surat:</span>
                <span class="font-bold text-gray-800">{{ $surat->jenisSurat->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Pengaju:</span>
                <span class="font-semibold text-gray-800">{{ $surat->pengaju->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Nama Kegiatan:</span>
                <span class="font-semibold text-gray-800">{{ $surat->data['namaKegiatan'] ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Total Anggaran:</span>
                <span class="font-extrabold text-emerald-600">Rp {{ number_format($surat->data['totalAnggaran'] ?? ($surat->data['nominal'] ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status Tindakan:</span>
                <span class="font-bold {{ $approval->isApproved ? 'text-green-600' : 'text-red-600' }}">
                    {{ $approval->isApproved ? 'Disetujui / Dicairkan' : 'Ditolak' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Catatan Bendahara:</span>
                <span class="font-semibold text-gray-800">{{ $approval->note ?? '-' }}</span>
            </div>
            @if (isset($surat->data['nomorBuktiPencairan']))
                <div class="flex justify-between">
                    <span class="text-gray-500">No Bukti Pencairan / KAS:</span>
                    <span class="font-mono font-bold text-blue-700">{{ $surat->data['nomorBuktiPencairan'] }}</span>
                </div>
            @endif
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('riwayat-persetujuan-bendahara') }}"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700">
                Kembali
            </a>
            <a href="{{ route('preview-surat-bendahara', $surat->id) }}" target="_blank"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                Preview Dokumen
            </a>
        </div>
    </div>
</x-layout>
