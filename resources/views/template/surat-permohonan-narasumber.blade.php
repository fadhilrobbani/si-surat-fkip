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
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top; width: 62%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 75px; vertical-align: top;">Nomor</td>
                        <td style="width: 10px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/UN30.7/DT.06/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : (isset($surat->created_at) ? $surat->created_at->format('Y') : date('Y')) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lampiran</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->files['berkasProposal']) ? '1 (satu) Berkas' : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Perihal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;"><b>Permohonan Menjadi Narasumber</b></td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; text-align: right; width: 38%;">
                <p>{{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : (isset($surat->created_at) ? formatTimestampToDateIndonesian($surat->created_at) : '') }}</p>
            </td>
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

    <table class="data-table" style="margin-left: 30px; width: calc(100% - 30px); border-collapse: collapse;">
        <tr>
            <td style="width: 170px; vertical-align: top;">Hari / Tanggal</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['hariTanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Pukul / Waktu</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['waktu'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Tempat Kegiatan</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['tempatKegiatan'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Tema / Materi</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['temaMateri'] ?? '-' }}</td>
        </tr>
    </table>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Demikian permohonan ini kami sampaikan. Atas perhatian, kesediaan, dan kerja sama yang baik, kami ucapkan terima kasih.
    </p>
    <br><br>

    @php
        $dekanApproval = $surat->approvals
            ->where('isApproved', true)
            ->filter(function ($a) {
                $roleId = $a->user->role_id ?? 0;
                $roleName = strtolower($a->user->role->name ?? '');
                return $roleId == 7 || str_contains($roleName, 'dekan');
            })
            ->last();
        $dekanUser = $dekanApproval ? $dekanApproval->user : (isset($dekan) ? $dekan : null);

        $namaDekan = $surat->data['private']['namaDekan'] 
            ?? ($dekanUser ? $dekanUser->name : 'Dekan FKIP');

        $nipDekan = $surat->data['private']['nipDekan'] 
            ?? ($dekanUser ? ($dekanUser->nip ?: $dekanUser->username) : '........................');
    @endphp

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
            <p><b>{{ $namaDekan }}</b></p>
            <p>NIP {{ $nipDekan }}</p>
        </div>
    </div>
</body>

</html>
