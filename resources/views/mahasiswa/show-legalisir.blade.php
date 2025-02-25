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
    <div class="bg-white rounded-lg shadow-md  overflow-x-auto">

        <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-400 mb-8">
            <tbody>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Status:</td>
                    <td class="px-6 py-4">
                        @php
                            $status = $surat->status;
                            $bgColor = 'bg-gray-100'; // Default
                            $textColor = 'text-gray-800'; // Default

                            if ($status == 'diproses') {
                                $bgColor = 'bg-yellow-100';
                                $textColor = 'text-yellow-800';
                            } elseif ($status == 'ditolak') {
                                $bgColor = 'bg-red-100'; // Atau 'bg-pink-100' jika ingin pink
                                $textColor = 'text-red-800'; // Atau 'text-pink-800' jika ingin pink
                            } elseif ($status == 'dikirim') {
                                $bgColor = 'bg-blue-100';
                                $textColor = 'text-blue-800';
                            }
                        @endphp

                        <span
                            class="{{ $bgColor }} {{ $textColor }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
                @if (isset($surat->data['noResi']))
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Nomor Resi:</td>
                        <td class="px-6 py-4">{{ $surat->data['noResi'] }}</td>
                    </tr>
                @endif
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Nama:</td>
                    <td class="px-6 py-4">{{ $surat->data['nama'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">NPM:</td>
                    <td class="px-6 py-4">{{ $surat->data['npm'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Program Studi:</td>
                    <td class="px-6 py-4">{{ $surat->data['programStudi'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Email:</td>
                    <td class="px-6 py-4">{{ $surat->data['email'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Jumlah Lembar:</td>
                    <td class="px-6 py-4">{{ $surat->data['jumlahLembar'] }} lembar</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Alamat:</td>
                    <td class="px-6 py-4">{{ $surat->data['alamat'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Kode Pos:</td>
                    <td class="px-6 py-4">{{ $surat->data['kodePos'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Provinsi:</td>
                    <td class="px-6 py-4">{{ $surat->data['provinsi'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Kota:</td>
                    <td class="px-6 py-4">{{ $surat->data['kota'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Kecamatan:</td>
                    <td class="px-6 py-4">{{ $surat->data['kecamatan'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Kelurahan:</td>
                    <td class="px-6 py-4">{{ $surat->data['kelurahan'] }}</td>
                </tr>
                {{-- @if (isset($surat->files))
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">File Ijazah:</td>
                    <td class="px-6 py-4">{{ $surat->files['iijazah'] }}</td>
                </tr>
                @endif --}}
                @if (isset($surat->files))

                    @foreach ($surat->files as $key => $value)
                        @if ($key == 'private')
                            @continue
                        @endif
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            {{-- Str::title(str_replace('_', ' ', $key)) . --}}
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Lampiran
                                {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:</td>
                            <td class="px-6 py-4">
                                {{-- <a class="text-blue-700 underline"
                                href="{{ route('show-file-mahasiswa', ['surat' => $surat->id, 'filename' => basename($value)]) }}">Lihat</a> --}}
                                {{-- <a class="text-blue-700 underline"
                                href="{{ '/storage/lampiran/' . basename($value) }}">Lihat</a> --}}
                                <?php
                                $path = public_path('storage/lampiran/' . basename($value));
                                $filename = pathInfo(basename($value), PATHINFO_FILENAME);
                                if (!empty($path) && file_exists($path)) {
                                    $mimeType = str_replace('/', '-', mime_content_type($path));
                                } else {
                                    // Handle ketika $path kosong atau file tidak ditemukan
                                    $mimeType = '/file-tidak-ditemukan';
                                }
                                
                                $extension = explode('.', basename($value))[1];
                                $url = URL::signedRoute('show-file', [
                                    'user' => $authUser->id,
                                    'filename' => $filename,
                                    'mimeType' => $mimeType,
                                    'extension' => $extension,
                                ]);
                                ?>

                                <a class="text-blue-700 underline" href="{{ $url }}">Lihat</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">URL Ongkos Kirim:</td>
                    <td class="px-6 py-4">
                        <a href="{{ $surat->data['urlOngkir'] }}" target="_blank"
                            class="text-blue-500 underline">{{ $surat->data['urlOngkir'] }}</a>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</x-layout>
