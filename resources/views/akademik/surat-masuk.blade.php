@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Surat Masuk
    </x-slot:title>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">Foto</th>
                    <th scope="col" class="px-4 py-3">Nama</th>
                    <th scope="col" class="px-4 py-3">NPM</th>
                    <th scope="col" class="px-4 py-3">Email</th>
                    <th scope="col" class="px-4 py-3">Surat yang Diajukan</th>
                    <th scope="col" class="px-4 py-3">Masa Aktif</th>
                    <th scope="col" class="px-4 py-3">
                        Actions
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarSuratMasuk as $surat)
                    @php
                        $avatar = 'https://ui-avatars.com/api/?name=' . $surat->data['name'] . '&background=random';
                    @endphp
                    <tr class=" border-b dark:border-gray-700 hover:bg-slate-100">
                        <th scope="row"
                            class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <img class="max-w-40 max-h-40" src="{{ $avatar }}" alt="profile-picture">
                        </th>
                        <th scope="row"
                            class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $surat->data['name'] }}
                        </th>

                        <td class="px-4 py-3">{{ $surat->data['username'] }}</td>
                        <td class="px-4 py-3">{{ $surat->data['email'] }}</td>
                        @php
                            $jenisSurat = App\Models\JenisSurat::find($surat->jenis_surat_id);
                        @endphp
                        <td class="px-4 py-3">{{ $jenisSurat->name }}</td>
                        <td class="px-4 py-3">{{ formatTimestampToDiffDays($surat->expired_at) }} hari</td>
                        <td class="px-4 py-3 flex ">


                            <div
                                class="hover:bg-blue-800 cursor-pointer rounded-lg text-center bg-blue-600 p-2 text-white m-2">
                                <a href="{{ route('show-surat-akademik', $surat->id) }}">Lihat</a>

                            </div>
                            <form
                                class="hover:bg-green-600 cursor-pointer rounded-lg text-center bg-green-500 p-2 text-white m-2"
                                action="{{ route('setujui-surat-akademik', $surat->id) }}" method="POST">
                                @csrf
                                @method('put')
                                <button type="submit">
                                    Setuju </button>
                            </form>
                            <div
                                class="hover:bg-pink-800 cursor-pointer rounded-lg text-center bg-pink-600 p-2 text-white m-2">
                                <a href="{{ route('confirm-tolak-surat-akademik', $surat->id) }}">Tolak</a>

                            </div>

                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</x-layout>
