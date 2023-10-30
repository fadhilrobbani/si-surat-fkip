@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->data['name'] . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Detail Surat
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-4 justify-evenly items-center">

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
                        <td>{{ formatTimestampToIndonesian($value) }}</td>
                    </tr>
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
                        <td class="font-semibold">Lampiran {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:</td>
                        <td>
                            <a class="text-blue-700 underline"
                                href="{{ route('show-file-akademik', ['surat' => $surat->id, 'filename' => basename($value)]) }}">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
        @if ($surat->current_user_id == auth()->user()->id && $surat->status == 'on_process')
            <form action="{{ route('setujui-surat-akademik', $surat->id) }}" method="POST" class="w-full">
                @csrf
                @method('put')
                <div class="flex justify-center mx-auto items-center w-full max-w-[400px]">
                    <label for="no-surat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor
                        Surat</label>
                    <input type="number" id="no-surat" name="no-surat"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan 4 digit no. surat, misal 0001" required>
                </div>
                <div class="justify-center mx-auto items-center flex gap-2 w-full max-w-[400px]">
                    <label for="note"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan</label>
                    <textarea id="note" name="note"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan catatan yang ingin disampaikan ke mahasiswa" required></textarea>
                </div>
                <div class="flex mt-8 justify-between flex-col sm:flex-row ">

                    <a href="{{ route('preview-surat', $surat->id) }}"><button type="button"
                            class="text-white p-2 m-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Preview</button></a>


                    <div class="flex flex-col sm:flex-row">

                        <div
                            class="hover:bg-green-600 cursor-pointer rounded-lg text-center bg-green-500 p-2 text-white m-2">

                            <button type="submit">
                                Setuju </button>
                        </div>
                        <div
                            class="hover:bg-pink-800 cursor-pointer rounded-lg text-center bg-pink-600 p-2 text-white m-2">
                            <a href="{{ route('confirm-tolak-surat-akademik', $surat->id) }}">Tolak</a>

                        </div>
                    </div>
        @endif
    </div>
    </form>

    </div>




</x-layout>
