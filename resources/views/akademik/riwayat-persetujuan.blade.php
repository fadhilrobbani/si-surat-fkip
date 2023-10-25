@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Riwayat Persetujuan
    </x-slot:title>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">Foto</th>
                    <th scope="col" class="px-4 py-3">Nama</th>
                    <th scope="col" class="px-4 py-3">NPM</th>

                    <th scope="col" class="px-4 py-3">Surat yang Diajukan</th>
                    <th scope="col" class="px-4 py-3">Tanggal disetujui/ditolak</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                    <th scope="col" class="px-4 py-3">
                        Actions
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarRiwayatSurat as $riwayatSurat)
                    @php
                        $avatar = 'https://ui-avatars.com/api/?name=' . $riwayatSurat->surat->pengaju->name . '&background=random';
                    @endphp
                    <tr class=" border-b dark:border-gray-700 hover:bg-slate-100">
                        <th scope="row"
                            class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <img class="max-w-40 max-h-40" src="{{ $avatar }}" alt="profile-picture">
                        </th>
                        <th scope="row"
                            class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $riwayatSurat->surat->pengaju->name }}
                        </th>

                        <td class="px-4 py-3">{{ $riwayatSurat->surat->data['username'] }}</td>


                        <td class="px-4 py-3">{{ $riwayatSurat->surat->jenisSurat->name }}</td>
                        <td class="px-4 py-3">{{ formatTimestampToIndonesian($riwayatSurat->created_at) }}</td>
                        <td class="px-4 py-3">{{ $riwayatSurat->isApproved == 1 ? 'Disetujui' : 'Ditolak' }}</td>
                        <td class="px-4 py-3 flex ">

                            <div
                                class="hover:bg-blue-800 cursor-pointer rounded-lg text-center bg-blue-600 p-2 text-white m-2">
                                <a href="{{ route('show-approval-akademik', $riwayatSurat->id) }}">Lihat</a>

                            </div>


                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        <div class="mt-4">
            {{ $daftarRiwayatSurat->links() }}
        </div>
    </div>
</x-layout>
