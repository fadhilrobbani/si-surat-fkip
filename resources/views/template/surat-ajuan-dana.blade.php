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
    <title>Surat Usulan Pengajuan Dana</title>
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
                        <td style="vertical-align: top;">{{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/UN30.7.11/KU.01.02/{{ $tahun }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lampiran</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->files['berkasProposal']) ? '1 (satu) Berkas Proposal & RAB' : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Perihal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">
                            <b>Usulan Pengajuan Dana {{ $surat->data['namaKegiatan'] ?? '' }} Prodi {{ $prodiName }}</b><br>
                            <b>{{ $jurusanName }} FKIP Universitas Bengkulu</b>
                        </td>
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
    <p style="text-indent: 30px;">FKIP Universitas Bengkulu</p>

    <br>
    <p style="text-align: justify; text-indent: 30px;">
        Sehubungan dengan akan dilaksanakannya Kegiatan {{ $surat->data['namaKegiatan'] ?? '' }}, bersama ini kami mengusulkan ajuan dana pengembangan Program Studi S1 {{ $prodiName }} Jurusan {{ $jurusanName }} FKIP Universitas Bengkulu Tahun Anggaran {{ $tahun }} dengan rincian sebagai berikut :
    </p>
    <br>

    <table class="border" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #000; padding: 6px; width: 40px; text-align: center;">No</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left;">Kegiatan / Uraian Kebutuhan Anggaran</th>
                <th style="border: 1px solid #000; padding: 6px; width: 150px; text-align: right;">Jumlah Total (Rp)</th>
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
                <td colspan="2" style="border: 1px solid #000; padding: 6px; text-align: right;">Jumlah Total</td>
                <td style="border: 1px solid #000; padding: 6px; text-align: right;">
                    Rp {{ number_format((float) ($surat->data['totalAnggaran'] ?? 0), 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <p style="text-align: justify; text-indent: 30px;">
        Atas perhatian dan kerjasama yang baik kami ucapkan terimakasih.
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
