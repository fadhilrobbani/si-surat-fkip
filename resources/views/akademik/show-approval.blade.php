@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $approval->surat->data['name'] . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Detail Riwayat Persetujuan
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">{{ $approval->surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-4 md:flex-row justify-evenly items-start">
        <div class="relative overflow-x-auto shadow-lg sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700">

                        <td class="font-semibold px-6 py-4 bg-gray-50 dark:bg-gray-800">Status:&nbsp;</td>
                        <td class="px-6 py-4">
                            {{ $approval->surat->expired_at < Carbon\Carbon::now() && $approval->surat->status == 'on_process' ? 'expired' : $approval->surat->status }}
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
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">
                                {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:&nbsp;</td>
                            <td class="px-6 py-4">{{ $value }}</td>
                        </tr>
                    @endforeach
                    @if (isset($approval->surat->files))

                        @foreach ($approval->surat->files as $key => $value)
                            <tr  class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Lampiran {{ Str::title(str_replace('_', ' ', $key)) }}:</td>
                                <td class="px-6 py-4">
                                    <a class="text-blue-700 underline"
                                        href="{{ route('show-file-akademik', ['surat' => $approval->surat->id, 'filename' => basename($value)]) }}">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>
        </div>


        <x-stepper :surat='$surat'/>
    </div>


    @if ($approval->surat->status == 'finished')
        <a href="{{ route('print-surat-akademik', $approval->surat->id) }}"><button type="button"
                class="text-white mt-8 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Cetak</button></a>
    @else
        <button type="button" disabled
            class="text-white cursor-not-allowed mt-8 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Cetak</button>
        <p class="italic text-slate-500">Surat belum dapat dicetak selama surat belum selesai / disetujui akademik</p>
    @endif
</x-layout>
