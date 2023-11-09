@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->data['name'] . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Detail Surat
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-4 md:flex-row justify-evenly items-center">

        <table class="flex flex-col  gap-10" border="2">
            <tr>
                <td class="font-semibold">Status:&nbsp;</td>
                <td>{{ $surat->expired_at < Carbon\Carbon::now() && $surat->status === 'on_process' ? 'expired' : $surat->status }}
                </td>
            </tr>
            <tr>
                @php
                    $recentStatus = 'user';
                    if ($surat->expired_at < Carbon\Carbon::now() && $surat->status === 'on_process') {
                        $recentStatus = 'Masa Aktif Habis Saat';
                    } elseif ($surat->status == 'on_process') {
                        $recentStatus = 'Menunggu';
                    } elseif ($surat->status == 'denied') {
                        $recentStatus = 'Ditolak';
                    } elseif ($surat->status == 'finished') {
                        $recentStatus = 'Diterima';
                    }
                @endphp
                <td class="font-semibold">{{ $recentStatus }}:&nbsp;</td>
                <td>{{ $surat->current_user->name }}</td>
            </tr>
            @php
                $riwayatPenolakan = App\Models\Approval::where('surat_id', '=', $surat->id)
                    ->where('isApproved', '=', 0)
                    ->first();
            @endphp

            @if ($surat->status == 'denied')
                <tr>
                    <td class="font-semibold">Catatan Penolakan:&nbsp;</td>
                    <td>{{ $riwayatPenolakan->note }}</td>
                </tr>
                <tr>
                    <td class="font-semibold">Tanggal Penolakan:&nbsp;</td>
                    <td>{{ formatTimestampToIndonesian($riwayatPenolakan->created_at) }}</td>
                </tr>
            @endif
            @if ($surat->status == 'finished' && isset($surat->data['tanggal_selesai']))
                <tr>
                    <td class="font-semibold">Tanggal Disetujui:&nbsp;</td>
                    <td>{{ $surat->data['tanggal_selesai'] }}
                    </td>
                </tr>
            @endif
            <tr>
                <td class="font-semibold">Tanggal Diajukan:&nbsp;</td>
                <td>{{ formatTimestampToIndonesian($surat->created_at) }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Masa Aktif Tersisa:&nbsp;</td>
                <td>{{ formatTimestampToDiffDays($surat->expired_at) }} hari</td>
            </tr>
            @foreach ($surat->data as $key => $value)
                @if ($key == 'tanggal_selesai')
                    {{-- <tr>
                        <td class="font-semibold">{{ Str::title(str_replace('_', ' ', $key)) }}:&nbsp;
                        </td>
                        <td>{{ $value}}</td>
                    </tr> --}}
                    @continue
                @endif
                @if ($key == 'ttdWD1')
                    @continue
                @endif
                <tr>
                    <td class="font-semibold">{{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:&nbsp;</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
            @if (isset($surat->files))

                @foreach ($surat->files as $key => $value)
                    <tr>
                        {{-- Str::title(str_replace('_', ' ', $key)) . --}}
                        <td class="font-semibold border-1 border-slate-300">Lampiran
                            {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:</td>
                        <td>
                            <a class="text-blue-700 underline"
                                href="{{ route('show-file-mahasiswa', ['surat' => $surat->id, 'filename' => basename($value)]) }}">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            @endif
            </table>

            <div>
                <ol
                    class="relative mx-8 text-gray-500 border-l border-gray-200 dark:border-gray-700 dark:text-gray-400">
                    <li class="mb-10 ml-6">
                        @if (count($step) == 0)
                            <span
                                class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                                <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                    <path
                                        d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                                </svg>
                            </span>
                        @else
                            <span
                                class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-green-900">
                                <svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                                </svg>
                            </span>
                        @endif
                        <h3 class="font-medium leading-tight">Staff</h3>
                        <p class="text-sm">Step details here</p>
                    </li>
                    <li class="mb-10 ml-6">
                        <span
                            class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                <path
                                    d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                            </svg>
                        </span>
                        <h3 class="font-medium leading-tight">Kaprodi</h3>
                        <p class="text-sm">Step details here</p>
                    </li>
                    <li class="mb-10 ml-6">
                        <span
                            class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                <path
                                    d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                            </svg>
                        </span>
                        <h3 class="font-medium leading-tight">Wakil Dekan 1</h3>
                        <p class="text-sm">Step details here</p>
                    </li>
                    <li class="ml-6">
                        <span
                            class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                <path
                                    d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                            </svg>
                        </span>
                        <h3 class="font-medium leading-tight">Akademik</h3>
                        <p class="text-sm">Step details here</p>
                    </li>
                </ol>
            </div>
        </div>

        {{-- <embed
    src="{{ route('show-file',basename($surat->files['ijazah'])) }}"
    style="width:600px; height:800px;"
    frameborder="0"> --}}

        @if ($surat->status == 'finished')
            <a href="{{ route('print-surat-mahasiswa', $surat->id) }}"> <button type="button"
                    class="text-white mt-8 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Cetak</button></a>
        @else
            <button type="button" disabled
                class="text-white cursor-not-allowed mt-8 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Cetak</button>
            <p class="italic text-slate-500">Surat belum dapat dicetak selama surat belum disetujui</p>
        @endif


</x-layout>
