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
    <title>Surat Permohonan Peminjaman Ruang</title>
</head>

<body>
    @include('components.kop-v2', ['surat' => $surat])
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 15%;">Nomor</td>
            <td style="width: 45%;">: {{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/UN30.7/PP/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : (isset($surat->created_at) ? $surat->created_at->format('Y') : date('Y')) }}</td>
            <td style="width: 40%; text-align: right;">
                {{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : (isset($surat->created_at) ? formatTimestampToDateIndonesian($surat->created_at) : '') }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>: {{ !empty($surat->files['berkasProposal']) ? '1 (satu) Berkas' : '-' }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Hal</td>
            <td>: <b>Permohonan Peminjaman Ruang {{ $surat->data['namaRuangan'] ?? '' }}</b></td>
            <td></td>
        </tr>
    </table>

    <br>
    <p>Yth. Wakil Dekan Bidang Keuangan dan Umum</p>
    <p>FKIP Universitas Bengkulu</p>
    <p>di Bengkulu</p>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Sehubungan dengan akan dilaksanakannya kegiatan <b>“{{ $surat->data['namaKegiatan'] ?? '' }}”</b>
        @if (!empty($surat->data['namaOrganisasi']))
            oleh <b>{{ $surat->data['namaOrganisasi'] }}</b>,
        @elseif (!empty($surat->data['programStudi']))
            Program Studi {{ $surat->data['programStudi'] }},
        @endif
        bersama ini kami mengajukan permohonan peminjaman ruangan dengan rincian sebagai berikut:
    </p>
    <br>

    <table class="data-table" style="margin-left: 30px;">
        <tr>
            <td style="width: 170px;">Ruangan / Fasilitas</td>
            <td>: <b>{{ $surat->data['namaRuangan'] ?? '-' }}</b></td>
        </tr>
        <tr>
            <td>Hari / Tanggal Pemakaian</td>
            <td>: {{ $surat->data['hariTanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <td>Waktu / Jam Pemakaian</td>
            <td>: {{ $surat->data['jamPemakaian'] ?? '-' }}</td>
        </tr>
        @if (!empty($surat->data['jumlahPeserta']))
        <tr>
            <td>Estimasi Jumlah Peserta</td>
            <td>: {{ $surat->data['jumlahPeserta'] }} Orang</td>
        </tr>
        @endif
        <tr>
            <td>Nama Pemohon / Penanggungjawab</td>
            <td>: {{ $surat->data['nama'] ?? ($surat->data['namaPengaju'] ?? '-') }}</td>
        </tr>
        <tr>
            <td>NIP / NPM</td>
            <td>: {{ $surat->data['username'] ?? ($surat->data['usernamePengaju'] ?? '-') }}</td>
        </tr>
        @if (!empty($surat->data['jabatanPengaju']))
        <tr>
            <td>Jabatan Organisasi</td>
            <td>: {{ $surat->data['jabatanPengaju'] }}</td>
        </tr>
        @endif
    </table>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Demikian permohonan ini kami sampaikan. Atas perhatian, izin, dan kerja sama yang baik, kami ucapkan terima kasih.
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
