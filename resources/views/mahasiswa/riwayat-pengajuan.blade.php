@php
    $authUser = auth()->user();
    $number = 1;

@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Dashboard
    </x-slot:title>
    {{-- @foreach ($daftarPengajuan as $surat)
        <div class="m-4 bg-slate-300">
            @php

                // Tanggal kadaluarsa dari surat (contoh)
                $expiredAt = Illuminate\Support\Carbon::parse($surat->expired_at); // Gantilah dengan tanggal kadaluarsa yang sesuai

                // Waktu saat ini
                $now = Illuminate\Support\Carbon::now();

                // Hitung sisa waktu kadaluarsa dalam hari
                $sisaHari = $now->diffInDays($expiredAt);
            @endphp

            <p>{{ $surat->pengaju->name }}</p>
            <p>{{ $surat->pengaju->id }}</p>
            <p>{{ $surat->data['programStudi'] }}</p>
            <p>{{ $surat->jenis_surat_id }}</p>
            <p>{{ $surat->status }}</p>
            <p>Kadaluarsa dalam {{ $sisaHari }}</p>
            <p>Menunggu: {{ $surat->current_user->name }}</p>
        </div>
    @endforeach --}}

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">No.</th>
                    <th scope="col" class="px-4 py-3">Jenis Surat</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3">Tanggal&Waktu Diajukan</th>
                    <th scope="col" class="px-4 py-3">Masa Aktif</th>
                    <th scope="col" class="px-4 py-3">
                        Actions
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarPengajuan as $surat)
                    @php
                        $avatar = 'https://ui-avatars.com/api/?name=' . $surat->data['name'] . '&background=random';

                        // Tanggal kadaluarsa dari surat (contoh)
                        $expiredAt = Illuminate\Support\Carbon::parse($surat->expired_at); // Gantilah dengan tanggal kadaluarsa yang sesuai

                        // Waktu saat ini
                        $now = Illuminate\Support\Carbon::now();

                        // Hitung sisa waktu kadaluarsa dalam hari
                        $masaAktif = $now->diffInDays($expiredAt);

                    @endphp
                    <tr class=" border-b dark:border-gray-700 hover:bg-slate-100">
                        <th scope="row"
                            class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $number++ }}
                        </th>
                        <th scope="row"
                            class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $surat->jenisSurat->name }}
                        </th>

                        <td class="px-4 py-3">{{ $surat->status }}</td>
                        <td class="px-4 py-3">{{ formatTimestampToIndonesian($surat->created_at) }}</td>

                        <td class="px-4 py-3">{{ $masaAktif }} hari</td>
                        <td class="px-4 py-3 flex ">


                            <div
                                class="hover:bg-blue-800 cursor-pointer rounded-lg text-center bg-blue-600 p-2 text-white m-2">
                                <a href="{{ route('lihat-surat-mahasiswa', $surat->id) }}">Lihat</a>

                            </div>

                            @if ($surat->status == 'on_process')
                                <form
                                    class="hover:bg-pink-800 cursor-pointer rounded-lg text-center bg-pink-600 p-2 text-white m-2"
                                    action="{{ route('destroy-surat', $surat->id) }}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <button type="submit">
                                        Batal </button>
                                </form>
                            @endif

                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</x-layout>
