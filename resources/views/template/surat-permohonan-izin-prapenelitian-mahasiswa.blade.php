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
                {{ $surat->data['noSurat'] ?? 'NoSurat' }}/UN30.7/PL/{{ isset($surat->data['tanggal_selesai']) ? \Illuminate\Support\Str::of($surat->data['tanggal_selesai'])->afterLast(' ') : 'Tahun' }}
            </td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:
                1 (satu) berkas proposal
            </td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:
                Permohonan Izin Pra-Penelitian
            </td>
        </tr>

    </table>

    <br>
    <p>Kepada Yth:</p>
    <p>{{ isset($surat->data['tujuan2']) || isset($surat->data['tujuan3']) ? '1.' : '' }}
        {{ $surat->data['tujuan1'] }}</p>
    @if (isset($surat->data['tujuan2']))
        <p>2. {{ $surat->data['tujuan2'] }}</p>
    @endif
    @if (isset($surat->data['tujuan3']))
        <p>3. {{ $surat->data['tujuan3'] }}</p>
    @endif
    <br>
    <p style="text-align: justify">Sehubungan dengan kegiatan penelitian dan penulisan skripsi/tesis/disertasi mahasiswa
        berikut, Kami
        mohon bantuan Bapak/Ibu untuk dapat memberikan izin melakukan pra-penelitian/pra-pengambilan data kepada:</p>
    <br>
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
            <td>: {{ $surat->data['programStudi'] }}</td>
        </tr>
        <tr>
            <td valign='top'>Judul Skripsi</td>
            <td>: {{ $surat->data['judulSkripsi'] }}</td>
        </tr>
        <tr>
            <td valign='top' style="width: 160px">Tempat Pra-Penelitian</td>
            <td>: {{ $surat->data['tempatPraPenelitian'] }}</td>
        </tr>
        <tr>
            <td>Waktu Pra-Penelitian</td>
            <td>: {{ $surat->data['waktuMulaiPraPenelitian'] . ' - ' . $surat->data['waktuSelesaiPraPenelitian'] }}
            </td>
        </tr>
    </table>
    {{-- <p>Nama&emsp; {{ $surat->pengaju->name }}</p>
    <p>NPM&emsp;: {{ $surat->pengaju->username }}</p>
    <p>Tempat/Tanggal Lahir&emsp;: {{ Str::title($surat->data['birthplace']) . ', ' . $surat->data['birthdate'] }}</p>
    <p>Program Studi&emsp;: {{ $surat->data['programStudi'] }}</p>
    <p>Nomor Seri Ijazah&emsp;: {{ $surat->data['noIjazah'] }}</p> --}}
    <br>

    <p style="text-align: justify">Demikian surat keterangan ini dibuat dengan sebenarnya, untuk dapat dipergunakan
        sebagaimana mestinya.</p>
    <br><br>
    <div>
        <div class="tandatangan">
            <div>
                <p>a.n. Dekan,
                </p>
                <p>Wakil Dekan Bidang Akademik </p>
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
                <p> {{ isset($surat->data['private']['namaWD1']) ? $surat->data['private']['namaWD1'] : '(Nama WD1)' }}
                </p>
                <p>NIP {{ isset($surat->data['private']['nipWD1']) ? $surat->data['private']['nipWD1'] : '(NIP WD1)' }}
                </p>
            </div>
        </div>
        {{-- @if ($surat->status == 'selesai' && !request()->hasValidSignature())
            <img src="data:image/svg;base64, {!! base64_encode(
                QrCode::format('svg')->size(90)->generate($url),
            ) !!}" style="position: absolute; bottom:70px">
        @endif --}}
    </div>


</body>

</html>
