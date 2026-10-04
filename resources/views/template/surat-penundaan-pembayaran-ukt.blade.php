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
    $cleanProdiName = preg_replace('/^Program Studi\s+/i', '', trim($prodiName));

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

    $nomorEdaran = $surat->data['nomorSuratEdaran'] ?? '';
    $tanggalEdaran = $surat->data['tanggalSuratEdaran'] ?? '';
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('styles/surat-alumni.css') }}" type="text/css">
    <title>Surat Permohonan Penundaan/Penangguhan Pembayaran UKT</title>
</head>

<body>
    @include('components.kop-prodi', ['surat' => $surat])
    <br>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top; width: 62%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 75px; vertical-align: top;">Nomor</td>
                        <td style="width: 10px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ formatNomorSuratDenganFormat($surat->data['noSurat'] ?? null, '/UN30.7.10/RT/', $surat) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lampiran</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">Satu Lembar</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Perihal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">Permohonan Penangguhan Pembayaran UKT Mahasiswa</td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; text-align: right; width: 38%;">
                <p>{{ $surat->status == 'selesai' ? formatTanggalSurat($surat->data['tanggal_selesai'] ?? null) : (isset($surat->created_at) ? formatTimestampToDateIndonesian($surat->created_at) : '') }}</p>
            </td>
        </tr>
    </table>

    <br>
    <p>Yth. Dekan FKIP</p>
    <p>Universitas Bengkulu</p>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Dengan hormat,
    </p>
    <p style="text-align: justify; text-indent: 30px;">
        Sehubungan dengan surat edaran Rektor @if ($nomorEdaran) Nomor {{ $nomorEdaran }} @endif @if ($tanggalEdaran) tanggal {{ $tanggalEdaran }} @endif tentang pemberitahuan usulan penundaan/penangguhan pembayaran UKT mahasiswa, bersama ini kami kirimkan ajuan nama-nama mahasiswa yang ujian di rentang tanggal {{ $surat->data['rentangAwal'] ?? '-' }} sampai {{ $surat->data['rentangAkhir'] ?? '-' }} Program Studi {{ $cleanProdiName }} FKIP Universitas Bengkulu (nama-nama terlampir).
    </p>
    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Atas perhatian dan kerjasama yang baik, kami ucapkan terima kasih.
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
            <p>{{ $namaKaprodi }}</p>
            <p>NIP {{ $nipKaprodi }}</p>
        </div>
    </div>

    <div class="page_break"></div>

    <p style="text-align: center; margin-bottom: 0;"><b>DAFTAR NAMA-NAMA MAHASISWA</b></p>
    <p style="text-align: center; margin: 0;"><b>YANG MENGAJUKAN USULAN PENUNDAAN PEMBAYARAN UKT</b></p>
    <p style="text-align: center; margin: 0;"><b>PROGRAM STUDI {{ \Illuminate\Support\Str::upper($cleanProdiName) }}</b></p>
    <p style="text-align: center; margin-top: 0;"><b>FKIP UNIVERSITAS BENGKULU</b></p>
    <br>

    <table class="border" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 30px; text-align: center;">NO</td>
            <td>NPM</td>
            <td>NAMA</td>
            <td>HARI/TANGGAL UJIAN</td>
            <td>WAKTU</td>
        </tr>
        @foreach ($surat->data['mahasiswa'] ?? [] as $mhs)
            <tr>
                <td style="width: 30px; text-align: center;">{{ $loop->iteration }}.</td>
                <td>{{ $mhs['npm'] ?? '-' }}</td>
                <td>{{ $mhs['nama'] ?? '-' }}</td>
                <td>{{ $mhs['hariTanggalUjian'] ?? '-' }}</td>
                <td>{{ $mhs['waktu'] ?? '-' }}</td>
            </tr>
        @endforeach
    </table>

    <br><br>
    <div class="tandatangan">
        <div>
            <p>Bengkulu, {{ $surat->status == 'selesai' ? formatTanggalSurat($surat->data['tanggal_selesai'] ?? null) : (isset($surat->created_at) ? formatTimestampToDateIndonesian($surat->created_at) : '') }}</p>
            <p>Koordinator Prodi,</p>
        </div>
        <div class="parent">
            @if ($surat->status == 'selesai')
                <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                    style="position: absolute; bottom: 20px;">
            @endif
        </div>
        <div>
            <p>{{ $namaKaprodi }}</p>
            <p>NIP {{ $nipKaprodi }}</p>
        </div>
    </div>
</body>

</html>
