@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $approval->surat->pengaju->name . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        kabag | Detail Riwayat Persetujuan
    </x-slot:title>
    {{ Breadcrumbs::render('detail-persetujuan', $approval) }}
    <h1 class="mx-auto text-center font-bold">{{ $approval->surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-4 md:flex-row justify-evenly items-start">

        <div class=" w-full overflow-x-auto shadow-lg sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-400">
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700">

                        <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Status:&nbsp;</td>
                        <td class="px-6 py-4">
                            {{ $approval->surat->expired_at < Carbon\Carbon::now() && $approval->surat->status == 'diproses' ? 'expired' : $approval->surat->status }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Hasil Konfirmasi Anda:&nbsp;
                        </td>
                        <td class="px-6 py-4">{{ $approval->isApproved == 1 ? 'Disetujui' : 'Ditolak' }}</td>
                    </tr>

                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Diajukan:&nbsp;</td>
                        <td class="px-6 py-4">{{ formatTimestampToIndonesian($approval->surat->created_at) }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Tanggal Anda
                            Menyetujui/Menolak:&nbsp;</td>
                        <td class="px-6 py-4">{{ formatTimestampToIndonesian($approval->created_at) }}</td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Masa Aktif Tersisa:&nbsp;</td>
                        <td class="px-6 py-4">{{ formatTimestampToDiffDays($approval->surat->expired_at) }} hari</td>
                    </tr>
                    @foreach ($approval->surat->data as $key => $value)
                        @if ($key == 'tanggal_selesai')
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">
                                    {{ Str::title(str_replace('_', ' Surat ', $key)) }}:&nbsp;
                                </td>
                                <td class="px-6 py-4">{{ $value }}</td>
                            </tr>

                            @continue
                        @endif
                        @if ($key == 'ttdWD1')
                            @continue
                        @endif
                        @if ($key == 'note')
                            @continue
                        @endif
                        @if ($key == 'private')
                            @continue
                        @endif
                        @if ($key == 'dosen')
                            @foreach ($value as $id => $data)
                                @foreach ($data as $key => $value)
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">
                                            {{ convertToTitleCase($key) }}:&nbsp;
                                        </td>
                                        <td class="px-6 py-4">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            @continue
                        @endif
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">
                                {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:&nbsp;</td>
                            <td class="px-6 py-4">{{ $value }}</td>
                        </tr>
                    @endforeach
                    @if (isset($approval->surat->files))

                        @foreach ($approval->surat->files as $key => $value)
                            @if ($key == 'private')
                                @continue
                            @endif
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Lampiran
                                    {{ Str::title(str_replace('_', ' ', $key)) }}:</td>
                                <td class="px-6 py-4">
                                    {{-- <a class="text-blue-700 underline"
                                        href="{{ route('show-file-kabag', ['surat' => $approval->surat->id, 'filename' => basename($value)]) }}">Lihat</a> --}}
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
        </div>


        @if ($surat->jenisSurat->user_type == 'mahasiswa')
            <x-stepper :surat='$surat' />
        @elseif ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'berita-acara-nilai')
            <x-stepper-staff-berita-acara-nilai :surat='$surat' />
        @elseif ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-pengajuan-atk')
            <x-stepper-staff-pengajuan-atk :surat='$surat' />
        @elseif($surat->jenisSurat->user_type == 'akademik' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-akademik')
            <x-stepper-akademik-pengajuan-atk :surat='$surat' />
        @elseif(
            $surat->jenisSurat->user_type == 'akademik_fakultas' &&
                $surat->jenisSurat->slug == 'surat-pengajuan-atk-akademik-fakultas')
            <x-stepper-akademik-fakultas-pengajuan-atk :surat='$surat' />
        @elseif(
            ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-tugas') ||
                ($surat->jenisSurat->user_type == 'staff' && $surat->jenisSurat->slug == 'surat-tugas-kelompok'))
            <x-stepper-flexible :surat='$surat' />
        @elseif($surat->jenisSurat->user_type == 'staff-dekan')
            <x-stepper-flexible :surat='$surat' />
        @endif
    </div>

    @if (
        $approval->surat->jenisSurat->slug != 'berita-acara-nilai' &&
            $approval->surat->jenisSurat->slug != 'surat-pengajuan-atk' &&
            $approval->surat->jenisSurat->slug != 'surat-pengajuan-atk-akademik' &&
            $approval->surat->jenisSurat->slug != 'surat-pengajuan-atk-akademik-fakultas' &&
            $approval->surat->jenisSurat->slug != 'surat-pengajuan-atk-kemahasiswaan')

        @if ($approval->surat->status == 'selesai')
            <a href="{{ route('print-surat-kabag', $approval->surat->id) }}"><button type="button"
                    class="text-white mt-8 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Cetak</button></a>
        @else
            <a href="{{ route('preview-surat-kabag', $approval->surat->id) }}"><button type="button"
                    class="text-white mt-8 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Preview</button></a>
            <p class="italic text-slate-500">Surat dianggap sah jika status surat adalah selesai dan sudah terdapat
                tanda tangan berupa QR Code.
            </p>
        @endif

    @endif
</x-layout>
