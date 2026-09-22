@php
    $url = URL::signedRoute('preview-surat-qr', [
        'surat' => $surat->id,
    ]);

    $pengaju = $surat->pengaju;
    $prodi = $pengaju ? $pengaju->programStudi : null;
    $jurusan = $pengaju ? $pengaju->jurusan : null;
    if (!$jurusan && $prodi) {
        $jurusan = $prodi->jurusan;
    }
    $prodiName = $prodi ? $prodi->name : ($surat->data['programStudi'] ?? '');
    $jurusanName = $jurusan ? $jurusan->name : ($surat->data['jurusan'] ?? '');
    $tahun = isset($surat->data['tahunAnggaran']) 
        ? $surat->data['tahunAnggaran'] 
        : (isset($surat->created_at) ? $surat->created_at->format('Y') : date('Y'));

    $kaprodiApproval = $surat->approvals
        ->where('isApproved', true)
        ->filter(function ($a) {
            $roleId = $a->user->role_id ?? 0;
            $roleName = strtolower($a->user->role->name ?? '');
            return $roleId == 4 || str_contains($roleName, 'kaprodi');
        })
        ->last();

    $prodiId = $surat->pengaju->program_studi_id ?? ($prodi ? $prodi->id : null);
    $kaprodiUser = $kaprodiApproval ? $kaprodiApproval->user : null;
    if (!$kaprodiUser && $prodiId) {
        $kaprodiUser = \App\Models\User::where('role_id', 4)->where('program_studi_id', $prodiId)->first();
    }
    if (!$kaprodiUser && ($surat->pengaju->role_id ?? 0) == 4) {
        $kaprodiUser = $surat->pengaju;
    }

    $namaKaprodi = $surat->data['private']['namaKaprodi'] 
        ?? ($kaprodiUser ? $kaprodiUser->name : 'Koordinator Program Studi');
    $nipKaprodi = $surat->data['private']['nipKaprodi'] 
        ?? ($kaprodiUser ? ($kaprodiUser->nip ?: $kaprodiUser->username) : '........................');
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
                        <td style="vertical-align: top;">{{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/DST/UN30.7.10/DT.06/{{ $tahun }}</td>
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
    <p>Yth. {{ $surat->data['namaNarasumber'] ?? '' }}</p>
    @if (!empty($surat->data['instansiNarasumber']))
        <p>({{ $surat->data['instansiNarasumber'] }})</p>
    @endif
    <p>di Tempat</p>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Sehubungan akan dilaksanakannya Kegiatan {{ $surat->data['namaKegiatan'] ?? '' }} Program Studi {{ $prodiName }} Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu Tahun {{ $tahun }}, dengan ini kami mohon kesediaan Bapak/Ibu untuk dapat menjadi Narasumber kegiatan yang akan diselenggarakan pada:
    </p>
    <br>

    <table class="data-table" style="margin-left: 30px; width: calc(100% - 30px); border-collapse: collapse;">
        <tr>
            <td style="width: 140px; vertical-align: top;">Hari/Tanggal</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['hariTanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Pukul</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['waktu'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Acara</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['namaKegiatan'] ?? '-' }}</td>
        </tr>
        @if (!empty($surat->data['temaMateri']) && $surat->data['temaMateri'] != ($surat->data['namaKegiatan'] ?? ''))
        <tr>
            <td style="vertical-align: top;">Tema / Materi</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['temaMateri'] }}</td>
        </tr>
        @endif
        @if (!empty($surat->data['tempatKegiatan']))
        <tr>
            <td style="vertical-align: top;">Tempat</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['tempatKegiatan'] }}</td>
        </tr>
        @endif
    </table>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Demikianlah surat ini kami sampaikan. Atas perhatian dan kerjasama yang baik kami ucapkan terima kasih.
    </p>
    <br><br>

    <div class="tandatangan">
        <div>
            <p>Koordinator Prodi,</p>
        </div>
        <div class="parent">
            @if ($surat->status == 'selesai')
                <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                    style="position: absolute; bottom: 20px;">
            @endif
        </div>
        <div>
            <p><b>{{ $namaKaprodi }}</b></p>
            <p>NIP {{ $nipKaprodi }}</p>
        </div>
    </div>
</body>

</html>
