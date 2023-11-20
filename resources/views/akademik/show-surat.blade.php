@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->pengaju->name . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Detail Surat
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">{{ $surat->jenisSurat->name }}</h1>
    <br>
    <div class="flex flex-col gap-10 justify-evenly items-start">

        <div class="flex flex-col md:flex-row gap-10 w-full justify-evenly">
            <div class="w-full overflow-x-auto shadow-lg sm:rounded-lg">
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
                            <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold">Masa Aktif Tersisa:&nbsp;
                            </td>
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
                            @if ($key == 'private')
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
                                            href="{{ route('show-file-akademik', ['surat' => $surat->id, 'filename' => basename($value)]) }}">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                    </tbody>
                </table>
            </div>

            <x-stepper :surat='$surat' />
        </div>


        @if ($surat->current_user_id == auth()->user()->id && $surat->status == 'diproses')
            <form action="{{ route('setujui-surat-akademik', $surat->id) }}" method="POST"
                class="bg-slate-100 rounded-lg w-full">
                @csrf
                @method('put')
                <div class=" flex flex-col gap-4 mt-10 items-center justify-center">
                    <div class=" w-full max-w-[400px]">
                        <label for="no-surat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor
                            Surat <span class="text-red-600">*</span></label>
                        <input type="number" id="no-surat" name="no-surat" value="{{ old('no-surat') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Masukkan 4 digit no. surat, misal 0001" required>
                    </div>

                    {{-- <div class="w-full max-w-[400px]">
                        <label for="stempel"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stempel yang
                            digunakan (Jika tidak sesuai/tidak muncul, Anda dapat mengubahnya di profil akun <a
                                class="underline text-blue-600" href="/akademik/profile">di sini</a>)</label>
                        <input type="text" name="stempel"
                            class="bg-gray-50 hidden border cur border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            value="ok">
                        <img class="w-20" src="{{ asset('storage/' . $authUser->tandatangan) }}" alt="">
                    </div> --}}
                    <div class="w-full max-w-[400px]">
                        <label for="note"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan
                            (opsional)</label>
                        <textarea id="note" name="note"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Masukkan catatan yang ingin disampaikan ke mahasiswa">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="flex mt-8 justify-between flex-col sm:flex-row ">
                    <a href="{{ route('preview-surat-akademik', $surat->id) }}"><button type="button"
                            class="text-white w-full p-2 m-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm  dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Preview</button></a>
                    <div class="flex flex-col sm:flex-row">




                        <button
                            class="hover:bg-green-600 cursor-pointer rounded-lg text-center bg-green-500 p-2 text-white m-2"
                            type="submit">
                            Setuju </button>

                        <a href="{{ route('confirm-tolak-surat-akademik', $surat->id) }}">
                            <div
                                class="hover:bg-pink-800 cursor-pointer rounded-lg text-center bg-pink-600 p-2 text-white m-2">
                                Tolak

                            </div>
                        </a>
                    </div>
        @endif
    </div>
    </form>

    </div>




</x-layout>
