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
    <title>Surat Permohonan Narasumber</title>
</head>

<body>
    @include('components.kop-v2', ['surat' => $surat])
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 15%;">Nomor</td>
            <td style="width: 45%;">: {{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/UN30.7/DT.06/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : (isset($surat->created_at) ? $surat->created_at->format('Y') : date('Y')) }}</td>
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
            <td>Perihal</td>
            <td>: <b>Permohonan Menjadi Narasumber</b></td>
            <td></td>
        </tr>
    </table>

    <br>
    <p>Kepada Yth.</p>
    <p><b>{{ $surat->data['namaNarasumber'] ?? '' }}</b></p>
    @if (!empty($surat->data['jabatanNarasumber']))
        <p>{{ $surat->data['jabatanNarasumber'] }}</p>
    @endif
    @if (!empty($surat->data['instansiNarasumber']))
        <p>{{ $surat->data['instansiNarasumber'] }}</p>
    @endif
    <p>di Tempat</p>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Sehubungan dengan rencana pelaksanaan kegiatan <b>{{ $surat->data['namaKegiatan'] ?? '' }}</b>
        Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu, bersama ini kami memohon kesediaan Bapak/Ibu untuk berkenan menjadi <b>Narasumber</b> pada kegiatan tersebut yang akan diselenggarakan pada:
    </p>
    <br>

    <table class="data-table" style="margin-left: 30px;">
        <tr>
            <td style="width: 150px;">Hari / Tanggal</td>
            <td>: {{ $surat->data['hariTanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pukul / Waktu</td>
            <td>: {{ $surat->data['waktu'] ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tempat Kegiatan</td>
            <td>: {{ $surat->data['tempatKegiatan'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Tema / Materi</td>
            <td>: {{ $surat->data['temaMateri'] ?? '-' }}</td>
        </tr>
    </table>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Demikian permohonan ini kami sampaikan. Atas perhatian, kesediaan, dan kerja sama yang baik, kami ucapkan terima kasih.
    </p>
    <br><br>

    <div class="tandatangan">
        <div>
            <p>Dekan,</p>
        </div>
        <div class="parent">
            @if ($surat->status == 'selesai')
                <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                    style="position: absolute; bottom: 20px;">
            @endif
        </div>
        <div>
            <p><b>{{ $surat->data['private']['namaDekan'] ?? (isset($dekan) ? $dekan->name : 'Dekan FKIP') }}</b></p>
            <p>NIP {{ $surat->data['private']['nipDekan'] ?? (isset($dekan) ? $dekan->username : '........................') }}</p>
        </div>
    </div>
</body>

</html>
