@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Tata Usaha | Surat Masuk
    </x-slot:title>
    <div class="overflow-x-auto">
        {{ Breadcrumbs::render('surat-masuk') }}
        <h1 class="mx-auto text-center font-bold text-xl my-4">Surat Masuk</h1>

        <form id="filter-form" method="GET"
            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <div class="w-full md:w-1/2">
                <div class="flex items-center">
                    <label for="search" class="sr-only">Cari (Nama/NPM/Kegiatan)</label>
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
            <div
                class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">

                <div class="flex items-center space-x-3 w-full md:max-w-[200px] md:w-auto">
                    <select id="jenis-surat" name="jenis-surat"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" selected>Jenis Surat (Semua)</option>
                        @foreach ($daftarJenisSurat as $jenisSurat)
                            <option {{ $jenisSurat->id == request()->get('jenis-surat') ? 'selected' : '' }}
                                value="{{ $jenisSurat->id }}">{{ $jenisSurat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-3 w-full md:max-w-[150px] md:w-auto">
                    <select id="order" name="order"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option {{ request()->get('order') != 'asc' ? 'selected' : '' }} value="desc">Terbaru</option>
                        <option {{ request()->get('order') == 'asc' ? 'selected' : '' }} value="asc">Terlama</option>
                    </select>
                </div>

                <button type="submit"
                    class="flex cursor-pointer items-center justify-center text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    Filter
                </button>

                <a href="{{ route('surat-masuk-tata-usaha') }}"
                    class="flex cursor-pointer items-center justify-center text-white bg-rose-600 hover:bg-rose-700 font-medium rounded-lg text-sm px-4 py-2">
                    Reset
                </a>
            </div>
        </form>

        @if ($daftarSuratMasuk->isEmpty())
            <p class="text-slate-500 text-xl font-semibold text-center mx-auto my-8">Tidak terdapat Surat Masuk</p>
        @else
            <div class="w-full overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-700 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Foto</th>
                            <th scope="col" class="px-4 py-3">Nama</th>
                            <th scope="col" class="px-4 py-3">NPM / Username</th>
                            <th scope="col" class="px-4 py-3">Program Studi</th>
                            <th scope="col" class="px-4 py-3">Surat yang Diajukan</th>
                            <th scope="col" class="px-4 py-3">Tanggal Diajukan</th>
                            <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarSuratMasuk as $surat)
                            @php
                                $namaPengaju = $surat->data['nama'] ?? ($surat->pengaju->name ?? 'User');
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
                                    {{ $surat->data['npm'] ?? $surat->data['username'] ?? $surat->pengaju->username ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $surat->data['programStudi'] ?? ($surat->pengaju->programStudi->name ?? '-') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $surat->jenisSurat->name }}
                                    @if (isset($surat->data['namaRuangan']) || isset($surat->data['namaKegiatan']))
                                        <span class="block text-xs text-gray-500">
                                            {{ $surat->data['namaRuangan'] ?? '' }} {{ isset($surat->data['namaRuangan'], $surat->data['namaKegiatan']) ? '-' : '' }} {{ $surat->data['namaKegiatan'] ?? '' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    {{ formatTimestampToIndonesian($surat->created_at) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('show-surat-masuk-tata-usaha', $surat->id) }}"
                                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-xs px-3 py-1.5 transition">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $daftarSuratMasuk->links() }}
            </div>
        @endif
    </div>
</x-layout>
