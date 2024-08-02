@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->pengaju->name . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff WD1 | Edit Surat
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">Edit {{ $surat->jenisSurat->name }}</h1>
    <br>
    <form action="{{ route('update-surat-staff-wd1', $surat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('put')
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Data dosen:</p>
        @foreach ($surat->data['dosen'] as $dosen)
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="nama-dosen" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama<span
                            class="text-red-500">*</span></label>
                    <input type="text"name="nama-dosen" id="nama-dosen"
                        class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan nama lengkap dan gelar" value="{{ $dosen['namaDosen'] }}" required>
                </div>
                <div>
                    <label for="nip-dosen" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP<span
                            class="text-red-500">*</span></label>
                    <input type="text"name="nip-dosen" id="nip-dosen"
                        class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan NIP" value="{{ $dosen['nipDosen'] }}" required>
                </div>
                <div>
                    <label for="pangkat-dosen"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pangkat/golongan<span
                            class="text-red-500">*</span></label>
                    <input type="text"name="pangkat-dosen" id="pangkat-dosen"
                        class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Contoh: Penata/IIIc" value="{{ $dosen['pangkatDosen'] }}" required>
                </div>
                <div>
                    <label for="jabatan-fungsional-dosen"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jabatan Fungsional<span
                            class="text-red-500">*</span></label>
                    <input type="text"name="jabatan-fungsional-dosen" id="jabatan-fungsional-dosen"
                        class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Contoh: Lektor" value="{{ $dosen['jabatanFungsionalDosen'] }}" required>
                </div>
            </div>
        @endforeach

        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Data penugasan:</p>
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="acara"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Acara/kegiatan<span
                        class="text-red-500">*</span></label>
                <input type="text"name="acara" id="acara"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: International Doctoral Supervision Bootcamp"
                    value="{{ $surat->data['acara'] }}" required>
            </div>
            <div>
                <label for="tempat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat<span
                        class="text-red-500">*</span></label>
                <input type="text"name="tempat" id="tempat"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan tempat pelaksanaan" value="{{ $surat->data['tempat'] }}" required>
            </div>
            <div>
                <label for="waktu-mulai-penugasan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu Mulai Penugasan<span
                        class="text-red-500">*</span></label>
                <input type="date" id="waktu-mulai-penugasan" name="waktu-mulai-penugasan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih waktu kapan penugasan dimulai"
                    value="{{ $surat->data['private']['waktuMulaiPenugasan'] }}" required>
            </div>
            <div>
                <label for="waktu-selesai-penugasan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu Selesai Penugasan<span
                        class="text-red-500">*</span></label>
                <input type="date" id="waktu-selesai-penugasan" name="waktu-selesai-penugasan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih waktu kapan penugasan selesai"
                    value="{{ $surat->data['private']['waktuSelesaiPenugasan'] }}" required>
            </div>
            <div>
                <label for="dasar-penugasan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dasar
                    Penugasan<span class="text-red-500">*</span></label>
                <input type="text"name="dasar-penugasan" id="dasar-penugasan"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh:  Surat Koordinator Prodi S2 Pendidikan Bahasa Inggris Nomor 85/UN30.7.7/PP/2024 tanggal 17 April 2024"
                    value="{{ $surat->data['dasarPenugasan'] }}" required>
            </div>

        </div>

        <div class="flex justify-center gap-4">
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
            <button type="button" onclick="window.location.href='{{ route('show-surat-staff-wd1', $surat->id) }}'"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Batal</button>
        </div>

    </form>



</x-layout>
