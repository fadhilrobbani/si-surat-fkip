@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->pengaju->name . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Menunggu Pembayaran
    </x-slot:title>
    {{ Breadcrumbs::render('show-pengajuan-surat', $surat) }}
    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <br>
    <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto">

        <table class="w-full mb-8 border-collapse border border-gray-400">
            <tbody>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Nama:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['nama'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">NPM:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['npm'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Program Studi:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['programStudi'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Email:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['email'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Jumlah Lembar:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['jumlahLembar'] }} lembar</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Alamat:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['alamat'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Kode Pos:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['kodePos'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Provinsi:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['provinsi'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Kota:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['kota'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Kecamatan:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['kecamatan'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Kelurahan:</td>
                    <td class="py-3 px-4 border border-gray-400">{{ $surat->data['kelurahan'] }}</td>
                </tr>
                <tr>
                    <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">URL Ongkos Kirim:</td>
                    <td class="py-3 px-4 border border-gray-400">
                        <a href="{{ $surat->data['urlOngkir'] }}" target="_blank"
                            class="text-blue-500 underline">{{ $surat->data['urlOngkir'] }}</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-8 p-6 bg-gray-100 rounded-lg overflow-x-auto">
            <h3 class="text-lg font-semibold mb-6 text-center">Rincian Biaya</h3>
            <table class="w-full border-collapse border border-gray-400">
                <tbody>
                    <tr>
                        <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Ongkos Kirim:</td>
                        <td class="py-3 px-4 border border-gray-400">Rp
                            {{ number_format($surat->data['ongkir'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Biaya Jasa:</td>
                        <td class="py-3 px-4 border border-gray-400">Rp
                            {{ number_format($surat->data['biayaJasa'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium text-gray-700 py-3 px-4 border border-gray-400">Biaya Legalisir:</td>
                        <td class="py-3 px-4 border border-gray-400">Rp
                            {{ number_format($surat->data['biayaLembar'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="font-medium text-lg font-bold text-gray-700 py-3 px-4 border border-gray-400">Total
                            Harga:</td>
                        <td class="text-lg font-bold py-3 px-4 border border-gray-400">Rp
                            {{ number_format($surat->data['totalHarga'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
