@php
    $authUser = auth()->user();
    $surat = $approval->surat;
    $namaPengaju = $surat->data['nama'] ?? ($surat->pengaju->name ?? 'User');
    $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($namaPengaju) . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Tata Usaha | Detail Riwayat Persetujuan
    </x-slot:title>
    {{ Breadcrumbs::render('detail-persetujuan', $approval) }}
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
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Hasil Konfirmasi Anda:&nbsp;</td>
                        <td class="px-6 py-4 font-semibold {{ $approval->isApproved == 1 ? 'text-green-600' : 'text-rose-600' }}">
                            {{ $approval->isApproved == 1 ? 'Disetujui' : 'Ditolak' }}
                        </td>
                    </tr>
                    @if ($approval->note)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Catatan Anda:&nbsp;</td>
                            <td class="px-6 py-4">{{ $approval->note }}</td>
                        </tr>
                    @endif
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Diajukan:&nbsp;</td>
                        <td class="px-6 py-4">{{ formatTimestampToIndonesian($surat->created_at) }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Ditinjau:&nbsp;</td>
                        <td class="px-6 py-4">{{ formatTimestampToIndonesian($approval->created_at) }}</td>
                    </tr>
                    @if ($surat->expired_at && $surat->status == 'diproses')
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Masa Aktif Tersisa:&nbsp;</td>
                            <td class="px-6 py-4">{{ formatTimestampToDiffDays($surat->expired_at) }} hari</td>
                        </tr>
                    @endif

                    @foreach ($surat->data as $key => $value)
                        @if ($key == 'tanggal_selesai')
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">
                                    {{ Str::title(str_replace('_', ' Surat ', $key)) }}:&nbsp;
                                </td>
                                <td class="px-6 py-4">{{ $value }}</td>
                            </tr>
                            @continue
                        @endif
                        @if (in_array($key, ['ttdWD1', 'ttdWD', 'note', 'private', 'catatanTU', 'alasanPenolakan']))
                            @continue
                        @endif
                        @if ($key == 'dosen')
                            @foreach ($value as $id => $data)
                                @foreach ($data as $dosenKey => $dosenValue)
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">
                                            {{ convertToTitleCase($dosenKey) }}:&nbsp;
                                        </td>
                                        <td class="px-6 py-4">{{ $dosenValue }}</td>
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

    @if ($surat->jenisSurat->slug != 'surat-pengajuan-atk-tata-usaha')
        <div class="flex items-center gap-2 mt-8">
            @if ($surat->status == 'selesai')
                <a href="{{ route('print-surat-tata-usaha', $surat->id) }}" target="_blank"
                    class="w-full sm:w-auto text-center text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 transition">
                    Cetak Surat
                </a>
            @else
                <a href="{{ route('preview-surat-tata-usaha', $surat->id) }}" target="_blank"
                    class="w-full sm:w-auto text-center text-white bg-slate-600 hover:bg-slate-700 font-medium rounded-lg text-sm px-5 py-2.5 transition">
                    Preview Surat
                </a>
                <x-info-tooltip id="tooltip-approval-tu" />
            @endif
        </div>
    @endif
</x-layout>
