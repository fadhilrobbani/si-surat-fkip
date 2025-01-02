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
    <p style="text-align: center"><b><u>SURAT REKOMENDASI</u></b></p>
    <p style="text-align: center">
        <b>Nomor:&nbsp;{{ $surat->data['noSurat'] ?? 'NoSurat' }}/UN30.7/MBKM/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : 'Tahun' }}
        </b>
    </p>
    <br>
    <br>
    <p style="text-align: justify">Yang bertanda tangan di bawah ini:
    </p>
    <table class="data-table">
        <tr>
            <td style="width: 94px">Nama</td>
            <td>:
                {{ isset($surat->data['private']['namaWD1']) ? $surat->data['private']['namaWD1'] : '(Nama WD1)' }}
            </td>
        </tr>
        <tr>
            <td>NIP</td>
            <td>:
                {{ isset($surat->data['private']['nipWD1']) ? $surat->data['private']['nipWD1'] : '(NIP WD1)' }}
            </td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:
                Wakil Dekan Bidang Akademik
            </td>
        </tr>
    </table>

    <br>
    <p style="text-align: justify">Menyatakan dengan sesungguhnya bahwa: </p>
    <table class="data-table">
        <tr>
            <td>Nama</td>
            <td>: {{ Str::title($surat->data['nama']) }}</td>
        </tr>
        <tr>
            <td>NPM</td>
            <td>: {{ $surat->data['npm'] }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:
                {{ Str::title($surat->data['programStudi']) }}
            </td>
        </tr>
        <tr>
            <td>IPK</td>
            <td>:
                {{ $surat->data['ipk'] }}
            </td>
        </tr>
    </table>
    <br>
    <p style="text-align: justify">untuk menjadi peserta program {{ $surat->data['jenisProgram'] }} pada semester
        {{ $surat->data['semesterSaatProgramBerlangsung'] }} tahun ajaran
        {{ $surat->data['tahunAkademikSaatProgramBerlangsung'] }} dan
        yang bersangkutan akan mendapatkan pengakuan/konversi sks dari Program MBKM tersebut sesuai dengan peraturan
        yang berlaku.
    </p>
    <br>
    <p style="text-align: justify">Demikian surat rekomendasi ini kami sampaikan untuk digunakan sebagaimana mestinya.
    </p>
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
                {{-- @if (isset($surat->data['ttdWD1']) && isset($surat->data['stempel']))
                    <img class="ttd" style="margin-left: 40px" width="100px"
                        src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($surat->data['ttdWD1']))) }}"
                        alt="ttd">
                    <img class="stempel" style="margin-left: 40px" width="120px"
                        src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($surat->data['stempel']))) }}"
                        alt="stempel">
                @endif --}}
                @if ($surat->status == 'selesai')
                    <img class="ttd" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(90)->generate($url)) !!}"
                        style="position: absolute; bottom:70px">
                @endif
            </div>
            <div>
                <p>{{ isset($surat->data['private']['namaWD1']) ? $surat->data['private']['namaWD1'] : '(Nama WD1)' }}
                </p>
                <p>NIP
                    {{ isset($surat->data['private']['nipWD1']) ? $surat->data['private']['nipWD1'] : '(NIP WD1)' }}
                </p>
            </div>
        </div>
    </div>


</body>

</html>
