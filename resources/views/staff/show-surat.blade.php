@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->data['name'] . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Detail Surat
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-4 md:flex-row justify-evenly items-center">

        <table border="2">
            <tr>
                <td class="font-semibold">Status:&nbsp;</td>
                <td>{{ $surat->status }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Tujuan selanjutnya:&nbsp;</td>
                <td>{{ $surat->penerima->name ?? '-' }}</td>
            </tr>
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
                    <tr>
                        <td class="font-semibold">{{ Str::title(str_replace('_', ' ', $key)) }}:&nbsp;
                        </td>
                        <td>{{ formatTimestampToIndonesian($surat->created_at) }}</td>
                    </tr>
                    @continue
                @endif


                <tr>
                    <td class="font-semibold">{{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:&nbsp;</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
            @foreach ($surat->files as $key => $value )
            <tr>
                <td class="font-semibold">Lampiran {{ Str::title(str_replace('_', ' ', $key))  }}:</td>
                <td>
                    <a class="text-blue-700 underline" href="{{ route('show-file-staff', ['surat' => $surat->id, 'filename' => basename($value)]) }}">Lihat</a>
                </td>
            </tr>
            @endforeach
        </table>

    </div>


    <div class="flex mt-8 justify-end flex-col sm:flex-row ">

        {{-- <button type="button"
            class="text-white p-2 m-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Lihat
            Lampiran</button> --}}

        @if ($surat->current_user_id == auth()->user()->id && $surat->status == 'on_process')
            <div class="flex flex-col sm:flex-row">

                <form class="hover:bg-green-600 cursor-pointer rounded-lg text-center bg-green-500 p-2 text-white m-2"
                    action="{{ route('setujui-surat', $surat->id) }}" method="POST">
                    @csrf
                    @method('put')
                    <button type="submit">
                        Setuju </button>
                </form>
                <div class="hover:bg-pink-800 cursor-pointer rounded-lg text-center bg-pink-600 p-2 text-white m-2">
                    <a href="{{ route('confirm-tolak-surat', $surat->id) }}">Tolak</a>

                </div>
            </div>
        @endif
    </div>

</x-layout>
