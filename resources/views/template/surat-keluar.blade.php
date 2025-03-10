@php
    $url = URL::signedRoute('preview-surat-qr', [
        'surat' => $surat->id,
    ]);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('styles/surat-alumni.css') }}" type="text/css">
    <title>Print</title>
</head>

<body>
    @include('components.kop', ['surat' => $surat])
    <br>
    <table>
        <tr>
            <td>Nomor</td>
            <td>:
                {{ $surat->data['noSurat'] ?? '[NoSurat]' }}/UN30.7/PP/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : '[Tahun]' }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:
                {{ $surat->data['jumlahLampiran'] == 0 ? '-' : $surat->data['jumlahLampiran'] . ' eksemplar' }}
            </td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:
                {{ $surat->data['perihal'] ?? 'Perihal' }}
            </td>
        </tr>

    </table>

    <br>
    <p>Yth.</p>
    <p>{{ isset($surat->data['tujuan2']) || isset($surat->data['tujuan3']) ? '1.' : '' }}
        {{ $surat->data['tujuan1'] }}</p>
    @if (isset($surat->data['tujuan2']))
        <p>2. {{ $surat->data['tujuan2'] }}</p>
    @endif
    @if (isset($surat->data['tujuan3']))
        <p>3. {{ $surat->data['tujuan3'] }}</p>
    @endif
    <br>
    <p style="text-align: justify">{!! html_entity_decode($surat->data['paragrafAwal']) !!}</p>
    <br>
    @if ($surat->data['tanggalPelaksanaan'] != null || $surat->data['waktu'] != null || $surat->data['tempat'] != null)
        <table class="data-table">
            @if ($surat->data['tanggalPelaksanaan'] != null)
                <tr>
                    <td valign='top'>Hari, tanggal</td>
                    <td>: {{ $surat->data['tanggalPelaksanaan'] }}</td>
                </tr>
            @endif

            @if ($surat->data['waktu'] != null)
                <tr>
                    <td>Waktu</td>
                    <td>: {{ $surat->data['waktu'] }}</td>
                </tr>
            @endif

            @if ($surat->data['tempat'] != null)
                <tr>
                    <td valign='top' style="width: 120px">Tempat</td>
                    <td>: {{ $surat->data['tempat'] }}</td>
                </tr>
            @endif

        </table>
        <br>
    @endif

    {{-- <p>Nama&emsp; {{ $surat->pengaju->name }}</p>
    <p>NPM&emsp;: {{ $surat->pengaju->username }}</p>
    <p>Tempat/Tanggal Lahir&emsp;: {{ Str::title($surat->data['birthplace']) . ', ' . $surat->data['birthdate'] }}</p>
    <p>Program Studi&emsp;: {{ $surat->data['programStudi'] }}</p>
    <p>Nomor Seri Ijazah&emsp;: {{ $surat->data['noIjazah'] }}</p> --}}

    <p style="text-align: justify">{!! html_entity_decode($surat->data['paragrafAkhir']) !!}</p>
    <br><br>
    <div>
        <div class="tandatangan">
            <div>
                <p>Dekan,
                </p>

            </div>
            <div class="parent">
                {{-- @if (isset($surat->files['private']['ttdWD1']))
                    <img class="ttd" style="margin-left: 40px" width="100px"
                        src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($surat->files['private']['ttdWD1']))) }}"
                        alt="ttd">
                @endif
                @if (isset($surat->files['private']['stempel']))
                    <img class="stempel" style="margin-left: 40px" width="120px"
                        src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($surat->files['private']['stempel']))) }}"
                        alt="stempel">
                @endif --}}

                @if ($surat->status == 'selesai')
                    <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                        style="position: absolute; bottom:70px">
                @endif
            </div>
            <div>
                <p> {{ isset($surat->data['private']['namaDekan']) ? $surat->data['private']['namaDekan'] : '[Nama Dekan]' }}
                </p>
                <p>NIP
                    {{ isset($surat->data['private']['nipDekan']) ? $surat->data['private']['nipDekan'] : '[NIP Dekan]' }}
                </p>
            </div>
        </div>
        {{-- @if ($surat->status == 'selesai' && !request()->hasValidSignature())
            <img src="data:image/svg;base64, {!! base64_encode(
                QrCode::format('svg')->size(90)->generate($url),
            ) !!}" style="position: absolute; bottom:70px">
        @endif --}}
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
