@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->pengaju->name . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Menunggu Pembayaran
    </x-slot:title>
    <x-slot:script>
        <script>
            document.getElementById('batal-button').addEventListener('click', function() {
                document.getElementById('bukti-bayar').removeAttribute('required');
            });


            document.getElementById('confirm-batal-button').addEventListener('click', function() {
                const form = document.getElementById('konfirmasi-pembayaran');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'action';
                hiddenInput.value = 'batal';
                form.appendChild(hiddenInput);
                form.submit();
            });
        </script>
    </x-slot:script>
    {{ Breadcrumbs::render('show-pengajuan-surat', $surat) }}
    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <div class="bg-white rounded-lg shadow-md  overflow-x-auto">

        <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-400 mb-8">
            <tbody>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Status:</td>
                    <td class="px-6 py-4">
                        @php
                            // Periksa apakah expired_at sudah lewat
                            $isExpired = $surat->expired_at && \Carbon\Carbon::now()->greaterThan($surat->expired_at);
                            // Tentukan status berdasarkan kondisi
                            $status = $isExpired ? 'Tidak Dibayar' : 'Menunggu Pembayaran';
                            $statusClass = $isExpired
                                ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                        @endphp

                        <span class="{{ $statusClass }} text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Diajukan:&nbsp;</td>
                    <td class="px-6 py-4">{{ formatTimestampToIndonesian($surat->created_at) }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Metode Pengiriman:&nbsp;</td>
                    <td class="px-6 py-4">{{ $surat->data['metodePengiriman'] }}</td>
                </tr>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tenggat Waktu Bayar Tersisa:&nbsp;
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

        <div class="mt-8 p-4 bg-gray-100 rounded-lg">

            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row justify-between">
                    <span class="font-semibold text-gray-700">Ongkos Kirim:</span>
                    <span>Rp {{ number_format($surat->data['ongkir'], 0, ',', '.') }}</span>
                </div>
                <div class="flex flex-col sm:flex-row  justify-between">
                    <span class="font-semibold text-gray-700">Biaya Jasa:</span>
                    <span>Rp {{ number_format($surat->data['biayaJasa'], 0, ',', '.') }}</span>
                </div>
                <div class="flex flex-col sm:flex-row  justify-between">
                    <span class="font-semibold text-gray-700">Biaya Legalisir:</span>
                    <span>Rp {{ number_format($surat->data['biayaLembar'], 0, ',', '.') }}</span>
                </div>
                <div class="h-2 bg-slate-800"></div>
                <div class="flex flex-col sm:flex-row  justify-between">
                    <span class="font-semibold text-lg text-gray-700">Total biaya yang dibayarkan:</span>
                    <span class="text-lg font-bold">Rp
                        {{ number_format($surat->data['totalHarga'], 0, ',', '.') }}</span>
                </div>
                <div class="h-2 bg-slate-800"></div>
                @if ($surat->expired_at > \Carbon\Carbon::now())
                    <form id="konfirmasi-pembayaran"
                        action="{{ route('konfirmasi-pembayaran-legalisir-ijazah', $surat->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="font-semibold text-gray-700" for="bukti-bayar">Upload Bukti Bayar<span
                                    class="text-red-500">*</span>: </label>
                            <input
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                aria-describedby="file_input_help" id="bukti-bayar" type="file" name="bukti-bayar"
                                accept=".jpg, .jpeg, .png, .pdf" max-size="2048" required>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG,
                                JPEG,
                                atau PDF (MAX 2 MB).</p>
                        </div>

                        <x-modal-konfirmasi-pembayaran-send :daftarPenerima='$daftarPenerima' />
                        <x-modal-batal-pengajuan />
                        <div class="flex flex-col gap-4 mt-4 sm:flex-row justify-center  items-center">
                            <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                                type="button"
                                class=" text-white bg-blue-700 w-full sm:w-fit  hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Konfirmasi Pembayaran
                            </button>

                            <button data-modal-target="batal-modal" data-modal-toggle="batal-modal" type="button"
                                id="batal-button"
                                class="text-white bg-pink-600 w-full sm:w-fit hover:bg-pink-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Batalkan Pengajuan
                            </button>
                        </div>

                    </form>
                @endif
            </div>
        </div>
    </div>
</x-layout>
