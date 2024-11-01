<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Cek Keaslian Surat</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800 font-sans">
    <main class="max-w-lg mx-auto p-6 bg-white rounded-lg shadow-md">
        <img class="w-28 mx-auto mb-5" src="{{ asset('images/logounib.png') }}" alt="logounib" />

        <h1 class="text-2xl font-bold text-center text-blue-800">Halaman Validasi Keaslian Surat</h1>
        <p class="text-center text-gray-700 mt-2 mb-6">Dengan ini menyatakan bahwa surat ini adalah asli atau resmi
            diterbitkan dari FKIP UNIB</p>

        <div class="overflow-x-auto shadow-lg rounded-lg">
            <table class="w-full text-sm text-left text-gray-700 bg-white">
                <tbody>
                    <tr class="border-b">
                        <td class="px-4 py-3 font-semibold bg-gray-50">Nama:</td>
                        <td class="px-4 py-3">{{ $surat->data['nama'] }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3 font-semibold bg-gray-50">Username:</td>
                        <td class="px-4 py-3">
                            {{ isset($surat->data['npm']) ? $surat->data['npm'] : $surat->data['username'] }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3 font-semibold bg-gray-50">Jenis Surat:</td>
                        <td class="px-4 py-3">{{ $surat->jenisSurat->name }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3 font-semibold bg-gray-50">Nomor Surat:</td>
                        <td class="px-4 py-3">{{ $surat->data['noSurat'] }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3 font-semibold bg-gray-50">Yang menandatangani:</td>
                        <td class="px-4 py-3">
                            {{ $surat->data['private']['namaWD1'] ?? ($surat->data['private']['namaWD'] ?? ($surat->data['private']['namaDekan'] ?? '(Nama tidak tersedia)')) }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3 font-semibold bg-gray-50">Tanggal Surat Diterbitkan:</td>
                        <td class="px-4 py-3">{{ $surat->data['tanggal_selesai'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @php
            $url = URL::signedRoute('cetak-surat-qr', ['surat' => $surat->id]);
        @endphp
        <a href="{{ $url }}"
            class="block text-center mt-8 px-6 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Cetak</a>
    </main>
</body>

</html>
