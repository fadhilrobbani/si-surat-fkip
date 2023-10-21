<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="/assets/images/logo.png" type="image/x-icon">
    <title>E-SURAT FMIPA</title>
    <style type="text/css">
        table {
            border-style: double;
            border-width: 3px;
            border-color: white;
        }

        table tr .text2 {
            text-align: right;
            font-size: 12px;
        }

        table tr .text {
            text-align: center;
            font-size: 12px;
        }

        table tr td {
            font-size: 12px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            size: portrait;
            margin: 10px auto;
            margin-top: 10px auto;
        }
    </style>
</head>

<body>
    <center>
        <table>
            <tr>

                <td><img src="/assets/images/logo.png" width="90" height="90"></td>
                <td>
                    <center>
                        <font size="4">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</font><br>
                        <font size="4">RISET, DAN TEKNOLOGI</font><br>
                        <font size="4"><b>UNIVERSITAS BENGKULU</b></font><br>
                        <font size="4"><b>FAKULTAS MATEMATIKA DAN ILMU PENGTAHUAN ALAM</b></font><br>
                        <font size="2">Jalan W.R Supratman, Kandang Limun (Dekanat MIPA), Bengkulu 38371A</font>
                        <br>
                        <font size="2">Telepon: (0736) 20919, 21170 ext. 208 Faksimile: (0736) 20919</font><br>
                        <font size="2">Laman: <i>http://www.fmipa.unib.ac.id </i> <i>e-mail</i>:
                            dekanat_fmipa@unib.ac.id </font>
                    </center>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>

            <table width="600">
                <tr>
                    <td>
                        <center>
                            <font size="4"><b><u>SURAT KETERANGAN LULUS </u></b></font> <br>
                            {{-- 2400/UN30.12/TU/2022 --}}
                            @if ($data->no_surat != null)
                                <font size="3"><b>Nomor :
                                        {{ $data->no_surat }}/UN30.12/{{ $data->tb_judul_surat->kode_no_surat }}/{{ date('Y') }}</b>
                                </font>
                            @else
                                <font size="3"><b>Nomor :
                                        /UN30.12/{{ $data->tb_judul_surat->kode_no_surat }}/{{ date('Y') }}</b>
                                </font>
                            @endif
                        </center>
                    </td>
                </tr>
            </table>
        </table>

        <table width="600">
            <tr>
                <td>
                    <font size="2">Yang bertanda tangan di bawah ini :</font>
                </td>
            </tr>
        </table>

        <table width="600">
            <tr class="text2">
                <td width="100px">Nama</td>
                @if ($data->status_persetujuan == 'Y')
                    <td>: {{ $data->user->nama }}</td>
                @else
                    <td>:</td>
                @endif
            </tr>
            <tr>
                <td>NIP</td>
                @if ($data->status_persetujuan == 'Y')
                    <td>: {{ $data->user->tb_persetujuan->nip }}</td>
                @else
                    <td>:</td>
                @endif
            </tr>
            <tr>
                <td>Golongan</td>
                @if ($data->status_persetujuan == 'Y')
                    <td>: {{ $data->user->tb_persetujuan->golongan }}</td>
                @else
                    <td>:</td>
                @endif
            </tr>
            <tr>
                <td>Jabatan</td>
                @if ($data->status_persetujuan == 'Y')
                    <td>: {{ $data->user->tb_persetujuan->jabatan }}</td>
                @else
                    <td>:</td>
                @endif
            </tr>
        </table>

        <table width="600">
            <tr>
                <td>
                    <font size="2">Dengan ini menerangkan bahwa mahasiswa berikut ini :</font>
                </td>
            </tr>
        </table>

        <table width="600">
            <tr class="text2">
                <td width="100px">Nama</td>
                <td>: {{ $data->tb_data_mahasiswa->nama }}</td>
            </tr>
            <tr class="text2">
                <td>NPM</td>
                <td>: {{ $data->tb_data_mahasiswa->npm }}</td>
            </tr>
            <tr class="text2">
                <td>Jurusan/Prodi</td>
                <td>: {{ $data->tb_data_mahasiswa->tb_prodi->nama_prodi }}</td>
            </tr>
            <tr class="text2">
                <td>Fakultas</td>
                <td>: Matematika dan Ilmu Pengtahuan Alam</td>
            </tr>
            <tr class="text2">
                <td>Perguruan Tinggi</td>
                <td>: Universitas Bengkulu</td>
            </tr>
            <tr class="text2">
                <td>IPK</td>
                <td>: {{ $data->ipk }}</td>
            </tr>
        </table>

        @php
            // $hariIni = new DateTime();
            $bulan = date('m', strtotime($data->tgl_lulus));
            $hari = date('d', strtotime($data->tgl_lulus));
            $tahun = date('Y', strtotime($data->tgl_lulus));

            switch ($bulan) {
                case '01':
                    $bulan1 = 'Januari';
                    break;
                case '02':
                    $bulan1 = 'Februari';
                    break;
                case '03':
                    $bulan1 = 'Maret';
                    break;
                case '04':
                    $bulan1 = 'April';
                    break;
                case '05':
                    $bulan1 = 'Mei';
                    break;
                case '06':
                    $bulan1 = 'Juni';
                    break;
                case '07':
                    $bulan1 = 'Juli';
                    break;
                case '08':
                    $bulan1 = 'Agustus';
                    break;
                case '09':
                    $bulan1 = 'September';
                    break;
                case '10':
                    $bulan1 = 'Oktober';
                    break;
                case '11':
                    $bulan1 = 'November';
                    break;
                case '12':
                    $bulan1 = 'Desember';
                    break;
            }
        @endphp


        <table width="600">
            <tr>
                <td>
                    <font size="2">
                        <p>Dinyatakan telah <strong>LULUS</strong>
                            sidang {{ $data->tingkat_strata }} Prodi
                            {{ ucwords($data->tb_data_mahasiswa->tb_prodi->nama_prodi) }} pada tanggal
                            {{ $hari . ' ' . $bulan1 . ' ' . $tahun }} dengan nilai = {{ $data->nilai }}.</p>
                    </font>
                </td>
            </tr>
            <tr>
                <td>
                    <font size="2">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan
                        sebagaimana mestinya.
                </td>
            </tr>
        </table>

        <br>

        @if ($data->status_persetujuan == 'Y')
            @php
                // $hariIni = new DateTime();

                $a = date('m');

                switch ($a) {
                    case '01':
                        $bulan = 'Januari';
                        break;
                    case '02':
                        $bulan = 'Februari';
                        break;
                    case '03':
                        $bulan = 'Maret';
                        break;
                    case '04':
                        $bulan = 'April';
                        break;
                    case '05':
                        $bulan = 'Mei';
                        break;
                    case '06':
                        $bulan = 'Juni';
                        break;
                    case '07':
                        $bulan = 'Juli';
                        break;
                    case '08':
                        $bulan = 'Agustus';
                        break;
                    case '09':
                        $bulan = 'September';
                        break;
                    case '10':
                        $bulan = 'Oktober';
                        break;
                    case '11':
                        $bulan = 'November';
                        break;
                    case '12':
                        $bulan = 'Desember';
                        break;
                }
            @endphp
            <table width="630">
                <tr>
                    <td width="410"><br><br><br><br></td>
                    <div>
                        <td class="text" style="text-align: left">Bengkulu,
                            {{ date('d ') . $bulan . ' ' . date('Y') }}<br>a.n.
                            Dekan<br>{{ $data->user->tb_persetujuan->jabatan }}
                    </div>

                    <div class="visible-print text-center" style="margin-top: 4px; margin-bottom: 4px">
                        {!! $data_qr !!}
                    </div>

                    <div style="margin-bottom: 2px">
                        {{ $data->user->nama }}
                    </div>

                    NIP. {{ $data->user->tb_persetujuan->nip }}
                    </td>
                </tr>
            </table>
        @endif
    </center>
</body>

</html>
