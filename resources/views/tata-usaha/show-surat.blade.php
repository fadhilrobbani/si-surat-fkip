@php
    $authUser = auth()->user();
    $step = [];
    $namaPengaju = $surat->data['nama'] ?? ($surat->pengaju->name ?? 'User');
    $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($namaPengaju) . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Tata Usaha | Detail Surat
    </x-slot:title>

    @if (request()->routeIs('show-surat-masuk-tata-usaha'))
        {{ Breadcrumbs::render('detail-surat-masuk', $surat) }}
    @else
        {{ Breadcrumbs::render('tata-usaha-show-pengajuan-surat', $surat) }}
    @endif

    <h1 class="mx-auto text-center font-bold text-xl my-4">{{ $surat->jenisSurat->name }}</h1>

    <div class="flex flex-col gap-4 md:flex-row justify-evenly items-start">

        <div class="w-full overflow-x-auto shadow-lg sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-400">
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Status:&nbsp;</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded text-xs font-semibold
                                {{ $surat->status == 'selesai' ? 'bg-green-100 text-green-800' : ($surat->status == 'ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($surat->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        @php
                            $recentStatus = 'Posisi Surat';
                            if ($surat->status == 'diproses') {
                                $recentStatus = 'Menunggu';
                            } elseif ($surat->status == 'ditolak') {
                                $recentStatus = 'Ditolak oleh';
                            } elseif ($surat->status == 'selesai') {
                                $recentStatus = 'Penerima';
                            }
                        @endphp
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">{{ $recentStatus }}:&nbsp;
                        </td>
                        <td class="px-6 py-4">{{ $surat->current_user->name ?? '-' }}</td>
                    </tr>
                    @php
                        $riwayatPenolakan = App\Models\Approval::where('surat_id', '=', $surat->id)
                            ->where('isApproved', '=', 0)
                            ->first();
                    @endphp

                    @if ($surat->status == 'ditolak' && $riwayatPenolakan)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Catatan Penolakan:&nbsp;
                            </td>
                            <td class="px-6 py-4 text-rose-600 font-medium">{{ $riwayatPenolakan->note }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Penolakan:&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ formatTimestampToIndonesian($riwayatPenolakan->created_at) }}
                            </td>
                        </tr>
                    @endif
                    @if ($surat->status == 'selesai' && isset($surat->data['tanggal_selesai']))
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Disetujui:&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ $surat->data['tanggal_selesai'] }}
                            </td>
                        </tr>
                    @endif

                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Diajukan:&nbsp;</td>
                        <td class="px-6 py-4">{{ formatTimestampToIndonesian($surat->created_at) }}</td>
                    </tr>
                    @if ($surat->expired_at && $surat->status == 'diproses')
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Masa Aktif Tersisa:&nbsp;</td>
                            <td class="px-6 py-4">{{ formatTimestampToDiffDays($surat->expired_at) }} hari</td>
                        </tr>
                    @endif

                    @foreach ($surat->data as $key => $value)
                        @if ($key == 'tanggal_selesai')
                            @continue
                        @endif
                        @if ($key == 'ttdWD1' || $key == 'ttdWD')
                            @continue
                        @endif
                        @if ($key == 'private')
                            @continue
                        @endif
                        @if ($key == 'dosen')
                            @foreach ($value as $id => $data)
                                @foreach ($data as $dosenKey => $dosenVal)
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">
                                            {{ convertToTitleCase($dosenKey) }}:&nbsp;
                                        </td>
                                        <td class="px-6 py-4">{{ $dosenVal }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            @continue
                        @endif
                        <x-surat-data-row :key="$key" :value="$value" />
                    @endforeach

                    @if (isset($surat->files))
                        @foreach ($surat->files as $key => $value)
                            @if ($key == 'private')
                                @continue
                            @endif
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Lampiran
                                    {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:</td>
                                <td class="px-6 py-4">
                                    @php
                                        $storagePath = 'lampiran/' . basename($value);
                                        $filename = pathInfo(basename($value), PATHINFO_FILENAME);

                                        if (\App\Services\StorageHelper::exists($storagePath)) {
                                            $mimeType = str_replace('/', '-', \App\Services\StorageHelper::mimeType($storagePath));
                                        } else {
                                            $mimeType = '/file-tidak-ditemukan';
                                        }
                                        $extension = explode('.', basename($value))[1] ?? 'pdf';
                                        $url = URL::signedRoute('show-file', [
                                            'user' => $authUser->id,
                                            'filename' => $filename,
                                            'mimeType' => $mimeType,
                                            'extension' => $extension,
                                        ]);
                                    @endphp

                                    <a class="text-blue-700 underline font-medium" href="{{ $url }}">Lihat Lampiran</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>
        </div>

        <div>
            @if ($surat->jenisSurat->user_type == 'tata-usaha' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-tata-usaha')
                <x-stepper-tata-usaha-pengajuan-atk :surat='$surat' />
            @else
                <x-stepper :surat='$surat' />
            @endif
        </div>

    </div>

    {{-- ACTION BAR UNTUK VERIFIKASI SURAT MASUK OLEH TATA USAHA --}}
    @if ($surat->current_user_id == auth()->user()->id && $surat->status == 'diproses')
        <div class="mt-8 p-6 bg-slate-100 dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="flex items-center gap-1.5 mb-4">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tindakan Verifikasi</h2>
                <x-info-tooltip id="tooltip-preview-tu" />
            </div>
            <form action="{{ route('setujui-surat-tata-usaha', $surat->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="catatan" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Catatan Persetujuan (Opsional)
                    </label>
                    <textarea id="catatan" name="catatan" rows="2"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                        placeholder="Tambahkan catatan jika diperlukan (misal: kunci ruangan dapat diambil di ruang TU)..."></textarea>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
                    <a href="{{ route('confirm-tolak-surat-tata-usaha', $surat->id) }}"
                        style="background-color: #e11d48; color: #ffffff;"
                        class="w-full sm:w-auto text-center px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg text-sm transition order-last sm:order-first">
                        Tolak Surat
                    </a>

                    <div class="flex flex-col sm:flex-row items-stretch gap-2 flex-1 sm:justify-end">
                        <a href="{{ route('preview-surat-tata-usaha', $surat->id) }}" target="_blank"
                            class="flex-1 text-center px-4 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg text-sm transition">
                            Preview Dokumen
                        </a>
                        <button type="submit"
                            style="background-color: #16a34a; color: #ffffff;"
                            class="flex-1 text-center px-6 py-2.5 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg text-sm shadow cursor-pointer transition">
                            Setujui Surat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @elseif ($surat->status == 'selesai' && $surat->jenisSurat->slug != 'surat-pengajuan-atk-tata-usaha')
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mt-8">
            <a href="{{ route('print-surat-tata-usaha', $surat->id) }}" target="_blank"
                class="w-full sm:w-auto text-center text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 transition">
                Cetak Surat
            </a>
        </div>
    @endif

</x-layout>