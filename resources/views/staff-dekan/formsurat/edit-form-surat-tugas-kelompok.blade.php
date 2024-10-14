@php
    $authUser = auth()->user();
    $step = [];
    $avatar = 'https://ui-avatars.com/api/?name=' . $surat->pengaju->name . '&background=random';
@endphp

<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff Dekan | Edit Surat
    </x-slot:title>
    <h1 class="mx-auto text-center font-bold">Edit {{ $surat->jenisSurat->name }}</h1>
    <br>
    <form action="{{ route('update-surat-staff-dekan', $surat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('put')
        <p class="font-semibold text-slate-500 text-md mx-auto mb-4">Data dosen:</p>
        <div x-data="{
            lecturers: {{ json_encode($surat->data['dosen'] ?? []) }}.length > 0 ?
                {{ json_encode($surat->data['dosen']) }} : [{ nipDosen1: '', namaDosen1: '', jabatanDosen1: '' }]
        }" class="mb-6">
            <template x-for="(lecturer, index) in lecturers" :key="index">
                <div class="mb-6 p-4 bg-slate-50 rounded-lg shadow-lg">
                    <p class="font-semibold text-slate-700 text-md mb-3" x-text="`Data Dosen ${index + 1}`"></p>
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <label :for="'nama-dosen-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">Nama<span
                                    class="text-red-500">*</span></label>
                            <input type="text" :name="`namaDosen${index + 1}`" :id="'nama-dosen-' + index"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Masukkan nama lengkap dan gelar" :value="lecturer[`namaDosen${index + 1}`]"
                                x-model="lecturer[`namaDosen${index + 1}`]" required>
                        </div>
                        <div>
                            <label :for="'nip-dosen-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">NIP<span
                                    class="text-red-500">*</span></label>
                            <input type="text" :name="`nipDosen${index + 1}`" :id="'nip-dosen-' + index"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Masukkan NIP" :value="lecturer[`nipDosen${index + 1}`]"
                                x-model="lecturer[`nipDosen${index + 1}`]" required>
                        </div>
                        <div class="md:col-span-2">
                            <label :for="'jabatan-dosen-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">Jabatan<span
                                    class="text-red-500">*</span></label>
                            <input type="text" :name="`jabatanDosen${index + 1}`" :id="'jabatan-dosen-' + index"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Contoh: Ketua Prodi Magister (S2) Pendidikan Bahasa Indonesia"
                                :value="lecturer[`jabatanDosen${index + 1}`]"
                                x-model="lecturer[`jabatanDosen${index + 1}`]" required>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="text-red-600 hover:text-red-800"
                            @click="lecturers.splice(index, 1)" x-show="lecturers.length > 1">
                            Hapus Dosen
                        </button>
                    </div>
                </div>
            </template>

            <div class="mt-4">
                <button type="button"
                    @click="lecturers.push({ [`namaDosen${lecturers.length + 1}`]: '', [`nipDosen${lecturers.length + 1}`]: '', [`jabatanDosen${lecturers.length + 1}`]: '' })"
                    class="text-white bg-slate-500 hover:bg-slate-700 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <span class="flex flex-row items-center justify-center gap-2">
                        <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14m-7 7V5" />
                        </svg>
                        <p>Tambah Dosen</p>
                    </span>
                </button>
            </div>
        </div>
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


        </div>



        <div class="flex justify-center gap-4">
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
            <button type="button" onclick="window.location.href='{{ route('show-surat-staff-dekan', $surat->id) }}'"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Batal</button>
        </div>

    </form>



</x-layout>
