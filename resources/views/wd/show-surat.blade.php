@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->data['name'] . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        WD | Detail Surat
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-4 md:flex-row justify-evenly items-start">

        <div class="relative overflow-x-auto shadow-lg sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
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
                            if ($surat->status == 'on_process') {
                                $recentStatus = 'Menunggu';
                            } elseif ($surat->status == 'denied') {
                                $recentStatus = 'Ditolak';
                            } elseif ($surat->status == 'finished') {
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

                    @if ($surat->status == 'denied')
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
                    @if ($surat->status == 'finished' && isset($surat->data['tanggal_selesai']))
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
                                {{-- Str::title(str_replace('_', ' ', $key)) . --}}
                                <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Lampiran
                                    {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', $key))) }}:</td>
                                <td class="px-6 py-4">
                                    <a class="text-blue-700 underline"
                                        href="{{ route('show-file-wd', ['surat' => $surat->id, 'filename' => basename($value)]) }}">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>
        </div>

        <x-stepper :surat='$surat' />
    </div>


    <div class="flex mt-8 justify-end flex-col sm:flex-row ">

        {{-- <button type="button"
            class="text-white p-2 m-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Preview</button> --}}

        @if ($surat->current_user_id == auth()->user()->id && $surat->status == 'on_process')
            <div class="flex flex-col sm:flex-row">

                <form action="{{ route('setujui-surat-wd', $surat->id) }}" method="POST">
                    @csrf
                    @method('put')
                    <x-modal-send :daftarPenerima='$daftarPenerima' />
                    <button
                        class="hover:bg-green-600 cursor-pointer rounded-lg text-center bg-green-500 p-2 text-white m-2"
                        data-modal-target="authentication-modal" data-modal-toggle="authentication-modal">
                        Setuju </button>

                </form>
                <a href="{{ route('confirm-tolak-surat-wd', $surat->id) }}">
                    <div class="hover:bg-pink-800 cursor-pointer rounded-lg text-center bg-pink-600 p-2 text-white m-2">
                        Tolak

                    </div>
                </a>
            </div>
        @endif
    </div>

</x-layout>
