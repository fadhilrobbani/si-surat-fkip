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
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top; width: 62%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 75px; vertical-align: top;">Nomor</td>
                        <td style="width: 10px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/UN30.7/PP/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : (isset($surat->created_at) ? $surat->created_at->format('Y') : date('Y')) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lampiran</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->files['berkasProposal']) ? '1 (satu) Berkas' : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Hal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;"><b>Permohonan Peminjaman Ruang {{ $surat->data['namaRuangan'] ?? '' }}</b></td>
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
        Sehubungan dengan akan dilaksanakannya kegiatan <b>“{{ $surat->data['namaKegiatan'] ?? '' }}”</b>
        @if (!empty($surat->data['namaOrganisasi']))
            oleh <b>{{ $surat->data['namaOrganisasi'] }}</b>,
        @elseif (!empty($surat->data['programStudi']))
            Program Studi {{ $surat->data['programStudi'] }},
        @endif
        bersama ini kami mengajukan permohonan peminjaman ruangan dengan rincian sebagai berikut:
    </p>
    <br>

    <table class="data-table" style="margin-left: 30px; width: calc(100% - 30px); border-collapse: collapse;">
        <tr>
            <td style="width: 220px; vertical-align: top;">Ruangan / Fasilitas</td>
            <td style="width: 10px; vertical-align: top;">:</td>
            <td style="vertical-align: top;"><b>{{ $surat->data['namaRuangan'] ?? '-' }}</b></td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Hari / Tanggal Pemakaian</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['hariTanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Waktu / Jam Pemakaian</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['jamPemakaian'] ?? '-' }}</td>
        </tr>
        @if (!empty($surat->data['jumlahPeserta']))
        <tr>
            <td style="vertical-align: top;">Estimasi Jumlah Peserta</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['jumlahPeserta'] }} Orang</td>
        </tr>
        @endif
        <tr>
            <td style="vertical-align: top;">Nama Pemohon / Penanggungjawab</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['nama'] ?? ($surat->data['namaPengaju'] ?? '-') }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">NIP / NPM</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['username'] ?? ($surat->data['usernamePengaju'] ?? '-') }}</td>
        </tr>
        @if (!empty($surat->data['jabatanPengaju']))
        <tr>
            <td style="vertical-align: top;">Jabatan Organisasi</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">{{ $surat->data['jabatanPengaju'] }}</td>
        </tr>
        @endif
    </table>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Demikian permohonan ini kami sampaikan. Atas perhatian, izin, dan kerja sama yang baik, kami ucapkan terima kasih.
    </p>
    <br><br>

    @php
        $wdApproval = $surat->approvals
            ->where('isApproved', true)
            ->filter(function ($a) {
                $roleId = $a->user->role_id ?? 0;
                $roleName = strtolower($a->user->role->name ?? '');
                $roleDesc = strtolower($a->user->role->description ?? '');
                $userName = strtolower($a->user->name ?? '');
                return in_array($roleId, [8, 9, 10]) || 
                       str_contains($roleName, 'wd') || 
                       str_contains($roleName, 'wakil dekan') ||
                       str_contains($roleDesc, 'wakil dekan') ||
                       str_contains($userName, 'wakil dekan');
            })
            ->last();
        $wdUser = $wdApproval ? $wdApproval->user : null;

        $namaWD = $surat->data['private']['namaWD2'] 
            ?? $surat->data['private']['namaWD'] 
            ?? ($wdUser ? $wdUser->name : ($surat->data['private']['namaWD1'] ?? null));

        $nipWD = $surat->data['private']['nipWD2'] 
            ?? $surat->data['private']['nipWD'] 
            ?? ($wdUser ? ($wdUser->nip ?: $wdUser->username) : ($surat->data['private']['nipWD1'] ?? null));
    @endphp

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
            <p><b>{{ $namaWD ?? 'Wakil Dekan II' }}</b></p>
            <p>NIP {{ $nipWD ?? '........................' }}</p>
        </div>
    </div>
</body>

</html>
