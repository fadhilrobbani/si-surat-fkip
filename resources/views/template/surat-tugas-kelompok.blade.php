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
    <p style="text-align: center"><b><u>SURAT TUGAS</u></b></p>
    <p style="text-align: center">
        <b>Nomor:&nbsp;{{ $surat->data['noSurat'] ?? 'NoSurat' }}/UN30.7/KP/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : 'Tahun' }}
        </b>
    </p>
    <br>
    <br>
    <p style="text-align: justify"> Dekan Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu menugaskan kepada:
    </p>
    <br>
    <table class="border">
        <tr>
            <td style="width: 20px">No.</td>
            <td>Nama</td>
            <td>NIP</td>
            <td>Jabatan</td>
        </tr>
        @foreach ($surat->data['dosen'] as $index => $dosen)
            <tr>
                <td style="width: 20px">{{ $loop->iteration }}</td>
                <td>{{ $dosen['namaDosen' . $loop->iteration] }}</td>
                <td>{{ $dosen['nipDosen' . $loop->iteration] }}</td>
                <td>{{ $dosen['jabatanDosen' . $loop->iteration] }}</td>
            </tr>
        @endforeach
    </table>
    <br>
    <p style="text-align: justify">Untuk melaksanakan kegiatan {{ $surat->data['acara'] }}, yang dilaksanakan pada:</p>
    <br>
    <table class="data-table">
        <tr>
            <td>Hari/Tanggal</td>
            <td>: {{ $surat->data['waktuPelaksanaan'] }}</td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>: {{ $surat->data['tempat'] }}</td>
        </tr>


    </table>
    {{-- <p>Nama&emsp; {{ $surat->pengaju->name }}</p>
    <p>NPM&emsp;: {{ $surat->pengaju->username }}</p>
    <p>Tempat/Tanggal Lahir&emsp;: {{ Str::title($surat->data['birthplace']) . ', ' . $surat->data['birthdate'] }}</p>
    <p>Program Studi&emsp;: {{ $surat->data['programStudi'] }}</p>
    <p>Nomor Seri Ijazah&emsp;: {{ $surat->data['noIjazah'] }}</p> --}}
    <br>

    <p style="text-align: justify">Demikianlah surat tugas ini dibuat untuk dapat dilaksanakan dengan sebaik-baiknya,
        dan setelah selesai diwajibkan memberikan laporan kepada pimpinan Fakultas.
    </p>
    <br><br>
    <div>

        {{-- ttd wd --}}

        @if (isset($surat->data['private']['nipWD']) &&
                isset($surat->data['private']['namaWD']) &&
                isset($surat->data['private']['deskripsiWD']))
            <div class="tandatangan">
                <div>
                    <p>Bengkulu,
                        {{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : '' }}
                    </p>
                    <p>a.n. Dekan</p>


                    <p> {{ isset($surat->data['private']['deskripsiWD']) ? $surat->data['private']['deskripsiWD'] : '(Wakil Dekan Bidang... / Dekan)' }}
                </div>
                <div class="parent">

                    @if ($surat->status == 'selesai')
                        <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                            style="position: absolute; bottom:70px">
                    @endif
                </div>
                <div>
                    <p> {{ isset($surat->data['private']['namaWD']) ? $surat->data['private']['namaWD'] : '(Nama)' }}
                    </p>
                    <p>NIP {{ isset($surat->data['private']['nipWD']) ? $surat->data['private']['nipWD'] : '(NIP)' }}
                    </p>
                </div>
            </div>
            {{-- ttd dekan --}}
        @elseif (isset($surat->data['private']['nipDekan']) &&
                isset($surat->data['private']['namaDekan']) &&
                isset($surat->data['private']['deskripsiDekan']))
            <div class="tandatangan">
                <div>
                    <p>Bengkulu,
                        {{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : '' }}
                    </p>
                    <p>Dekan</p>
                </div>
                <div class="parent">

                    @if ($surat->status == 'selesai')
                        <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                            style="position: absolute; bottom:70px">
                    @endif
                </div>
                <div>
                    <p> {{ isset($surat->data['private']['namaDekan']) ? $surat->data['private']['namaDekan'] : '(Nama)' }}
                    </p>
                    <p>NIP
                        {{ isset($surat->data['private']['nipDekan']) ? $surat->data['private']['nipDekan'] : '(NIP)' }}
                    </p>
                </div>
            </div>
        @else
            {{-- placeholder ttd --}}
            <div class="tandatangan">
                <div>
                    <p>Bengkulu,
                        {{ '' }}
                    </p>
                    <p>a.n. Dekan</p>


                    <p> {{ '(Wakil Dekan Bidang... / Dekan)' }}
                </div>
                <div class="parent">

                </div>
                <div>
                    <p> {{ '(Nama)' }}
                    </p>
                    <p>NIP {{ '(NIP)' }}
                    </p>
                </div>
            </div>

        @endif
    </div>
    <div class="page_break"></div>
    @include('template.sppd')

</body>

</html>
