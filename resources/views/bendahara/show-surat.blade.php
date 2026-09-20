@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Bendahara | Verifikasi Pencairan Dana
    </x-slot:title>

    <div class="max-w-4xl mx-auto p-4 bg-white rounded-lg shadow dark:bg-gray-800">
        <h1 class="text-xl font-bold text-center text-gray-900 dark:text-white mb-4">
            {{ $surat->jenisSurat->name }}
        </h1>

        <div class="border-t border-b py-4 my-4">
            <h2 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Informasi Pengaju:</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-500">Nama Pengaju:</span>
                    <span class="font-semibold text-gray-800 ml-2">{{ $surat->pengaju->name }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Username / NPM:</span>
                    <span class="font-semibold text-gray-800 ml-2">{{ $surat->pengaju->username }}</span>
                </div>
                @if (isset($surat->data['namaOrganisasi']))
                    <div>
                        <span class="text-gray-500">Organisasi / Himpunan:</span>
                        <span class="font-semibold text-indigo-600 ml-2">{{ $surat->data['namaOrganisasi'] }} ({{ $surat->data['jabatanPengaju'] ?? 'Pengurus' }})</span>
                    </div>
                @endif
                <div>
                    <span class="text-gray-500">Tanggal Pengajuan:</span>
                    <span class="font-semibold text-gray-800 ml-2">{{ formatTimestampToIndonesian($surat->created_at) }}</span>
                </div>
            </div>
        </div>

        <div class="my-4">
            <x-stepper-flexible :surat="$surat" />
        </div>

        <div class="py-2">
            <h2 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">Rincian Ajuan Dana:</h2>
            <div class="bg-gray-50 p-4 rounded-lg space-y-3 text-sm">
                <div>
                    <span class="text-gray-500 font-medium">Nama Kegiatan:</span>
                    <p class="font-semibold text-gray-800 text-base mt-0.5">{{ $surat->data['namaKegiatan'] ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500 font-medium">Tahun Anggaran:</span>
                    <span class="font-semibold text-gray-800 ml-2">{{ $surat->data['tahunAnggaran'] ?? date('Y') }}</span>
                </div>

                @if (isset($surat->data['items']) && is_array($surat->data['items']))
                    <div class="mt-3">
                        <span class="text-gray-500 font-medium block mb-1">Rincian Kebutuhan Anggaran:</span>
                        <table class="w-full text-xs border border-gray-200">
                            <thead class="bg-gray-200 text-gray-700">
                                <tr>
                                    <th class="p-2 text-left">No</th>
                                    <th class="p-2 text-left">Uraian / Kebutuhan</th>
                                    <th class="p-2 text-right">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($surat->data['items'] as $idx => $item)
                                    <tr class="border-t">
                                        <td class="p-2">{{ $idx + 1 }}</td>
                                        <td class="p-2 font-medium">{{ $item['uraian'] ?? '-' }}</td>
                                        <td class="p-2 text-right">Rp {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="pt-2 border-t flex justify-between items-center">
                    <span class="text-base font-bold text-gray-700">Total Anggaran yang Diajukan:</span>
                    <span class="text-xl font-extrabold text-emerald-600">
                        Rp {{ number_format($surat->data['totalAnggaran'] ?? ($surat->data['nominal'] ?? 0), 0, ',', '.') }}
                    </span>
                </div>

                <div class="mt-4 pt-3 border-t grid grid-cols-1 md:grid-cols-3 gap-2 bg-blue-50 p-3 rounded">
                    <div>
                        <span class="text-xs text-gray-500 block">Bank Tujuan</span>
                        <span class="font-bold text-blue-900">{{ $surat->data['namaBank'] ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Nomor Rekening</span>
                        <span class="font-bold text-blue-900">{{ $surat->data['nomorRekening'] ?? ($surat->data['noRekening'] ?? '-') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Atas Nama Rekening</span>
                        <span class="font-bold text-blue-900">{{ $surat->data['atasNamaRekening'] ?? '-' }}</span>
                    </div>
                </div>

                @if (isset($surat->files['berkasProposal']))
                    <div class="mt-3">
                        <span class="text-gray-500 font-medium mr-2">Lampiran Berkas Proposal & RAB:</span>
                        <a href="{{ asset('storage/' . $surat->files['berkasProposal']) }}" target="_blank"
                            class="text-blue-600 hover:underline font-semibold text-xs inline-flex items-center gap-1">
                            Lihat Dokumen PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @if ($surat->status == 'diproses')
            {{-- Form Tindakan Bendahara --}}
            <div class="mt-6 pt-4 border-t">
                <div class="flex items-center gap-1.5 mb-3">
                    <h2 class="text-sm font-semibold text-gray-700">Tindakan Verifikasi</h2>
                    <x-info-tooltip id="tooltip-preview-bendahara" />
                </div>
                <form action="{{ route('setujui-surat-bendahara', $surat->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="no_bukti_pencairan" class="block mb-1 text-sm font-medium text-gray-700">
                            Nomor Bukti Pencairan / Referensi Kas (Opsional)
                        </label>
                        <input type="text" id="no_bukti_pencairan" name="no_bukti_pencairan"
                            placeholder="Contoh: KAS/2026/09/012 (boleh dikosongkan jika manual)"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                    </div>

                    <div class="mb-4">
                        <label for="note" class="block mb-1 text-sm font-medium text-gray-700">
                            Catatan Bendahara (Opsional)
                        </label>
                        <textarea id="note" name="note" rows="2"
                            placeholder="Tambahkan catatan pencairan dana atau informasi transfer..."
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mt-6">
                        <a href="{{ route('confirm-tolak-surat-bendahara', $surat->id) }}"
                            style="background-color: #e11d48; color: #ffffff;"
                            class="w-full sm:w-auto text-center px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg text-sm transition order-last sm:order-first">
                            Tolak Pengajuan
                        </a>

                        <div class="flex flex-col sm:flex-row items-stretch gap-2 flex-1 sm:justify-end">
                            <a href="{{ route('preview-surat-bendahara', $surat->id) }}" target="_blank"
                                class="flex-1 text-center px-4 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg text-sm transition">
                                Preview Dokumen
                            </a>
                            <button type="submit"
                                style="background-color: #059669; color: #ffffff;"
                                class="flex-1 text-center px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg text-sm shadow cursor-pointer transition">
                                Setujui & Selesaikan Pencairan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @elseif ($surat->status == 'selesai')
            <div class="mt-6 pt-4 border-t">
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                    <span class="font-bold">Status Surat: Selesai.</span> Pengajuan dana ini telah diverifikasi dan disetujui untuk dicairkan.
                    @if (!empty($surat->data['nomorBuktiPencairan']))
                        <span class="block mt-1 font-mono text-xs">No. Bukti Kas: {{ $surat->data['nomorBuktiPencairan'] }}</span>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <a href="{{ route('print-surat-bendahara', $surat->id) }}" target="_blank"
                        class="w-full sm:w-auto text-center text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 transition">
                        Cetak Surat
                    </a>
                    <a href="{{ route('riwayat-persetujuan-bendahara') }}"
                        class="w-full sm:w-auto text-center text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 transition">
                        Kembali ke Riwayat
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-layout>
