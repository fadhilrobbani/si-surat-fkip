@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->pengaju->name . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Detail Pengajuan Legalisir
    </x-slot:title>
    <x-slot:script>
        <script>
            function openConfirmModal() {
                document.getElementById('confirmModal').classList.remove('hidden');
            }

            function closeConfirmModal() {
                document.getElementById('confirmModal').classList.add('hidden');
            }


            document.getElementById('confirm-button').addEventListener('click', function() {
                document.getElementById('confirmation-modal').classList.remove('hidden');
            });

            document.getElementById('close-modal').addEventListener('click', function() {
                document.getElementById('confirmation-modal').classList.add('hidden');
            });

            document.getElementById('cancel-button').addEventListener('click', function() {
                document.getElementById('confirmation-modal').classList.add('hidden');
            });

            document.getElementById('submit-button').addEventListener('click', function() {
                document.getElementById('approval-form').submit();
            });
        </script>
    </x-slot:script>

    {{ Breadcrumbs::render('detail-surat-masuk', $surat) }}
    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Data Pengajuan Legalisir:</p>

    <div class="bg-white rounded-lg shadow-md  overflow-x-auto">

        <div class="stepper-layout">
            <table class="w-full shadow-lg text-sm text-left rtl:text-right text-gray-700 dark:text-gray-400 mb-8">
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
                                } elseif ($status == 'selesai') {
                                    $bgColor = 'bg-green-200';
                                    $textColor = 'text-black';
                                }
                            @endphp

                            <span
                                class="{{ $bgColor }} {{ $textColor }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                {{ $status }}
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Diajukan:&nbsp;</td>
                        <td class="px-6 py-4">{{ formatTimestampToIndonesian($surat->created_at) }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Masa Aktif Tersisa:&nbsp;
                        </td>
                        <td class="px-6 py-4">{{ formatTimestampToDiffDays($surat->expired_at) }} hari</td>
                    </tr>
                    @if (isset($surat->data['tanggalSelesai']))
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Tanggal Selesai:</td>
                            <td class="px-6 py-4">{{ $surat->data['tanggalSelesai'] }}</td>
                        </tr>
                    @endif
                    @if (isset($surat->data['noResi']))
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Nomor Resi:</td>
                            <td class="px-6 py-4">{{ $surat->data['noResi'] }}</td>
                        </tr>
                    @endif
                    @if (isset($surat->data['catatanUntukAkademik']))
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Pesan dari Mahasiswa:</td>
                            <td class="px-6 py-4">{{ $surat->data['catatanUntukAkademik'] }}</td>
                        </tr>
                    @endif
                    @if (isset($surat->data['note']))
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Pesan dari Akademik:</td>
                            <td class="px-6 py-4">{{ $surat->data['note'] }}</td>
                        </tr>
                    @endif
                    @if (isset($surat->data['alasanPenolakan']))
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Alasan Penolakan:</td>
                            <td class="px-6 py-4">{{ $surat->data['alasanPenolakan'] }}</td>
                        </tr>
                    @endif
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Metode Pengiriman:&nbsp;</td>
                        <td class="px-6 py-4">{{ $surat->data['metodePengiriman'] }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Jumlah Lembar:</td>
                        <td class="px-6 py-4">{{ $surat->data['jumlahLembar'] }} lembar</td>
                    </tr>

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
                    @if (isset($surat->data['kontak']))
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">No. HP:</td>
                            <td class="px-6 py-4">{{ $surat->data['kontak'] }}</td>
                        </tr>
                    @endif

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

                </tbody>
            </table>
            <x-stepper-legalisir :surat="$surat" />

        </div>
        @if (auth()->user()->role_id == 2 && $surat->status == 'dikirim')
            <button type="button" onclick="openConfirmModal()"
                class="my-6 mx-auto bg-blue-600 flex flex-row  items-center gap-4 text-white px-6 py-2 rounded-lg shadow-md hover:bg-blue-800 transition duration-300 flex justify-center">
                <svg class="w-8 h-8 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p>Konfirmasi Pengiriman Selesai</p>

            </button>

            <div id="confirmModal"
                class="fixed inset-0 p-4 flex items-center justify-center hidden bg-black bg-opacity-50 z-50">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-sm relative">
                    <button onclick="closeConfirmModal()"
                        class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>

                    </button>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi</h2>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                        Apakah Anda yakin bahwa paket anda telah tiba dan ingin mengubah status dikirim menjadi selesai?
                    </p>
                    <form action="{{ route('konfirmasi-selesai-legalisir-ijazah', $surat->id) }}" method="POST"
                        class="mt-4 flex justify-end">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="bg-blue-500 text-white mx-auto px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-300">
                            Ya, Konfirmasi
                        </button>
                    </form>
                </div>
            </div>
        @endif



    </div>

    @if ($surat->status == 'diproses' || $surat->status == 'dikirim' || $surat->status == 'selesai')
        <a href="{{ route('print-surat-akademik', $surat->id) }}"
            class="p-2 my-4 flex justify-center items-center flex-row gap-2 text-white bg-blue-700 rounded-lg"
            target="_blank">
            <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                    d="M16.444 18H19a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h2.556M17 11V5a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v6h10ZM7 15h10v4a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-4Z" />
            </svg>
            <p>Cetak Invoice</p>

        </a>
    @endif
    <form id="approval-form" action="{{ route('setujui-surat-akademik', $surat->id) }}" method="POST"
        class="bg-slate-100 pt-2 rounded-lg w-full">
        @csrf
        @method('put')
        <div class="flex flex-col gap-4 mt-10 items-center justify-center">
            <div class="w-full max-w-[400px]">
                <label for="no-resi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nomor Resi J&T <span class="text-red-600">*</span>
                </label>

                <input type="text" id="no-resi" name="no-resi" value="{{ old('no-resi') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan nomor resi pengiriman" required>
            </div>

            <div class="w-full max-w-[400px]">
                <label for="note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan
                    (opsional)</label>
                <textarea id="note" name="note"
                    class="bg-gray-50 min-h-[150px] border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan catatan yang ingin diletakkan pada invoice / ke mahasiswa">{{ old('note') }}</textarea>
            </div>
        </div>

        <div class="flex mt-8 justify-center gap-10 flex-col sm:flex-row ">
            <button id="confirm-button"
                class="hover:bg-green-600 cursor-pointer rounded-lg text-center bg-green-500 p-2 text-white m-2"
                type="button">
                Setuju
            </button>

            <a href="{{ route('confirm-tolak-surat-akademik', $surat->id) }}">
                <div class="hover:bg-pink-800 cursor-pointer rounded-lg text-center bg-pink-600 p-2 text-white m-2">
                    Tolak
                </div>
            </a>
        </div>
    </form>

    <!-- Modal Konfirmasi -->
    <div id="confirmation-modal" class="fixed inset-0 flex items-center justify-center  hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold">Konfirmasi</h2>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                    <x-close-button />
                </button>
            </div>
            <p class="mt-4">Apakah Anda yakin ingin menyetujui dan mengubah status ke dikirim untuk pengajuan
                legalisir ini?</p>
            <div class="mt-6 flex justify-end gap-4">
                <button id="submit-button"
                    class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg">Setuju</button>
                <button id="cancel-button"
                    class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg">Batal</button>
            </div>
        </div>
    </div>

</x-layout>
