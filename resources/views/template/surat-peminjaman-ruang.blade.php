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
    $jurusanName = $jurusan ? $jurusan->name : ($surat->data['jurusan'] ?? 'Pendidikan MIPA');
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
    <title>Surat Permohonan Peminjaman Ruang</title>
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
                        <td style="vertical-align: top;">{{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/DST/UN30.7.11/PP/{{ $tahun }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lampiran</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->files['berkasProposal']) ? '1 (satu) Berkas' : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Hal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;"><b>Permohonan Peminjaman {{ $surat->data['namaRuangan'] ?? 'Ruangan' }}</b></td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; text-align: right; width: 38%;">
                <p>{{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : (isset($surat->created_at) ? formatTimestampToDateIndonesian($surat->created_at) : '') }}</p>
            </td>
        </tr>
    </table>

    <br>
    <p>Yth. Wakil Dekan Bidang Keuangan dan Umum</p>
    <p>FKIP Universitas Bengkulu</p>
    <p>di Bengkulu</p>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Sehubungan dengan akan dilaksanakannya kegiatan <b>“{{ $surat->data['namaKegiatan'] ?? '' }}”</b> Program Studi {{ $prodiName }} Jurusan {{ $jurusanName }} (pamflet terlampir), bersama ini kami mengajukan permohonan peminjaman ruang {{ $surat->data['namaRuangan'] ?? '' }} pada :
    </p>
    <br>

    <table class="data-table" style="margin-left: 30px; width: calc(100% - 30px); border-collapse: collapse;">
        <tr>
            <td style="width: 140px; vertical-align: top;">Hari/tanggal</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['hariTanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Jam</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['jamPemakaian'] ?? '-' }} WIB</td>
        </tr>
    </table>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Demikian atas perhatian dan kerjasama yang baik, disampaikan terima kasih.
    </p>
    <br><br>

    <div class="tandatangan">
        <div>
            <p>Koordinator Program Studi,</p>
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
