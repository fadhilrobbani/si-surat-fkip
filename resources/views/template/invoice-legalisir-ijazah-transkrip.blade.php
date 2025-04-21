<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Legalisir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .invoice-container {
            width: 100%;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
        }

        .header h2 {
            margin: 0;
        }

        .details,
        .summary {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .details td,
        .summary td,
        .summary th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .summary th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="header">
            <h2>Invoice Legalisir</h2>
            <p>Fakultas Keguruan dan Ilmu Pendidikan - Universitas Bengkulu</p>
        </div>

        <table class="details">
            <tr>
                <td><strong>Order ID:</strong> {{ $surat->id }}</td>
                <td><strong>Nama:</strong> {{ $surat->data['nama'] }}</td>
            </tr>
            <tr>
                <td><strong>NPM:</strong> {{ $surat->data['npm'] }}</td>
                <td><strong>Program Studi:</strong> {{ $surat->data['programStudi'] }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong> {{ $surat->data['email'] }}</td>
                @if (isset($surat->data['kontak']))
                    <td><strong>No. HP:</strong> {{ $surat->data['kontak'] }}</td>
                @endif
            </tr>
            <tr>
                <td colspan="2"><strong>Metode Pengiriman:</strong> {{ $surat->data['metodePengiriman'] }}</td>
            </tr>
            @if ($surat->data['pengiriman'] == 'dikirim')
                <tr>
                    <td colspan="2">
                        <strong>Alamat Pengiriman:</strong><br>
                        {{ $surat->data['alamat'] }}<br>
                        {{ $surat->data['kelurahan'] }}, {{ $surat->data['kecamatan'] }}<br>
                        {{ $surat->data['kota'] }}, {{ $surat->data['provinsi'] }} - {{ $surat->data['kodePos'] }}
                    </td>
                </tr>
                @if (!empty($surat->data['noResi']))
                    <tr>
                        <td colspan="2"><strong>Nomor Resi:</strong> {{ $surat->data['noResi'] }}</td>
                    </tr>
                @endif
            @endif
            @if (!empty($surat->data['tanggalSelesai']))
                <tr>
                    <td colspan="2"><strong>Tanggal Selesai:</strong> {{ $surat->data['tanggalSelesai'] }}</td>
                </tr>
            @endif
            @if (!empty($surat->data['tanggalPengiriman']))
                <tr>
                    <td colspan="2"><strong>Tanggal Pengiriman:</strong> {{ $surat->data['tanggalPengiriman'] }}
                    </td>
                </tr>
            @endif
        </table>

        <table class="summary">
            <tr>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
            <tr>
                <td>{{ 'Legalisir Ijazah' }} </td>
                <td>{{ $surat->data['private']['jumlahLembarIjazah'] }} </td>
                <td>-</td>
            </tr>
            <tr>
                <td>{{ 'Legalisir Transkrip' }} </td>
                <td>{{ $surat->data['private']['jumlahLembarTranskrip'] }} </td>
                <td>-</td>
            </tr>

            @if ($surat->data['pengiriman'] == 'dikirim')
                <tr>
                    <td>Ongkos Kirim</td>
                    <td>{{ isset($surat->data['ongkir']) ? $surat->data['ongkir'] : 'Bisa cek di invoice / data resmi dari J&T' }}
                    </td>
                    {{-- <td>Rp {{ number_format($surat->data['ongkir'], 0, ',', '.') }}</td> --}}
                    <td>Pembayaran metode COD </td>
                </tr>
            @endif

        </table>

        <div class="footer">
            <p><strong>Catatan:</strong> {{ $surat->data['note'] ?? '-' }}</p>
            <p>Terima kasih telah menggunakan layanan E-Surat FKIP UNIB.</p>
        </div>
    </div>
</body>

</html>
