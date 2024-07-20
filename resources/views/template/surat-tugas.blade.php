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
    <table class="kop">
        <tr>

            <td>
                <img class="logo"
                    src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logounib.png'))) }}"
                    alt="logo">
            </td>
            <td class="kop-text">
                <center>
                    <font size="4">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</font><br>
                    <font size="4">RISET, DAN TEKNOLOGI</font><br>
                    <font size="4"><b>UNIVERSITAS BENGKULU</b></font><br>
                    <font size="4"><b>FAKULTAS KEGURUAN DAN ILMU PENDIDIKAN</b></font><br>
                    <font size="2">Jalan WR. Supratman Kandang Limun Bengkulu 38371A</font>
                    <br>
                    <font size="2">Telepon: (0736) 21170, Psw.203-232, 21186 Faksimile: (0736) 21186</font><br>
                    <font size="2">Laman: <i>http://www.fkip.unib.ac.id </i> <i>e-mail</i>:
                        fkip@unib.ac.id </font>
                </center>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <hr>
            </td>
        </tr>

    </table>
    <br>
    <p style="text-align: center"><b><u>SURAT TUGAS</u></b></p>
    <p style="text-align: center">
        <b>Nomor:&nbsp;{{ $surat->data['noSurat'] ?? 'NoSurat' }}/UN30.7/KP/{{ Carbon\Carbon::now()->year }} </b>
    </p>
    <br>
    <br>
    <p style="text-align: justify"> Dekan Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu menugaskan kepada:
    </p>
    <br>

    <table>
        @foreach ($surat->data['dosen'] as $dosen)
            <tr>
                <td>Nama</td>
                <td>: {{ Str::title($dosen['namaDosen']) }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>: {{ $dosen['nipDosen'] }}</td>
            </tr>
            <tr>
                <td>Pangkat/Golongan</td>
                <td>: {{ $dosen['pangkatDosen'] }}</td>
            </tr>
            <tr>
                <td>Jabatan Fungsional</td>
                <td>: {{ $dosen['jabatanFungsionalDosen'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td>Unit Kerja</td>
            <td>: FKIP Universitas Bengkulu</td>
        </tr>
        <tr>
            <td>Acara</td>
            <td>: {{ $surat->data['acara'] }}</td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>: {{ $surat->data['tempat'] }}</td>
        </tr>
        <tr>
            <td>Waktu Pelaksanaan</td>
            <td>: {{ $surat->data['waktuPelaksanaan'] }}</td>
        </tr>
        <tr>
            <td>Dasar Penugasan</td>
            <td>: {{ $surat->data['dasarPenugasan'] }}</td>
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
        <div class="tandatangan">
            <div>
                <p>Bengkulu,
                    {{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : '' }}
                </p>
                <p>a.n. Dekan </p>
                <p> {{ isset($surat->data['private']['deskripsiWD']) ? $surat->data['private']['deskripsiWD'] : '(Wakil Dekan Bidang...)' }}
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
                <p> {{ isset($surat->data['private']['namaWD']) ? $surat->data['private']['namaWD'] : '(Nama WD)' }}
                </p>
                <p>NIP {{ isset($surat->data['private']['nipWD']) ? $surat->data['private']['nipWD'] : '(NIP WD)' }}
                </p>
            </div>
        </div>
        {{-- @if ($surat->status == 'selesai' && !request()->hasValidSignature())
            <img src="data:image/svg;base64, {!! base64_encode(
                QrCode::format('svg')->size(90)->generate($url),
            ) !!}" style="position: absolute; bottom:70px">
        @endif --}}
    </div>
    <div class="page_break"></div>
    @include('template.sppd')
</body>

</html>
