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
        <x-stepper :surat='$surat'/>
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
