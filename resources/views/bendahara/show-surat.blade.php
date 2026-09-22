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
                    @php
                        $items = $surat->data['items'];
                        $hasMak = false;
                        foreach ($items as $it) {
                            if (is_array($it) && !empty($it['mak'])) {
                                $hasMak = true;
                                break;
                            }
                        }
                    @endphp
                    <div class="mt-3">
                        <span class="text-gray-500 font-medium block mb-1">Rincian Kebutuhan Anggaran:</span>
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full text-xs">
                                <thead class="bg-gray-200 text-gray-700">
                                    <tr>
                                        <th class="p-2 text-center w-10">No</th>
                                        <th class="p-2 text-left">Kegiatan / Uraian Kebutuhan Anggaran</th>
                                        @if ($hasMak)
                                            <th class="p-2 text-center w-28">Kode Akun / MAK</th>
                                        @endif
                                        <th class="p-2 text-right w-36">Jumlah (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $idx => $item)
                                        @if (!empty($item['has_sub']) && !empty($item['sub_items']))
                                            {{-- Header Kegiatan Utama --}}
                                            <tr class="border-t bg-gray-100 font-semibold text-gray-900">
                                                <td class="p-2 text-center">{{ $idx + 1 }}</td>
                                                <td class="p-2">{{ $item['uraian'] ?? '-' }}</td>
                                                @if ($hasMak)
                                                    <td class="p-2 text-center font-mono text-gray-700">{{ $item['mak'] ?? '-' }}</td>
                                                @endif
                                                <td class="p-2 text-right font-bold">Rp {{ number_format((float)($item['nominal'] ?? 0), 0, ',', '.') }}</td>
                                            </tr>
                                            {{-- Baris Sub-kegiatan --}}
                                            @foreach ($item['sub_items'] as $sub)
                                                <tr class="border-t bg-white hover:bg-gray-50">
                                                    <td class="p-2 text-center"></td>
                                                    <td class="p-2 pl-6 text-gray-700">
                                                        {{ $sub['uraian'] ?? '-' }}
                                                        @if (!empty($sub['volume']) && !empty($sub['satuan']))
                                                            <span class="text-gray-500 text-[11px] block sm:inline sm:ml-1">({{ $sub['volume'] }} {{ $sub['satuan'] }} @ Rp {{ number_format((float)($sub['harga_satuan'] ?? 0), 0, ',', '.') }})</span>
                                                        @endif
                                                    </td>
                                                    @if ($hasMak)
                                                        <td class="p-2 text-center text-gray-400">-</td>
                                                    @endif
                                                    <td class="p-2 text-right text-gray-700">Rp {{ number_format((float)($sub['nominal'] ?? 0), 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            {{-- Baris Flat --}}
                                            <tr class="border-t bg-white hover:bg-gray-50">
                                                <td class="p-2 text-center">{{ $idx + 1 }}</td>
                                                <td class="p-2 font-medium text-gray-800">
                                                    {{ $item['uraian'] ?? '-' }}
                                                    @if (!empty($item['volume']) && !empty($item['satuan']))
                                                        <span class="text-gray-500 text-[11px] block sm:inline sm:ml-1">({{ $item['volume'] }} {{ $item['satuan'] }} @ Rp {{ number_format((float)($item['harga_satuan'] ?? 0), 0, ',', '.') }})</span>
                                                    @endif
                                                </td>
                                                @if ($hasMak)
                                                    <td class="p-2 text-center font-mono text-gray-700">{{ $item['mak'] ?? '-' }}</td>
                                                @endif
                                                <td class="p-2 text-right font-semibold text-gray-900">Rp {{ number_format((float)($item['nominal'] ?? ($item['subtotal'] ?? 0)), 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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

                @if (isset($surat->files) && is_array($surat->files))
                    <div class="mt-3 space-y-2">
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
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 font-medium mr-2">{{ $label }}:</span>
                                <a href="{{ $url }}" target="_blank"
                                    class="text-blue-600 hover:underline font-semibold text-xs inline-flex items-center gap-1">
                                    <x-heroicon-o-document-text class="w-4 h-4" />
                                    Lihat Dokumen PDF
                                </a>
                            </div>
                        @endforeach
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
                        <label for="no-surat" class="block mb-1 text-sm font-medium text-gray-700">
                            Nomor Surat <span class="text-xs font-normal text-gray-500">(Opsional - lengkapi jika belum ada)</span>
                        </label>
                        <input type="text" id="no-surat" name="no-surat"
                            value="{{ old('no-surat', $surat->data['noSurat'] ?? '') }}"
                            placeholder="Contoh: 015/DST/UN30.7.11/KU.01.02/{{ date('Y') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <div class="mt-1.5 flex items-center gap-2">
                            <button type="button"
                                onclick="fillNoSuratBendahara('/DST/UN30.7.11/KU.01.02/{{ date('Y') }}')"
                                class="text-xs inline-flex items-center gap-1 font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded border border-blue-200 transition">
                                📋 Gunakan Format: /DST/UN30.7.11/KU.01.02/{{ date('Y') }}
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika nomor belum terbit. Jika diisi, wajib sertakan format lengkap.</p>
                        @error('no-surat')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
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

    <script>
        function fillNoSuratBendahara(format) {
            const input = document.getElementById('no-surat');
            if (!input) return;
            const currentVal = input.value.trim();
            if (!currentVal) {
                input.value = format;
                input.focus();
                input.setSelectionRange(0, 0);
            } else if (!currentVal.includes('/')) {
                input.value = currentVal + format;
                input.focus();
            }
        }
    </script>
</x-layout>
