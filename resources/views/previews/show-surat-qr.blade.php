<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Cek Keaslian Surat</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
    <main class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md">
        <img class="w-28 mx-auto mb-5" src="{{ asset('images/logounib.png') }}" alt="logounib" />

        <h1 class="text-2xl font-bold text-center text-blue-800">Halaman Validasi Keaslian Surat</h1>
        <p class="text-center text-gray-700 mt-2 mb-2">Dengan ini menyatakan bahwa surat ini adalah benar
            diterbitkan dari FKIP UNIB. </p>


        @if ($surat->jenisSurat->user_type == 'mahasiswa')
            <div class="overflow-x-auto  border-2 border-slate-300 rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nomor Surat:</td>
                            <td class="px-4 py-3">{{ $surat->data['noSurat'] ?? '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Tanggal Surat Diterbitkan:</td>
                            <td class="px-4 py-3">{{ $surat->data['tanggal_selesai'] ?? '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nama:</td>
                            <td class="px-4 py-3">{{ $surat->data['nama'] ?? '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">NPM:</td>
                            <td class="px-4 py-3">
                                {{ isset($surat->data['npm']) ? $surat->data['npm'] : ($surat->data['username'] ?? '-') }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jenis Surat:</td>
                            <td class="px-4 py-3">{{ $surat->jenisSurat->name }}</td>
                        </tr>

                        @php
                            $isKaprodiSigned = in_array($surat->jenisSurat->slug, [
                                'surat-permohonan-narasumber',
                                'surat-peminjaman-ruang',
                                'surat-peminjaman-ruang-mahasiswa',
                                'surat-pencairan-dana',
                                'surat-pencairan-dana-mahasiswa'
                            ]);

                            $namaSigner = $isKaprodiSigned
                                ? ($surat->data['private']['namaKaprodi'] ?? 'Koordinator Program Studi')
                                : ($surat->data['private']['namaWD1'] ?? ($surat->data['private']['namaWD'] ?? ($surat->data['private']['namaDekan'] ?? '(Nama tidak tersedia)')));

                            $jabatanSigner = $isKaprodiSigned
                                ? ($surat->data['private']['deskripsiKaprodi'] ?? 'Koordinator Program Studi')
                                : ($surat->data['private']['deksripsiWD1'] ?? ($surat->data['private']['deskripsiWD'] ?? ($surat->data['private']['deskripsiDekan'] ?? 'Wakil Dekan Bidang Akademik')));

                            $nipSigner = $isKaprodiSigned
                                ? ($surat->data['private']['nipKaprodi'] ?? '-')
                                : ($surat->data['private']['nipWD1'] ?? ($surat->data['private']['nipWD'] ?? ($surat->data['private']['nipDekan'] ?? '(NIP tidak tersedia)')));
                        @endphp

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Ditandatangani oleh:</td>
                            <td class="px-4 py-3">
                                {{ $namaSigner }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jabatan:</td>
                            <td class="px-4 py-3">
                                {{ $jabatanSigner }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">NIP:</td>
                            <td class="px-4 py-3">
                                {{ $nipSigner }}
                            </td>
                        </tr>


                    </tbody>
                </table>
            </div>

            <div class="overflow-x-auto  border-2 border-slate-300 rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        @foreach ($surat->data as $key => $value)
                            @if (in_array($key, [
                                    'private',
                                    'ttdWD',
                                    'ttdWD1',
                                    'noSurat',
                                    'note',
                                    'tanggal_selesai',
                                    'nama',
                                    'username',
                                    'email',
                                    'perihal',
                                    'npm',
                                    'items',
                                ]))
                                @continue
                            @endif

                            @if (is_array($value))
                                @continue
                            @endif

                            @if ($key == 'dosen')
                                @foreach ($value as $id => $data)
                                    @foreach ($data as $key => $value)
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold bg-gray-50">
                                                {{ convertToTitleCase($key) }}:</td>
                                            <td class="px-4 py-3">
                                                {!! html_entity_decode($value) !!}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                @continue
                            @endif

                            @if ($value != null)
                                <tr class="border-b">
                                    <td class="px-4 py-3 font-semibold bg-gray-50">
                                        {{ convertToTitleCase($key) }}:</td>
                                    <td class="px-4 py-3">
                                        {!! html_entity_decode((string) $value) !!}</td>
                                </tr>
                            @endif
                        @endforeach


                    </tbody>
                </table>


            </div>
        @endif

        @if (
            ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-tugas') ||
                ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-tugas-kelompok'))
            <div class="overflow-x-auto  border-2 border-slate-300 rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nomor Surat:</td>
                            <td class="px-4 py-3">
                                @php
                                    $noSuratQr = $surat->data['noSurat'] ?? null;
                                    if (!empty($noSuratQr)) {
                                        if (!str_contains($noSuratQr, '/')) {
                                            $tahunSurat = isset($surat->data['tanggal_selesai'])
                                                ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ')
                                                : ($surat->created_at ? $surat->created_at->year : date('Y'));
                                            $noSuratQr = $noSuratQr . '/UN30.7/KP/' . $tahunSurat;
                                        }
                                    } else {
                                        $noSuratQr = '-';
                                    }
                                @endphp
                                {{ $noSuratQr }}
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Tanggal Surat Diterbitkan:</td>
                            <td class="px-4 py-3">{{ $surat->data['tanggal_selesai'] ?? '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jenis Surat / Perihal:</td>
                            <td class="px-4 py-3">{{ $surat->jenisSurat->name }}</td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Ditandatangani oleh:</td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['namaWD1'] ?? ($surat->data['private']['namaWD'] ?? ($surat->data['private']['namaDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['deksripsiWD1'] ?? ($surat->data['private']['deskripsiWD'] ?? ($surat->data['private']['deskripsiDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['nipWD1'] ?? ($surat->data['private']['nipWD'] ?? ($surat->data['private']['nipDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="overflow-x-auto  border-2 border-slate-300 rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        @foreach ($surat->data as $key => $value)
                            @if (in_array($key, [
                                    'private',
                                    'ttdWD',
                                    'ttdWD1',
                                    'noSurat',
                                    'note',
                                    'tanggal_selesai',
                                    'nama',
                                    'username',
                                    'email',
                                    'perihal',
                                ]))
                                @continue
                            @endif

                            @if ($key == 'dosen')
                                @foreach ($value as $id => $data)
                                    @foreach ($data as $key => $value)
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold bg-gray-50">
                                                {{ convertToTitleCase($key) }}:</td>
                                            <td class="px-4 py-3">
                                                {!! html_entity_decode($value) !!}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                @continue
                            @endif

                            @if ($value != null)
                                <tr class="border-b">
                                    <td class="px-4 py-3 font-semibold bg-gray-50">
                                        {{ convertToTitleCase($key) }}:</td>
                                    <td class="px-4 py-3">
                                        {!! html_entity_decode($value) !!}</td>
                                </tr>
                            @endif
                        @endforeach


                    </tbody>
                </table>


            </div>
        @elseif ($surat->jenisSurat->user_type == 'staff' && in_array($surat->jenisSurat->slug, ['surat-permohonan-narasumber', 'surat-peminjaman-ruang', 'surat-pencairan-dana']))
            <div class="overflow-x-auto border-2 border-slate-300 rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nomor Surat:</td>
                            <td class="px-4 py-3">{{ $surat->data['noSurat'] ?? '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Tanggal Surat Diterbitkan:</td>
                            <td class="px-4 py-3">{{ $surat->data['tanggal_selesai'] ?? '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nama Pengaju:</td>
                            <td class="px-4 py-3">{{ $surat->data['nama'] ?? ($surat->pengaju->name ?? '-') }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Program Studi:</td>
                            <td class="px-4 py-3">{{ $surat->data['programStudi'] ?? ($surat->pengaju->programStudi->name ?? '-') }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jenis Surat:</td>
                            <td class="px-4 py-3">{{ $surat->jenisSurat->name }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Ditandatangani oleh:</td>
                            <td class="px-4 py-3">{{ $surat->data['private']['namaKaprodi'] ?? 'Koordinator Program Studi' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jabatan:</td>
                            <td class="px-4 py-3">{{ $surat->data['private']['deskripsiKaprodi'] ?? 'Koordinator Program Studi' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">NIP:</td>
                            <td class="px-4 py-3">{{ $surat->data['private']['nipKaprodi'] ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="overflow-x-auto border-2 border-slate-300 rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        @foreach ($surat->data as $key => $value)
                            @if (in_array($key, [
                                    'private',
                                    'ttdWD',
                                    'ttdWD1',
                                    'noSurat',
                                    'note',
                                    'tanggal_selesai',
                                    'nama',
                                    'username',
                                    'email',
                                    'perihal',
                                    'programStudi',
                                    'items',
                                ]))
                                @continue
                            @endif

                            @if (is_array($value))
                                @continue
                            @endif

                            @if ($value != null)
                                <tr class="border-b">
                                    <td class="px-4 py-3 font-semibold bg-gray-50">
                                        {{ convertToTitleCase($key) }}:</td>
                                    <td class="px-4 py-3">
                                        {!! html_entity_decode((string) $value) !!}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (!empty($surat->data['items']) && is_array($surat->data['items']))
                @php
                    $itemsQr = $surat->data['items'];
                    $hasMakQr = false;
                    foreach ($itemsQr as $it) {
                        if (is_array($it) && !empty($it['mak'])) {
                            $hasMakQr = true;
                            break;
                        }
                    }
                @endphp
                <div class="mt-4">
                    <h3 class="font-semibold text-gray-800 text-sm mb-2">Rincian Kebutuhan Anggaran:</h3>
                    <div class="overflow-x-auto border-2 border-slate-300 rounded-lg">
                        <table class="w-full text-xs text-left text-gray-700 bg-white">
                            <thead class="bg-gray-100 uppercase text-gray-700 font-semibold text-[11px]">
                                <tr>
                                    <th class="px-3 py-2 text-center w-12 border-b">No</th>
                                    <th class="px-3 py-2 border-b">Kegiatan / Uraian Kebutuhan Anggaran</th>
                                    @if ($hasMakQr)
                                        <th class="px-3 py-2 text-center w-28 border-b">Kode Akun / MAK</th>
                                    @endif
                                    <th class="px-3 py-2 text-right w-40 border-b">Jumlah Anggaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($itemsQr as $idx => $item)
                                    @if (!empty($item['has_sub']) && !empty($item['sub_items']))
                                        {{-- Header Kegiatan Utama --}}
                                        <tr class="bg-gray-100 font-semibold text-gray-900 border-b">
                                            <td class="px-3 py-2 text-center text-gray-500">{{ $idx + 1 }}</td>
                                            <td class="px-3 py-2">{{ $item['uraian'] ?? '-' }}</td>
                                            @if ($hasMakQr)
                                                <td class="px-3 py-2 text-center font-mono text-gray-600">{{ $item['mak'] ?? '-' }}</td>
                                            @endif
                                            <td class="px-3 py-2 text-right font-bold text-emerald-700">
                                                Rp {{ number_format((float)($item['nominal'] ?? 0), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        {{-- Sub-kegiatan --}}
                                        @foreach ($item['sub_items'] as $sub)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="px-3 py-2 text-center"></td>
                                                <td class="px-3 py-2 pl-6 text-gray-700">
                                                    {{ $sub['uraian'] ?? '-' }}
                                                    @if (!empty($sub['volume']) && !empty($sub['satuan']))
                                                        <span class="text-gray-500 text-[11px] block sm:inline sm:ml-1">({{ $sub['volume'] }} {{ $sub['satuan'] }} @ Rp {{ number_format((float)($sub['harga_satuan'] ?? 0), 0, ',', '.') }})</span>
                                                    @endif
                                                </td>
                                                @if ($hasMakQr)
                                                    <td class="px-3 py-2 text-center text-gray-400">-</td>
                                                @endif
                                                <td class="px-3 py-2 text-right text-gray-700">
                                                    Rp {{ number_format((float)($sub['nominal'] ?? 0), 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-3 py-2 text-center font-medium text-gray-500">{{ $idx + 1 }}</td>
                                            <td class="px-3 py-2 font-medium text-gray-900">
                                                {{ $item['uraian'] ?? '-' }}
                                                @if (!empty($item['volume']) && !empty($item['satuan']))
                                                    <span class="text-gray-500 text-[11px] block sm:inline sm:ml-1">({{ $item['volume'] }} {{ $item['satuan'] }} @ Rp {{ number_format((float)($item['harga_satuan'] ?? 0), 0, ',', '.') }})</span>
                                                @endif
                                            </td>
                                            @if ($hasMakQr)
                                                <td class="px-3 py-2 text-center font-mono text-gray-600">{{ $item['mak'] ?? '-' }}</td>
                                            @endif
                                            <td class="px-3 py-2 text-right font-semibold text-emerald-700">
                                                Rp {{ number_format((float)($item['nominal'] ?? ($item['subtotal'] ?? 0)), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        @if ($surat->jenisSurat->user_type == 'staff-dekan' && $surat->jenisSurat->slug == 'surat-keluar')
            <div class="overflow-x-auto border-2 border-slate-300 rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Nomor Surat:</td>
                            <td class="px-4 py-3">
                                @php
                                    $noSuratKeluarQr = $surat->data['noSurat'] ?? null;
                                    if (!empty($noSuratKeluarQr)) {
                                        if (!str_contains($noSuratKeluarQr, '/')) {
                                            $tahunSuratKeluar = isset($surat->data['tanggal_selesai'])
                                                ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ')
                                                : ($surat->created_at ? $surat->created_at->year : date('Y'));
                                            $noSuratKeluarQr = $noSuratKeluarQr . '/UN30.7/PP/' . $tahunSuratKeluar;
                                        }
                                    } else {
                                        $noSuratKeluarQr = '-';
                                    }
                                @endphp
                                {{ $noSuratKeluarQr }}
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Tanggal Surat Diterbitkan:</td>
                            <td class="px-4 py-3">{{ $surat->data['tanggal_selesai'] ?? '-' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Jenis Surat / Perihal:</td>
                            <td class="px-4 py-3">{{ $surat->data['perihal'] ?? ($surat->jenisSurat->name ?? '-') }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50">Ditandatangani oleh:</td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['namaWD1'] ?? ($surat->data['private']['namaWD'] ?? ($surat->data['private']['namaDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['deksripsiWD1'] ?? ($surat->data['private']['deskripsiWD'] ?? ($surat->data['private']['deskripsiDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                        <tr class="border-b">
                            <td class="px-4 py-3 font-semibold bg-gray-50"></td>
                            <td class="px-4 py-3">
                                {{ $surat->data['private']['nipWD1'] ?? ($surat->data['private']['nipWD'] ?? ($surat->data['private']['nipDekan'] ?? '(Nama tidak tersedia)')) }}
                            </td>
                        </tr>

                    </tbody>
                </table>


            </div>

            <div class="overflow-x-auto  border-2 border-slate-300 rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-700 bg-white">
                    <tbody>
                        @foreach ($surat->data as $key => $value)
                            @if (in_array($key, [
                                    'private',
                                    'ttdWD',
                                    'ttdWD1',
                                    'noSurat',
                                    'note',
                                    'tanggal_selesai',
                                    'nama',
                                    'username',
                                    'email',
                                    'perihal',
                                ]))
                                @continue
                            @endif


                            @if ($key == 'dosen')
                                @foreach ($value as $id => $data)
                                    @foreach ($data as $key => $value)
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold bg-gray-50">
                                                {{ convertToTitleCase($key) }}:</td>
                                            <td class="px-4 py-3">
                                                {!! html_entity_decode($value) !!}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                @continue
                            @endif

                            @if ($value != null)
                                <tr class="border-b">
                                    <td class="px-4 py-3 font-semibold bg-gray-50">
                                        {{ convertToTitleCase($key) }}:</td>
                                    <td class="px-4 py-3">
                                        {!! html_entity_decode($value) !!}</td>
                                </tr>
                            @endif
                        @endforeach


                    </tbody>
                </table>


            </div>
        @endif

        <p class="text-center mt-6  text-gray-700  mb-6">Untuk memastikan bahwa Anda mengakses data surat yang
            benar, pastikan URL pada
            browser berasal dari <a class="underline" href=" https://esurat.fkip.unib.ac.id">
                https://esurat.fkip.unib.ac.id</a></p>
        @php
            $url = URL::signedRoute('cetak-surat-qr', ['surat' => $surat->id]);
        @endphp


        {{-- <a href="{{ route('lihat-html-surat-qr', ['surat' => $surat->id]) }}">html</a> --}}
        {{-- <iframe src="{{ $url }}" width="100%" height="600"></iframe> --}}
        {{-- <div id="pdf-viewer" style="position: relative; width: 100%; margin: auto; overflow-x: auto;">
            <!-- Loading Animation -->
            <div id="loading-animation" class="mt-2 flex flex-row text-blue-800 italic"
                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <p>Loading PDF...</p>
                <x-loading />
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
        <script>
            const pdfUrl = "{{ $url }}";
            const loadingTask = pdfjsLib.getDocument(pdfUrl);
            const viewer = document.getElementById('pdf-viewer');
            const loadingAnimation = document.getElementById('loading-animation');
            const pixelRatio = window.devicePixelRatio || 1;

            function calculateOptimalScale(page, containerWidth) {
                const originalViewport = page.getViewport({
                    scale: 1.0
                });

                // Hitung scale hanya jika lebar PDF melebihi container
                if (originalViewport.width > containerWidth) {
                    return (containerWidth / originalViewport.width) * pixelRatio;
                }

                // Jika PDF lebih kecil dari container, gunakan ukuran asli
                return 1.0 * pixelRatio;
            }

            function renderPDFPage(page) {
                // Dapatkan ukuran container
                const containerWidth = viewer.clientWidth;

                // Buat wrapper div untuk mengatur posisi canvas
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                wrapper.style.display = 'flex';
                wrapper.style.justifyContent = 'center';
                wrapper.style.minWidth = 'min-content'; // Untuk memastikan wrapper tidak mengecil dari ukuran konten

                // Hitung scale optimal
                const scale = calculateOptimalScale(page, containerWidth);
                const viewport = page.getViewport({
                    scale: scale
                });

                // Buat dan setup canvas
                const canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                // Set display size yang sesuai dengan pixel ratio
                const displayWidth = viewport.width / pixelRatio;
                const displayHeight = viewport.height / pixelRatio;

                // Atur style canvas untuk mempertahankan ukuran asli
                canvas.style.width = `${displayWidth}px`;
                canvas.style.height = `${displayHeight}px`;
                canvas.style.display = 'block';

                // Setup context canvas dengan kualitas tinggi
                const context = canvas.getContext('2d', {
                    alpha: false,
                    antialias: true
                });

                context.imageSmoothingEnabled = true;
                context.imageSmoothingQuality = 'high';

                // Tambahkan canvas ke wrapper
                wrapper.appendChild(canvas);

                // Bersihkan viewer dan tambahkan wrapper
                viewer.innerHTML = '';
                viewer.appendChild(wrapper);

                // Render PDF dengan opsi yang dioptimalkan
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport,
                    enableWebGL: true,
                    renderInteractiveForms: true,
                    antialiasing: true
                };

                page.render(renderContext).promise.then(() => {
                    loadingAnimation.style.display = 'none';

                    // Setelah render selesai, atur tinggi container sesuai dengan konten
                    viewer.style.height = `${displayHeight}px`;
                });
            }

            // Fungsi untuk handle resize dengan debouncing
            let resizeTimeout;

            function handleResize() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    // Tampilkan loading animation
                    loadingAnimation.style.display = 'block';
                    viewer.appendChild(loadingAnimation);

                    // Re-render PDF
                    loadingTask.promise.then(pdf => {
                        return pdf.getPage(1).then(renderPDFPage);
                    });
                }, 250);
            }

            // Load PDF
            loadingTask.promise.then(pdf => {
                pdf.getPage(1).then(renderPDFPage);
            }).catch(error => {
                console.error('Error loading PDF:', error);
                loadingAnimation.innerHTML = '<p>Gagal memuat PDF.</p>';
            });

            // Event listener untuk resize
            window.addEventListener('resize', handleResize);
        </script> --}}
        {{-- <a href="{{ $url }}"
            class="block text-center px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Unduh</a> --}}
    </main>
</body>

</html>
