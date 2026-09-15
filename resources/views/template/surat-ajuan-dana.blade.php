@php
    $url = URL::signedRoute('preview-surat-qr', [
        'surat' => $surat->id,
    ]);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('styles/surat-alumni.css') }}" type="text/css">
    <title>Surat Usulan Pengajuan Dana</title>
</head>

<body>
    @include('components.kop-v2', ['surat' => $surat])
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 15%;">Nomor</td>
            <td style="width: 45%;">: {{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/UN30.7.10/KU/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : (isset($surat->created_at) ? $surat->created_at->format('Y') : date('Y')) }}</td>
            <td style="width: 40%; text-align: right;">
                {{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : (isset($surat->created_at) ? formatTimestampToDateIndonesian($surat->created_at) : '') }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>: {{ !empty($surat->files['berkasProposal']) ? '1 (satu) Berkas Proposal & RAB' : '-' }}</td>
            <td></td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Perihal</td>
            <td>: <b>Usulan Pengajuan Dana {{ $surat->data['namaKegiatan'] ?? '' }}</b></td>
            <td></td>
        </tr>
    </table>

    <br>
    <p>Yth. Wakil Dekan Bidang Keuangan dan Umum</p>
    <p>FKIP Universitas Bengkulu</p>
    <p>di Bengkulu</p>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Bersama ini kami mengusulkan ajuan dana kegiatan <b>“{{ $surat->data['namaKegiatan'] ?? '' }}”</b>
        @if (!empty($surat->data['namaOrganisasi']))
            oleh <b>{{ $surat->data['namaOrganisasi'] }}</b>
        @elseif (!empty($surat->data['programStudi']))
            Program Studi {{ $surat->data['programStudi'] }}
        @endif
        Tahun Anggaran {{ $surat->data['tahunAnggaran'] ?? date('Y') }} dengan rincian kebutuhan sebagai berikut:
    </p>
    <br>

    <table class="border" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #000; padding: 6px; width: 40px; text-align: center;">No</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left;">Uraian Kebutuhan Anggaran</th>
                <th style="border: 1px solid #000; padding: 6px; width: 140px; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $items = $surat->data['items'] ?? [];
            @endphp
            @forelse ($items as $idx => $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $idx + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item['uraian'] ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: right;">
                        {{ number_format((float) ($item['nominal'] ?? 0), 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="border: 1px solid #000; padding: 6px; text-align: center;">Tidak ada rincian</td>
                </tr>
            @endforelse
            <tr style="font-weight: bold; background-color: #fafafa;">
                <td colspan="2" style="border: 1px solid #000; padding: 6px; text-align: right;">Total Usulan Dana:</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: right;">
                    Rp {{ number_format((float) ($surat->data['totalAnggaran'] ?? 0), 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; font-size: 13px; margin-bottom: 10px;">
        <tr>
            <td style="width: 150px;">Nama Pengaju / PIC</td>
            <td>: {{ $surat->data['nama'] ?? ($surat->data['namaPengaju'] ?? '-') }} ({{ $surat->data['username'] ?? ($surat->data['usernamePengaju'] ?? '-') }})</td>
        </tr>
        @if (!empty($surat->data['jabatanPengaju']))
        <tr>
            <td>Jabatan</td>
            <td>: {{ $surat->data['jabatanPengaju'] }}</td>
        </tr>
        @endif
        @if (!empty($surat->data['namaBank']))
        <tr>
            <td>Penyaluran / Bank</td>
            <td>: {{ $surat->data['namaBank'] }} | No. Rek: {{ $surat->data['nomorRekening'] ?? '-' }} a.n {{ $surat->data['atasNamaRekening'] ?? '-' }}</td>
        </tr>
        @endif
    </table>

    <p style="text-align: justify; text-indent: 30px;">
        Demikian usulan ini kami sampaikan. Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.
    </p>
    <br><br>

    <div class="tandatangan">
        <div>
            <p>a.n. Dekan,</p>
            <p>Wakil Dekan Bidang Keuangan dan Umum,</p>
        </div>
        <div class="parent">
            @if ($surat->status == 'selesai')
                <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                    style="position: absolute; bottom: 20px;">
            @endif
        </div>
        <div>
            <p><b>{{ $surat->data['private']['namaWD2'] ?? 'Wakil Dekan II' }}</b></p>
            <p>NIP {{ $surat->data['private']['nipWD2'] ?? '........................' }}</p>
        </div>
    </div>
</body>

</html>
