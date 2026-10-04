@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Pengajuan Surat Izin Observasi Mahasiswa
    </x-slot:title>
    {{ Breadcrumbs::render('staff-pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-4">Surat Izin Observasi Mahasiswa</p>

    <form action="{{ route('staff-store-surat-izin-observasi', $jenisSurat->slug) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('post')

        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Pengaju (Staf)</label>
                <input type="text" id="name" name="name" readonly value="{{ $authUser->name }}"
                    class="bg-gray-100 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP / Username</label>
                <input type="text" id="username" name="username" readonly value="{{ $authUser->username }}"
                    class="bg-gray-100 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="mata_kuliah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Mata Kuliah <span class="text-red-500">*</span>
                </label>
                <input type="text" id="mata_kuliah" name="mata_kuliah" required
                    value="{{ old('mata_kuliah') }}"
                    placeholder="Contoh: Inovasi Pembelajaran Biologi (BIO-340)"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="tentang" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Tentang / Topik Observasi <span class="text-red-500">*</span>
                </label>
                <input type="text" id="tentang" name="tentang" required
                    value="{{ old('tentang') }}"
                    placeholder="Contoh: Analisis Kebutuhan dan Kurikulum"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="hari_tanggal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Hari / Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="text" id="hari_tanggal" name="hari_tanggal" required
                    value="{{ old('hari_tanggal') }}"
                    placeholder="Contoh: Rabu/ 26 Februari 2025"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="pukul" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Pukul <span class="text-red-500">*</span>
                </label>
                <input type="text" id="pukul" name="pukul" required
                    value="{{ old('pukul') }}"
                    placeholder="Contoh: 08.00 WIB. s.d selesai"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label for="tempat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Sekolah / Tempat Observasi <span class="text-red-500">*</span>
                </label>
                <input type="text" id="tempat" name="tempat" required
                    value="{{ old('tempat') }}"
                    placeholder="Contoh: SMAN 08 Kota Bengkulu"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                <p class="mt-1 text-xs text-gray-500">Satu surat untuk satu sekolah. Jika observasi ke beberapa sekolah, buat surat terpisah.</p>
            </div>
        </div>

        <p class="font-semibold text-slate-500 text-md mx-auto mb-4">Daftar Mahasiswa yang Mengikuti Observasi:</p>
        <div x-data='{ students: [{ nama: "", npm: "", program_studi: "{{ $authUser->programStudi->name ?? "" }}" }] }' class="mb-6">
            <template x-for="(student, index) in students" :key="index">
                <div class="mb-6 p-4 bg-slate-50 rounded-lg shadow-lg">
                    <p class="font-semibold text-slate-700 text-md mb-3" x-text="'Mahasiswa ' + (index + 1)"></p>
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <label :for="'npm-mahasiswa-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">NPM<span class="text-red-500">*</span></label>
                            <input type="text" :name="'mahasiswa[' + index + '][npm]'" :id="'npm-mahasiswa-' + index"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Contoh: A1D022001" x-model="student.npm" required>
                        </div>
                        <div>
                            <label :for="'nama-mahasiswa-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">Nama<span class="text-red-500">*</span></label>
                            <input type="text" :name="'mahasiswa[' + index + '][nama]'" :id="'nama-mahasiswa-' + index"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Masukkan nama lengkap" x-model="student.nama" required>
                        </div>
                        <div class="md:col-span-2">
                            <label :for="'prodi-mahasiswa-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">Program Studi</label>
                            <input type="text" :name="'mahasiswa[' + index + '][program_studi]'" :id="'prodi-mahasiswa-' + index"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Program Studi" x-model="student.program_studi">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" class="text-red-600 hover:text-red-800"
                            @click="students.splice(index, 1)" x-show="students.length > 1">
                            Hapus Mahasiswa
                        </button>
                    </div>
                </div>
            </template>

            <div class="mt-4">
                <button type="button"
                    @click="students.push({ nama: '', npm: '', program_studi: '{{ $authUser->programStudi->name ?? '' }}' })"
                    class="text-white bg-slate-500 hover:bg-slate-700 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    <span class="flex flex-row items-center justify-center gap-2">
                        <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14m-7 7V5" />
                        </svg>
                        <p>Tambah Mahasiswa</p>
                    </span>
                </button>
            </div>
        </div>

        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="berkas_pendukung">
                    Upload Berkas Pendukung (Opsional)
                </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="berkas_pendukung_help" id="berkas_pendukung" type="file" name="berkas_pendukung"
                    accept=".jpg, .jpeg, .png, .pdf">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="berkas_pendukung_help">PNG, JPG, JPEG, atau PDF (MAX. 10 MB).</p>
            </div>
        </div>

        <x-modal-send :daftarPenerima='$daftarPenerima' />
        <div class="flex justify-end gap-3 mt-4">
            <button type="button" data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                Pilih Penerima & Ajukan
            </button>
        </div>
    </form>
</x-layout>
