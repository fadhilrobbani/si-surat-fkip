@php
    $url = URL::signedRoute('preview-surat-qr', [
        'surat' => $surat->id,
    ]);

    $dasar = trim($surat->data['dasarPenugasan'] ?? '');
    $teksDasar = '';
    if (!empty($dasar)) {
        $awalanBaku = ['berdasarkan', 'menindaklanjuti', 'sehubungan', 'sesuai', 'merujuk'];
        $sudahAdaAwalan = \Illuminate\Support\Str::startsWith(strtolower($dasar), $awalanBaku);
        $teksDasar = $sudahAdaAwalan ? $dasar : 'Berdasarkan ' . $dasar;
    }
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ public_path('styles/surat-alumni.css') }}" type="text/css">
    <title>Surat Tugas</title>
</head>

<body>
    @include('components.kop-v2', ['surat' => $surat])
    <br>
    <p style="text-align: center"><b><u>SURAT TUGAS</u></b></p>
    <p style="text-align: center">
        <b>Nomor:&nbsp;{{ $surat->data['noSurat'] ?? 'NoSurat' }}/UN30.7/KP/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : 'Tahun' }}
        </b>
    </p>
    <br>
    <br>
    <p style="text-align: justify">
        @if (!empty($teksDasar))
            {{ $teksDasar }}, maka
        @endif
        Dekan Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu menugaskan kepada:
    </p>
    <br>

    <table class="data-table">
        @php
            $dosenList = $surat->data['dosen'] ?? [];
        @endphp
        @foreach ($dosenList as $dosen)
            <tr>
                <td>Nama</td>
                <td>: {{ $dosen['namaDosen'] ?? ($dosen['nama'] ?? '') }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>: {{ $dosen['nipDosen'] ?? ($dosen['nip'] ?? '') }}</td>
            </tr>
            <tr>
                <td>Pangkat dan Golongan</td>
                <td>: {{ $dosen['pangkatDosen'] ?? ($dosen['pangkat'] ?? '-') }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: {{ $dosen['jabatanFungsionalDosen'] ?? ($dosen['jabatanDosen'] ?? ($dosen['jabatan'] ?? '-')) }}</td>
            </tr>
        @endforeach
    </table>
    <br>

    <p style="text-align: justify">Untuk melaksanakan kegiatan {{ $surat->data['acara'] ?? '' }}, yang dilaksanakan pada:</p>
    <br>
    <table class="data-table">
        @if (!empty($surat->data['waktuPelaksanaan']))
            <tr>
                <td>Waktu Pelaksanaan</td>
                <td>: {{ $surat->data['waktuPelaksanaan'] }}</td>
            </tr>
        @endif
        @if (!empty($surat->data['tempat']))
            <tr>
                <td>Tempat</td>
                <td>: {{ $surat->data['tempat'] }}</td>
            </tr>
        @endif
    </table>
    <br>

    <p style="text-align: justify">Demikianlah surat tugas ini dibuat untuk dapat dilaksanakan dengan sebaik-baiknya, dan tidak mengganggu tugas pokok. Setelah selesai diwajibkan memberikan laporan kepada pimpinan Fakultas.
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
                    <p> {{ isset($surat->data['private']['deskripsiWD']) ? $surat->data['private']['deskripsiWD'] : '(Wakil Dekan Bidang... / Dekan)' }}</p>
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
                        {{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : '' }}
                    </p>
                    <p>a.n. Dekan</p>
                    <p> {{ '(Wakil Dekan Bidang... / Dekan)' }}</p>
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
