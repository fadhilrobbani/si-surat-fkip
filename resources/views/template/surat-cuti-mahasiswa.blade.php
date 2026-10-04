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
    $cleanProdiName = preg_replace('/^Program Studi\s+/i', '', trim($prodiName));
    $cleanJurusanName = !empty($jurusanName)
        ? \Illuminate\Support\Str::start(preg_replace('/^Jurusan\s+/i', '', trim($jurusanName)), 'Jurusan ')
        : '';

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

    $lamaCuti = (int) ($surat->data['lamaCuti'] ?? 2);
    $terbilang = [1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat', 5 => 'lima', 6 => 'enam', 7 => 'tujuh', 8 => 'delapan'];
    $lamaCutiTeks = ($terbilang[$lamaCuti] ?? $lamaCuti);
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('styles/surat-alumni.css') }}" type="text/css">
    <title>Surat Permohonan Cuti Akademik Mahasiswa</title>
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
                        <td style="vertical-align: top;">{{ formatNomorSuratDenganFormat($surat->data['noSurat'] ?? null, '/DST/UN30.7.9/DT.00.00/', $surat) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lampiran</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->files['berkasPendukung']) ? '1 (satu) Berkas' : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Hal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">Permohonan Cuti Akademik</td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; text-align: right; width: 38%;">
                <p>{{ $surat->status == 'selesai' ? formatTanggalSurat($surat->data['tanggal_selesai'] ?? null) : (isset($surat->created_at) ? formatTimestampToDateIndonesian($surat->created_at) : '') }}</p>
            </td>
        </tr>
    </table>

    <br>
    <p>Yth. Wakil Dekan Bidang Akademik</p>
    <p>FKIP Universitas Bengkulu</p>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Bersama ini kami sampaikan bahwa mahasiswa Program Studi {{ $cleanProdiName }} {{ $cleanJurusanName }} FKIP Universitas Bengkulu berikut ini:
    </p>
    <br>

    <table class="border" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 30px; text-align: center;">No</td>
            <td>Nama</td>
            <td>NPM</td>
            <td>Program Studi</td>
            <td>Alasan Cuti</td>
        </tr>
        @foreach ($surat->data['mahasiswa'] ?? [] as $index => $mhs)
            <tr>
                <td style="width: 30px; text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ $mhs['nama'] ?? '-' }}</td>
                <td>{{ $mhs['npm'] ?? '-' }}</td>
                <td>{{ $mhs['programStudi'] ?? $prodiName }}</td>
                <td>{{ $mhs['alasanCuti'] ?? '-' }}</td>
            </tr>
        @endforeach
    </table>
    <br>

    <p style="text-align: justify; text-indent: 30px;">
        tidak dapat mengikuti perkuliahan pada semester ganjil dan semester genap TA. {{ $surat->data['tahunAkademik'] ?? '-' }}. Oleh karena itu, kami mohon mahasiswa yang bersangkutan dapat diberikan izin cuti akademik selama {{ $lamaCuti }} ({{ $lamaCutiTeks }}) semester.
    </p>
    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.
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
</body>

</html>
