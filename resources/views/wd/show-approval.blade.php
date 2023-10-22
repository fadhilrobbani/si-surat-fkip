@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $approval->surat->data['name'] . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        WD| Detail Surat
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">{{ $approval->surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-4 md:flex-row justify-evenly items-center">

        <table border="2">
            <tr>
                <td class="font-semibold">Status Surat:&nbsp;</td>
                <td>{{ $approval->surat->status }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Hasil Konfirmasi Anda:&nbsp;</td>
                <td>{{ $approval->isApproved == 1 ? 'Disetujui' : 'Ditolak' }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Tanggal Diajukan:&nbsp;</td>
                <td>{{ formatTimestampToIndonesian($approval->surat->created_at) }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Tanggal Anda Menyetujui/Menolak:&nbsp;</td>
                <td>{{ formatTimestampToIndonesian($approval->created_at) }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Masa Aktif Tersisa:&nbsp;</td>
                <td>{{ formatTimestampToDiffDays($approval->surat->expired_at) }} hari</td>
            </tr>
            @foreach ($approval->surat->data as $key => $value)
                @if ($key == 'tanggal_selesai')
                    <tr>
                        <td class="font-semibold">{{ Str::title(str_replace('_', ' ', $key)) }}:&nbsp;
                        </td>
                        <td>{{ formatTimestampToIndonesian($value) }}</td>
                    </tr>
                    @continue
                @endif
                <tr>
                    <td class="font-semibold">{{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:&nbsp;</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </table>

    </div>


    <div class="flex mt-8 justify-between flex-col sm:flex-row ">

        <button type="button"
            class="text-white p-2 m-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Preview</button>
    </div>

</x-layout>
