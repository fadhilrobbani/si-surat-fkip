@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Bendahara | Surat Masuk
    </x-slot:title>
    <div class="overflow-x-auto">
        <h1 class="mx-auto text-center font-bold mb-4">Daftar Pengajuan Pencairan Dana (Menunggu Verifikasi)</h1>
        
        <form id="filter-form" method="GET" class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <div class="w-full md:w-1/2">
                <div class="flex items-center">
                    <label for="search" class="sr-only">Cari Pengaju / Kegiatan</label>
                    <input type="text" id="search" name="search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="Cari berdasarkan nama pengaju atau jenis surat..." value="{{ request()->get('search') }}">
                </div>
            </div>
            <div class="flex gap-2">
                <select id="order" name="order" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option {{ request()->get('order') != 'asc' ? 'selected' : '' }} value="desc">Terbaru</option>
                    <option {{ request()->get('order') == 'asc' ? 'selected' : '' }} value="asc">Terlama</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Filter</button>
            </div>
        </form>

        <div class="w-full overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Pengaju</th>
                        <th class="px-6 py-3">Jenis Pengajuan</th>
                        <th class="px-6 py-3">Nama Kegiatan</th>
                        <th class="px-6 py-3">Total Anggaran</th>
                        <th class="px-6 py-3">Tanggal Masuk</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarSuratMasuk as $index => $surat)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $daftarSuratMasuk->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $surat->pengaju->name }}<br>
                                <span class="text-xs text-gray-500">({{ $surat->pengaju->username }})</span>
                            </td>
                            <td class="px-6 py-4">{{ $surat->jenisSurat->name }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $surat->data['namaKegiatan'] ?? ($surat->data['keperluan'] ?? '-') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600">
                                @if (isset($surat->data['totalAnggaran']))
                                    Rp {{ number_format($surat->data['totalAnggaran'], 0, ',', '.') }}
                                @elseif (isset($surat->data['nominal']))
                                    Rp {{ number_format($surat->data['nominal'], 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ formatTimestampToIndonesian($surat->created_at) }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('show-surat-masuk-bendahara', $surat->id) }}"
                                    class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-xs px-3 py-1.5">
                                    Periksa & Cairkan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">Tidak ada pengajuan dana yang menunggu saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $daftarSuratMasuk->links() }}
        </div>
    </div>
</x-layout>
