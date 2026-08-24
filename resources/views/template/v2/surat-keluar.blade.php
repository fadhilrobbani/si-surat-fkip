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
    <title>Surat Keluar</title>
</head>

<body>
    @include('components.kop-v2', ['surat' => $surat])
    <br>
    <table>
        <tr>
            <td style="vertical-align: top; width: 62%;">
                <table>
                    <tr>
                        <td style="width: 70px; vertical-align: top;">Nomor</td>
                        <td style="width: 8px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $surat->data['noSurat'] ?? '[NoSurat]' }}/UN30.7/PP/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : ($surat->created_at ? $surat->created_at->year : date('Y')) }}</td>
                    </tr>
                    @if (isset($surat->data['jumlahLampiran']) && $surat->data['jumlahLampiran'] > 0)
                        <tr>
                            <td style="vertical-align: top;">Lampiran</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">{{ $surat->data['jumlahLampiran'] . ' eksemplar' }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="vertical-align: top;">Perihal</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $surat->data['perihal'] ?? 'Perihal' }}</td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; text-align: right; width: 38%;">
                <p>{{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : \Carbon\Carbon::parse($surat->created_at ?? now())->translatedFormat('d F Y') }}</p>
            </td>
        </tr>
    </table>

    <br>
    <p>Yth.</p>
    <p>{{ isset($surat->data['tujuan2']) || isset($surat->data['tujuan3']) ? '1.' : '' }}
        {{ $surat->data['tujuan1'] ?? '' }}</p>
    @if (isset($surat->data['tujuan2']))
        <p>2. {{ $surat->data['tujuan2'] }}</p>
    @endif
    @if (isset($surat->data['tujuan3']))
        <p>3. {{ $surat->data['tujuan3'] }}</p>
    @endif

    <br>
    <p style="text-align: justify">{!! html_entity_decode($surat->data['paragrafAwal'] ?? '') !!}</p>
    <br>

    @if (!empty($surat->data['tanggalPelaksanaan']) || !empty($surat->data['waktu']) || !empty($surat->data['tempat']))
        <table class="data-table">
            @if (!empty($surat->data['tanggalPelaksanaan']))
                <tr>
                    <td valign='top'>Hari, tanggal</td>
                    <td>: {{ $surat->data['tanggalPelaksanaan'] }}</td>
                </tr>
            @endif

            @if (!empty($surat->data['waktu']))
                <tr>
                    <td>Waktu</td>
                    <td>: {{ $surat->data['waktu'] }}</td>
                </tr>
            @endif

            @if (!empty($surat->data['tempat']))
                <tr>
                    <td valign='top' style="width: 120px">Tempat</td>
                    <td>: {{ $surat->data['tempat'] }}</td>
                </tr>
            @endif
        </table>
        <br>
    @endif

    @php
        $paragrafAkhir = $surat->data['paragrafAkhir'] ?? '';
        $adaPenutup = !empty($paragrafAkhir) && \Illuminate\Support\Str::contains(strtolower($paragrafAkhir), 'demikian');
    @endphp

    @if (!empty($paragrafAkhir))
        <p style="text-align: justify">{!! html_entity_decode($paragrafAkhir) !!}</p>
        <br>
    @endif

    @if (!$adaPenutup)
        <p style="text-align: justify">Demikianlah, atas perhatian dan kerjasama yang baik kami ucapkan terima kasih.</p>
        <br><br>
    @else
        <br>
    @endif

    <div>
        <div class="tandatangan">
            <div>
                @if (isset($surat->data['private']['deskripsiWD']))
                    <p>a.n. Dekan</p>
                    <p>{{ $surat->data['private']['deskripsiWD'] }}</p>
                @elseif (isset($surat->data['private']['deskripsiDekan']))
                    <p>{{ $surat->data['private']['deskripsiDekan'] }}</p>
                @else
                    <p>Dekan,</p>
                @endif
            </div>
            <div class="parent">
                @if ($surat->status == 'selesai')
                    <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                        style="position: absolute; bottom:70px">
                @endif
            </div>
            <div>
                <p>{{ $surat->data['private']['namaWD'] ?? ($surat->data['private']['namaDekan'] ?? ($surat->data['private']['namaWD1'] ?? '[Nama Dekan]')) }}</p>
                <p>NIP {{ $surat->data['private']['nipWD'] ?? ($surat->data['private']['nipDekan'] ?? ($surat->data['private']['nipWD1'] ?? '[NIP Dekan]')) }}</p>
            </div>
        </div>
    </div>

    <br>
    @if (isset($surat->data['private']['tembusan']) && !empty($surat->data['private']['tembusan']))
        <div class="distribution-list">
            <p>Tembusan:</p>
            <ol style="margin-left: 20px">
                @foreach ($surat->data['private']['tembusan'] as $tembusan)
                    <li>{{ $tembusan }}</li>
                @endforeach
            </ol>
        </div>
    @endif

</body>

</html>
