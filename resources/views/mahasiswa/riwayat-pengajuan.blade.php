@php
    $authUser = auth()->user();

@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Riwayat Pengajuan
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">Riwayat Pengajuan</h1>

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

    <div>

        <form id="filter-form" method="GET"
            class="flex   flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <div class="w-full md:w-1/2">
                <div class="flex items-center">
                    <label for="search" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="search" name="search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Cari (Jenis Surat)" value="{{ request()->get('search') }}">
                    </div>
                </div>
            </div>
            <div
                class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">

                <div class="flex items-center space-x-3 w-full md:max-w-[150px] md:w-auto">

                    <select id="jenis-surat" name="jenis-surat"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" selected>Jenis Surat (Semua)</option>
                        @foreach ($daftarJenisSurat as $jenisSurat)
                            <option {{ $jenisSurat->id == request()->get('jenis-surat') ? 'selected' : '' }}
                                value="{{ $jenisSurat->id }}">{{ $jenisSurat->name }}</option>
                        @endforeach

                    </select>


                </div>
                <div class="flex items-center space-x-3 w-full md:max-w-[250px] md:w-auto">

                    <select id="status" name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" selected>Status (Semua)</option>
                        @foreach ($daftarStatus as $status)
                            <option {{ request()->get('status') == $status ? 'selected' : '' }}
                                value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                        <option {{ request()->get('status') == 'expired' ? 'selected' : '' }} value="expired">
                            expired
                        </option>

                    </select>


                </div>
                <div class="flex items-center space-x-3 w-full md:max-w-[250px] md:w-auto">

                    <select id="order" name="order"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option {{ request()->get('order') != 'asc' ? 'selected' : '' }} value="desc" selected>
                            Terbaru
                        </option>
                        <option {{ request()->get('order') == 'asc' ? 'selected' : '' }} value="asc">Terlama
                        </option>


                    </select>


                </div>
                <button type="submit"
                    class="flex cursor-pointer items-center justify-center text-white bg-blue-500 hover:bg-blue-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>


                    &nbsp;Terapkan Filter
                </button>
                <script>
                    const resetFilter = () => {
                        document.getElementById('filter-form').reset();
                        document.getElementById('status').value = null;
                        document.getElementById('search').value = null;
                        document.getElementById('order').value = null;
                        document.getElementById('jenis-surat').value = null;
                        return false;
                    }
                </script>
                <button onclick="resetFilter()"
                    class="flex cursor-pointer items-center justify-center text-white bg-rose-600 hover:bg-rose-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>


                    &nbsp;Reset Filter
                </button>
            </div>
        </form>

        @if ($daftarPengajuan->isEmpty())
            <p class="text-slate-500 text-xl font-semibold text-center mx-auto">Tidak terdapat riwayat pengajuan</p>
        @else
            <div class="w-full overflow-x-auto">


                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">No.</th>
                            <th scope="col" class="px-4 py-3">Jenis Surat</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Tanggal&Waktu Diajukan</th>
                            <th scope="col" class="px-4 py-3">Masa Aktif</th>
                            <th scope="col" class="px-4 py-3">
                                Aksi
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($daftarPengajuan as $surat)
                            @php
                                $avatar =
                                    'https://ui-avatars.com/api/?name=' . $surat->pengaju->name . '&background=random';
                                $statusStyle = '';
                                if ($surat->status == 'selesai') {
                                    $statusStyle = ' text-green-400 font-semibold';
                                } elseif ($surat->status == 'diproses' && $surat->expired_at > Carbon\Carbon::now()) {
                                    $statusStyle = ' text-yellow-400 font-semibold';
                                } elseif ($surat->status == 'dikirim' && $surat->expired_at > Carbon\Carbon::now()) {
                                    $statusStyle = ' text-blue-400 font-semibold';
                                } elseif ($surat->expired_at < Carbon\Carbon::now() && $surat->status === 'diproses') {
                                    $statusStyle = ' text-pink-500 font-semibold';
                                } elseif (
                                    $surat->expired_at > Carbon\Carbon::now() &&
                                    $surat->status === 'menunggu_pembayaran'
                                ) {
                                    $statusStyle = ' text-yellow-600 font-semibold';
                                } elseif (
                                    $surat->expired_at < Carbon\Carbon::now() &&
                                    $surat->status === 'menunggu_pembayaran'
                                ) {
                                    $statusStyle = ' text-pink-600 font-semibold';
                                } else {
                                    $statusStyle = ' text-pink-500 font-semibold';
                                }
                            @endphp
                            <tr class=" border-b dark:border-gray-700 hover:bg-slate-100">
                                <th scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $loop->iteration + $daftarPengajuan->firstItem() - 1 }}
                                </th>
                                <th scope="row"
                                    class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $surat->jenisSurat->name }}
                                </th>
                                <td class="px-4 py-3">
                                    <p class="{{ $statusStyle }}">
                                        {{ $surat->expired_at < Carbon\Carbon::now() && $surat->status === 'diproses'
                                            ? 'expired'
                                            : ($surat->expired_at < Carbon\Carbon::now() && $surat->status === 'menunggu_pembayaran'
                                                ? 'tidak dikonfirmasi'
                                                : ($surat->status === 'menunggu_pembayaran'
                                                    ? 'menunggu konfirmasi'
                                                    : $surat->status)) }}
                                    </p>
                                </td>

                                <td class="px-4 py-3">{{ formatTimestampToIndonesian($surat->created_at) }}</td>

                                <td class="px-4 py-3">
                                    {{ formatTimestampToDiffDays($surat->expired_at) != 0 ? formatTimestampToDiffDays($surat->expired_at) . ' hari' : '-' }}
                                </td>
                                <td class="px-4 py-3 flex ">


                                    <a href="{{ route('lihat-surat-mahasiswa', $surat->id) }}">
                                        <div
                                            class="hover:bg-blue-800 cursor-pointer rounded-lg text-center bg-blue-600 p-2 text-white m-2">
                                            Lihat

                                        </div>
                                    </a>

                                    @if ($surat->jenisSurat->slug !== 'legalisir-ijazah' && $surat->status == 'diproses')
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
        @endif
    </div>
    <div class="mt-4">

        {{ $daftarPengajuan->links() }}
    </div>
</x-layout>
