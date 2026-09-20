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
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top; width: 62%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 75px; vertical-align: top;">Nomor</td>
                        <td style="width: 10px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->data['noSurat']) ? $surat->data['noSurat'] : '..........' }}/UN30.7.10/KU/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : (isset($surat->created_at) ? $surat->created_at->format('Y') : date('Y')) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lampiran</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ !empty($surat->files['berkasProposal']) ? '1 (satu) Berkas Proposal & RAB' : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Perihal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;"><b>Usulan Pengajuan Dana {{ $surat->data['namaKegiatan'] ?? '' }}</b></td>
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

    <table class="data-table" style="margin-left: 30px; margin-bottom: 15px;">
        <tr>
            <td style="width: 170px;">Nama Pengaju / PIC</td>
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
