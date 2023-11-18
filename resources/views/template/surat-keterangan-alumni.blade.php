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
                    <font size="4"><b>FAKULTAS KEGURUAN DAN PENDIDIKAN</b></font><br>
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
    <p style="text-align: center"><b><u>SURAT KETERANGAN ALUMNI</u></b></p>
    <p style="text-align: center">
        <b>Nomor:&nbsp;{{ $surat->data['noSurat'] ?? 'NoSurat' }}/UN30.7/PP/{{ Carbon\Carbon::now()->year }} </b>
    </p>
    <br>
    <br>
    <p style="text-align: justify">Wakil Dekan Bidang Akademik Fakultas Keguruan dan Ilmu Pendidikan Universitas
        Bengkulu menerangkan dengan
        sesungguhnya bahwa Saudara tersebut di bawah ini:</p>
    <br>
    <table>
        <tr>
            <td>Nama</td>
            <td>: {{ $surat->pengaju->name }}</td>
        </tr>
        <tr>
            <td>NPM</td>
            <td>: {{ $surat->pengaju->username }}</td>
        </tr>
        <tr>
            <td>Tempat/Tanggal Lahir</td>
            <td>: {{ Str::title($surat->data['tempatLahir']) . ', ' . $surat->data['tanggalLahir'] }}
            </td>
        </tr>
        <tr>
            <td>Nomor Seri Ijazah</td>
            <td>: {{ $surat->data['noIjazah'] }}</td>
        </tr>
    </table>
    {{-- <p>Nama&emsp; {{ $surat->pengaju->name }}</p>
    <p>NPM&emsp;: {{ $surat->pengaju->username }}</p>
    <p>Tempat/Tanggal Lahir&emsp;: {{ Str::title($surat->data['birthplace']) . ', ' . $surat->data['birthdate'] }}</p>
    <p>Program Studi&emsp;: {{ $surat->data['programStudi'] }}</p>
    <p>Nomor Seri Ijazah&emsp;: {{ $surat->data['noIjazah'] }}</p> --}}
    <br>
    <p style="text-align: justify">Adalah benar Alumni Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu yang
        mengikuti perkuliahan secara
        regular dari Tahun Akademik {{ $surat->data['tahunAkademikAwal'] ?? '20xx' }} s.d Tahun Akademik
        {{ $surat->data['tahunAkademikAkhir'] ?? '20xx' }} .</p>
    <br>
    <p style="text-align: justify">Demikianlah surat keterangan ini dibuat dengan sesungguhnya, apabila terjadi
        kekeliruan akan diperbaiki
        sebagaimana mestinya.</p>
    <br><br>
    <div>
        <div class="tandatangan">
            <div>
                <p>Bengkulu,
                    {{ isset($surat->data['tanggal_selesai']) ? $surat->data['tanggal_selesai'] : '' }}
                </p>
                <p>Wakil Dekan Bidang Akademik</p>
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

                @if ($surat->status == 'finished')
                    <img class="ttd" src="data:image/svg;base64, {!! base64_encode(
                        QrCode::format('svg')->size(80)->generate($url),
                    ) !!}"
                        style="position: absolute; bottom:70px">
                @endif

            </div>
            <div>
                <p>Dr. Abdul Rahman, M.Si</p>
                <p>NIP 198108202006041006</p>
            </div>
        </div>
        {{--
        @if ($surat->status == 'finished' && !request()->hasValidSignature())
        <img src="data:image/svg;base64, {!! base64_encode(
            QrCode::format('svg')->size(90)->generate($url),
        ) !!}" style="position: absolute; bottom:70px">
    @endif --}}
    </div>


</body>

</html>
