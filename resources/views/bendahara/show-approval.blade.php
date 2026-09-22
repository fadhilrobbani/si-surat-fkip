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
            @if (isset($surat->files) && is_array($surat->files))
                @foreach ($surat->files as $key => $value)
                    @if ($key == 'private' || empty($value))
                        @continue
                    @endif
                    @php
                        $storagePath = 'lampiran/' . basename($value);
                        $filename = pathInfo(basename($value), PATHINFO_FILENAME);
                        if (\App\Services\StorageHelper::exists($storagePath)) {
                            $mimeType = str_replace('/', '-', \App\Services\StorageHelper::mimeType($storagePath));
                        } else {
                            $mimeType = 'application-pdf';
                        }
                        $extension = pathinfo(basename($value), PATHINFO_EXTENSION) ?: 'pdf';
                        $url = URL::signedRoute('show-file', [
                            'user' => $authUser->id,
                            'filename' => $filename,
                            'mimeType' => $mimeType,
                            'extension' => $extension,
                        ]);
                        $label = $key === 'berkasProposal'
                            ? 'Lampiran Berkas Proposal & RAB'
                            : 'Lampiran ' . ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key)));
                    @endphp
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">{{ $label }}:</span>
                        <a href="{{ $url }}" target="_blank"
                            class="text-blue-600 hover:underline font-semibold text-xs inline-flex items-center gap-1">
                            <x-heroicon-o-document-text class="w-4 h-4" />
                            Lihat Dokumen PDF
                        </a>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="my-4">
            <x-stepper-flexible :surat="$surat" />
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mt-6">
            <a href="{{ route('riwayat-persetujuan-bendahara') }}"
                class="w-full sm:w-auto text-center px-4 py-2 bg-gray-600 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition order-last sm:order-first">
                Kembali
            </a>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                @if ($surat->status == 'selesai')
                    <a href="{{ route('print-surat-bendahara', $surat->id) }}" target="_blank"
                        class="w-full sm:w-auto text-center px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-medium rounded-lg text-sm transition">
                        Cetak Surat
                    </a>
                @else
                    <a href="{{ route('preview-surat-bendahara', $surat->id) }}" target="_blank"
                        class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg text-sm transition">
                        Preview Dokumen
                    </a>
                    <x-info-tooltip id="tooltip-approval-bendahara" />
                @endif
            </div>
        </div>
    </div>
</x-layout>
