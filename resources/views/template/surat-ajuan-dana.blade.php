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

    $cleanProdiName = preg_replace('/^Program Studi\s+/i', '', trim($prodiName));
    $cleanJurusanName = !empty($jurusanName) 
        ? \Illuminate\Support\Str::start(preg_replace('/^Jurusan\s+/i', '', trim($jurusanName)), 'Jurusan ') 
        : '';
    $cleanNamaKegiatan = preg_replace('/^Kegiatan\s+/i', '', trim($surat->data['namaKegiatan'] ?? ''));
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('styles/surat-alumni.css') }}" type="text/css">
    <title>Surat Usulan Pengajuan Dana</title>
    <style>
        table.border {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12pt;
        }
        table.border th,
        table.border td {
            font-size: 12pt;
            border: 1px solid #000;
            padding: 5px 6px;
        }
        table.border th {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
        }
    </style>
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
                        <td style="vertical-align: top;">{{ formatNomorSurat($surat->data['noSurat'] ?? null) }}</td>
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
                            Usulan Pengajuan Dana {{ $cleanNamaKegiatan }} Prodi {{ $cleanProdiName }}<br>
                            {{ $cleanJurusanName }} FKIP Universitas Bengkulu
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
        Sehubungan dengan akan dilaksanakannya Kegiatan {{ $cleanNamaKegiatan }}, bersama ini kami mengusulkan ajuan dana pengembangan Program Studi {{ $cleanProdiName }} {{ $cleanJurusanName }} FKIP Universitas Bengkulu Tahun Anggaran {{ $tahun }} dengan rincian sebagai berikut :
    </p>
    <br>

    @php
        $items = $surat->data['items'] ?? [];
        $hasMakColumn = false;
        foreach ($items as $it) {
            if (!empty($it['mak'])) {
                $hasMakColumn = true;
                break;
            }
        }
    @endphp

    <table class="border" style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 12pt;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="border: 1px solid #000; padding: 5px 6px; width: 35px; text-align: center; font-size: 12pt;">No</th>
                <th style="border: 1px solid #000; padding: 5px 6px; text-align: left; font-size: 12pt;">Kegiatan / Uraian Kebutuhan Anggaran</th>
                @if ($hasMakColumn)
                    <th style="border: 1px solid #000; padding: 5px 6px; width: 110px; text-align: center; font-size: 12pt;">Kode Akun / MAK</th>
                @endif
                <th style="border: 1px solid #000; padding: 5px 6px; width: 140px; text-align: right; font-size: 12pt;">Jumlah Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $idx => $item)
                @if (!empty($item['has_sub']) && !empty($item['sub_items']))
                    {{-- Row Kegiatan Utama / Header --}}
                    <tr style="background-color: #f9f9f9;">
                        <td style="border: 1px solid #000; padding: 5px 6px; text-align: center; font-size: 12pt;">{{ $idx + 1 }}</td>
                        <td style="border: 1px solid #000; padding: 5px 6px; font-size: 12pt;">{{ $item['uraian'] ?? '-' }}</td>
                        @if ($hasMakColumn)
                            <td style="border: 1px solid #000; padding: 5px 6px; text-align: center; font-size: 12pt;">
                                {{ $item['mak'] ?? '-' }}
                            </td>
                        @endif
                        <td style="border: 1px solid #000; padding: 5px 6px; text-align: right; font-size: 12pt;">
                            {{ number_format((float) ($item['nominal'] ?? 0), 0, ',', '.') }}
                        </td>
                    </tr>
                    {{-- Sub-items rincian --}}
                    @foreach ($item['sub_items'] as $sIdx => $sub)
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px 6px; text-align: center; font-size: 12pt;"></td>
                            <td style="border: 1px solid #000; padding: 5px 6px 5px 18px; font-size: 12pt;">
                                {{ $sub['uraian'] ?? '-' }}
                                @if (!empty($sub['volume']) && !empty($sub['satuan']))
                                    <span style="color: #555;">({{ $sub['volume'] }} {{ $sub['satuan'] }} @ Rp {{ number_format((float) ($sub['harga_satuan'] ?? 0), 0, ',', '.') }})</span>
                                @endif
                            </td>
                            @if ($hasMakColumn)
                                <td style="border: 1px solid #000; padding: 5px 6px; text-align: center; color: #888; font-size: 12pt;">-</td>
                            @endif
                            <td style="border: 1px solid #000; padding: 5px 6px; text-align: right; font-size: 12pt;">
                                {{ number_format((float) ($sub['nominal'] ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{-- Flat mode --}}
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px 6px; text-align: center; font-size: 12pt;">{{ $idx + 1 }}</td>
                        <td style="border: 1px solid #000; padding: 5px 6px; font-size: 12pt;">{{ $item['uraian'] ?? '-' }}</td>
                        @if ($hasMakColumn)
                            <td style="border: 1px solid #000; padding: 5px 6px; text-align: center; font-size: 12pt;">
                                {{ $item['mak'] ?? '-' }}
                            </td>
                        @endif
                        <td style="border: 1px solid #000; padding: 5px 6px; text-align: right; font-size: 12pt;">
                            {{ number_format((float) ($item['nominal'] ?? 0), 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="{{ $hasMakColumn ? 4 : 3 }}" style="border: 1px solid #000; padding: 5px 6px; text-align: center; font-size: 12pt;">Tidak ada rincian</td>
                </tr>
            @endforelse
            <tr style="font-weight: bold; background-color: #fafafa;">
                <td colspan="{{ $hasMakColumn ? 3 : 2 }}" style="border: 1px solid #000; padding: 5px 6px; text-align: right; font-size: 12pt;">Jumlah Total</td>
                <td style="border: 1px solid #000; padding: 5px 6px; text-align: right; font-size: 12pt;">
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
            <p>{{ $namaKaprodi }}</p>
            <p>NIP {{ $nipKaprodi }}</p>
        </div>
    </div>
</body>

</html>
