@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->pengaju->name . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Detail Surat
    </x-slot:title>
    @php
        $urlPath = request()->path();
    @endphp

    {{ Breadcrumbs::render('akademik-show-pengajuan-surat', $surat) }}

    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-4 md:flex-row justify-evenly items-start">

        <div class="w-full overflow-x-auto shadow-lg sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-400">
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700">

                        <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Status:&nbsp;</td>
                        <td class="px-6 py-4">
                            {{ $surat->status }}
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        @php
                            $recentStatus = 'user';
                            if ($surat->status == 'diproses') {
                                $recentStatus = 'Menunggu';
                            } elseif ($surat->status == 'ditolak') {
                                $recentStatus = 'Ditolak';
                            } elseif ($surat->status == 'selesai') {
                                $recentStatus = 'Diterima';
                            }
                        @endphp
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">{{ $recentStatus }}:&nbsp;
                        </td>
                        <td class="px-6 py-4">{{ $surat->current_user->name }}</td>
                    </tr>
                    @php
                        $riwayatPenolakan = App\Models\Approval::where('surat_id', '=', $surat->id)
                            ->where('isApproved', '=', 0)
                            ->first();
                    @endphp

                    @if ($surat->status == 'ditolak')
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Catatan Penolakan:&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ $riwayatPenolakan->note }}</td>
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
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Masa Aktif Tersisa:&nbsp;</td>
                        <td class="px-6 py-4">{{ formatTimestampToDiffDays($surat->expired_at) }} hari</td>
                    </tr>
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
                                {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:&nbsp;
                            </td>
                            <td class="px-6 py-4">{{ $value }}</td>
                        </tr>
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

        @if ($surat->jenisSurat->user_type == 'akademik' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-akademik')
            <x-stepper-akademik-pengajuan-atk :surat='$surat' />
        @endif

    </div>

    {{-- ACTION BAR UNTUK DETAIL RIWAYAT PENGAJUAN SURAT DARI AKADEMIK --}}
    @if ($surat->jenisSurat->user_type == 'akademik' && $surat->jenisSurat->slug == 'surat-pengajuan-atk-akademik')
        {{-- Surat pengajuan ATK tidak memiliki preview/cetak --}}
        <p class="italic text-slate-500 mt-8">Surat pengajuan ATK ini tidak memiliki preview/cetak.
            Jika terdapat kesalahan dalam surat, silahkan batalkan pengajuan surat.
        </p>
    @endif

    </div>
    </form>

    </div>

</x-layout>
