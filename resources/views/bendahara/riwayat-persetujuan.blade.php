@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Bendahara | Riwayat Persetujuan
    </x-slot:title>
    <div class="overflow-x-auto">
        <h1 class="mx-auto text-center font-bold mb-4">Riwayat Pencairan & Persetujuan Dana</h1>

        <div class="w-full overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Pengaju</th>
                        <th class="px-6 py-3">Nama Kegiatan</th>
                        <th class="px-6 py-3">Total Anggaran</th>
                        <th class="px-6 py-3">Status Pencairan</th>
                        <th class="px-6 py-3">Tanggal Verifikasi</th>
                        <th class="px-6 py-3 text-center">
                            <span class="inline-flex items-center justify-center gap-1">
                                Detail & Aksi
                                <x-info-tooltip id="tooltip-riwayat-bendahara" size="w-3.5 h-3.5" />
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarRiwayatSurat as $index => $approval)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $daftarRiwayatSurat->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $approval->surat->pengaju->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $approval->surat->data['namaKegiatan'] ?? ($approval->surat->data['keperluan'] ?? '-') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600">
                                Rp {{ number_format($approval->surat->data['totalAnggaran'] ?? ($approval->surat->data['nominal'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($approval->isApproved)
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        Dicairkan / Disetujui
                                    </span>
                                @else
                                    <span class="bg-rose-100 text-rose-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ formatTimestampToIndonesian($approval->created_at) }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('show-approval-bendahara', $approval->id) }}"
                                        class="text-white bg-slate-700 hover:bg-slate-800 font-medium rounded-lg text-xs px-2.5 py-1.5 transition">
                                        Lihat
                                    </a>
                                    @if ($approval->surat->status == 'selesai')
                                        <a href="{{ route('print-surat-bendahara', $approval->surat->id) }}" target="_blank"
                                            class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-xs px-2.5 py-1.5 transition">
                                            Cetak
                                        </a>
                                    @else
                                        <a href="{{ route('preview-surat-bendahara', $approval->surat->id) }}" target="_blank"
                                            class="text-white bg-slate-600 hover:bg-slate-700 font-medium rounded-lg text-xs px-2.5 py-1.5 transition">
                                            Preview
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">Belum ada riwayat pencairan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $daftarRiwayatSurat->links() }}
        </div>
    </div>
</x-layout>
