@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Tata Usaha | Riwayat Persetujuan
    </x-slot:title>
    <div class="overflow-x-auto">
        {{ Breadcrumbs::render('riwayat-persetujuan') }}
        <h1 class="mx-auto text-center font-bold text-xl my-4">Riwayat Persetujuan Surat</h1>

        <form id="filter-form" method="GET"
            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <div class="w-full md:w-1/2">
                <div class="flex items-center">
                    <label for="search" class="sr-only">Cari Pengaju / Surat</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="search" name="search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Cari pengaju atau kegiatan..." value="{{ request()->get('search') }}">
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <select id="order" name="order" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option {{ request()->get('order') != 'asc' ? 'selected' : '' }} value="desc">Terbaru</option>
                    <option {{ request()->get('order') == 'asc' ? 'selected' : '' }} value="asc">Terlama</option>
                </select>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Filter</button>
                <a href="{{ route('riwayat-persetujuan-tata-usaha') }}"
                    class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Reset</a>
            </div>
        </form>

        @if ($daftarRiwayatPersetujuan->isEmpty())
            <p class="text-slate-500 text-xl font-semibold text-center mx-auto my-8">Tidak terdapat Riwayat Persetujuan</p>
        @else
            <div class="w-full overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Foto</th>
                            <th scope="col" class="px-4 py-3">Nama</th>
                            <th scope="col" class="px-4 py-3">NPM / Username</th>
                            <th scope="col" class="px-4 py-3">Surat</th>
                            <th scope="col" class="px-4 py-3">Tanggal Ditinjau</th>
                            <th scope="col" class="px-4 py-3">Hasil Konfirmasi</th>
                            <th scope="col" class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center gap-1">
                                    Aksi
                                    <x-info-tooltip id="tooltip-riwayat-tu" size="w-3.5 h-3.5" />
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarRiwayatPersetujuan as $riwayatSurat)
                            @php
                                $namaPengaju = $riwayatSurat->surat->data['nama'] ?? ($riwayatSurat->surat->pengaju->name ?? 'User');
                                $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($namaPengaju) . '&background=random';
                            @endphp
                            <tr class="border-b dark:border-gray-700 hover:bg-slate-50">
                                <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    <img class="w-10 h-10 rounded-full" src="{{ $avatar }}" alt="avatar">
                                </th>
                                <th scope="row" class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $namaPengaju }}
                                </th>
                                <td class="px-4 py-3">
                                    {{ $riwayatSurat->surat->data['npm'] ?? $riwayatSurat->surat->data['username'] ?? $riwayatSurat->surat->pengaju->username ?? '-' }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $riwayatSurat->surat->jenisSurat->name }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ formatTimestampToIndonesian($riwayatSurat->created_at) }}
                                </td>
                                <td class="px-4 py-3 font-semibold {{ $riwayatSurat->isApproved == 1 ? 'text-green-600' : 'text-rose-600' }}">
                                    {{ $riwayatSurat->isApproved == 1 ? 'Disetujui' : 'Ditolak' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('show-approval-tata-usaha', $riwayatSurat->id) }}"
                                            class="inline-block bg-slate-700 hover:bg-slate-800 text-white font-medium rounded-lg text-xs px-2.5 py-1.5 transition">
                                            Lihat
                                        </a>
                                        @if ($riwayatSurat->surat->jenisSurat->slug != 'surat-pengajuan-atk-tata-usaha')
                                            @if ($riwayatSurat->surat->status == 'selesai')
                                                <a href="{{ route('print-surat-tata-usaha', $riwayatSurat->surat->id) }}" target="_blank"
                                                    class="inline-block bg-blue-700 hover:bg-blue-800 text-white font-medium rounded-lg text-xs px-2.5 py-1.5 transition">
                                                    Cetak
                                                </a>
                                            @else
                                                <a href="{{ route('preview-surat-tata-usaha', $riwayatSurat->surat->id) }}" target="_blank"
                                                    class="inline-block bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg text-xs px-2.5 py-1.5 transition">
                                                    Preview
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $daftarRiwayatPersetujuan->links() }}
            </div>
        @endif
    </div>
</x-layout>
