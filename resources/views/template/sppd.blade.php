<div class="sppd-container">
    <table class="sppd-table">
        <tr class="sppd-header-row">
            <td class="sppd-header-cell-left" colspan="2"></td>
            <td class="sppd-header-cell-right">
                <div class="sppd-cell-content">
                    Berangkat dari &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <br>
                    (tempat kedudukan) <br>
                    Ke:<br>
                    Pada tanggal:
                    @if ($surat->status == 'selesai')
                        <img class="ttd-sppd1" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(50)->generate($url)) !!}">
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td class="sppd-number-cell">I</td>
            <td>
                Tiba di:<br>
                Pada tanggal:<br>
                Kepala:
            </td>
            <td>
                Berangkat dari:<br>
                Ke:<br>
                Pada tanggal:<br>
                Kepala:
            </td>
        </tr>
        <tr>
            <td class="sppd-number-cell">II</td>
            <td>
                Tiba di:<br>
                Pada tanggal:<br>
                Kepala:
            </td>
            <td>
                Berangkat dari:<br>
                Ke:<br>
                Pada tanggal:<br>
                Kepala:
            </td>
        </tr>
        <tr>
            <td class="sppd-number-cell">III</td>
            <td>
                Tiba di:<br>
                Pada tanggal:<br>
                Kepala:
            </td>
            <td>
                Berangkat dari:<br>
                Ke:<br>
                Pada tanggal:<br>
                Kepala:
            </td>
        </tr>
        <tr>
            <td class="sppd-number-cell">IV</td>
            <td>
                <div class="sppd-cell-content">
                    Tiba di
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    :<br>
                    (tempat kedudukan)<br>
                    Pada tanggal:
                </div>
                @if ($surat->status == 'selesai')
                    <img class="ttd-sppd2" src="data:image/svg;base64, {!! base64_encode(QrCode::format('svg')->size(50)->generate($url)) !!}">
                @endif
            </td>
            <td>
                Telah diperiksa, dengan keterangan bahwa perjalanan tersebut di atas dilakukan atas perintahnya dan
                semata-mata untuk kepentingan jabatan dalam waktu yang sesingkat-singkatnya.<br>
                (tempat kedudukan)<br>
                Pada tanggal:
            </td>
        </tr>
        <tr>
            <td class="sppd-number-cell" style="height: 60px">V</td>
            <td colspan="2">
                Catatan lain-lain:
            </td>
        </tr>
        <tr>
            <td class="sppd-number-cell" style="height: 40px">VI</td>
            <td colspan="2">
                <div class="sppd-footer">
                    <strong>Perhatian</strong><br>
                    Pejabat yang berwenang menerbitkan SPPD, Pegawai yang melakukan perjalanan dinas, para pejabat yang
                    mengesahkan
                    tanggal berangkat/tiba serta Bendaharawan bertanggungjawab berdasarkan peraturan-peraturan keuangan
                    negara
                    apabila negara menderita rugi akibat kesalahan, kelalaian dan kealpaan, (angka 8 lampiran Surat
                    Edaran Menteri
                    Keuangan tanggal 30 April 1974 No. B.296/MK/14/1974.
                </div>
            </td>
        </tr>
    </table>


</div>
