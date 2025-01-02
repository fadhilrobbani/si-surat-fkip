<table class="kop">
    <tr>

        <td>
            <img class="logo"
                src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/logounib.png'))) }}"
                alt="logo">
        </td>
        <td class="kop-text">
            <center>
                <font size="4">
                    {{ $surat->created_at->isAfter('2025-01-02') ? 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS,' : 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,' }}
                </font><br>

                <font size="4">
                    {{ $surat->created_at->isAfter('2025-01-02') ? 'DAN TEKNOLOGI' : 'RISET, DAN TEKNOLOGI' }}
                </font><br>
                <font size="4"><b>UNIVERSITAS BENGKULU</b></font><br>
                <font size="4"><b>FAKULTAS KEGURUAN DAN ILMU PENDIDIKAN</b></font><br>
                <font size="2">Jalan WR. Supratman Kandang Limun Bengkulu 38371A</font>
                <br>
                <font size="2">Telepon: (0736) 21170, Psw.203-232, 21186 Faksimile: (0736) 21186</font><br>
                <font size="2">Laman: <i>https://fkip.unib.ac.id </i> <i>e-mail</i>:
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
