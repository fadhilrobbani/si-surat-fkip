@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Pengajuan Surat Permohonan Cuti Akademik Mahasiswa
    </x-slot:title>
    {{ Breadcrumbs::render('staff-pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-4">Surat Permohonan Cuti Akademik Mahasiswa</p>

    <form action="{{ route('staff-store-surat-cuti-mahasiswa', $jenisSurat->slug) }}" method="POST"
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
                <label for="tahun_akademik" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Tahun Akademik <span class="text-red-500">*</span>
                </label>
                <input type="text" id="tahun_akademik" name="tahun_akademik" required
                    value="{{ old('tahun_akademik') }}"
                    placeholder="Contoh: 2026/2027"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="lama_cuti" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Lama Cuti (semester) <span class="text-red-500">*</span>
                </label>
                <input type="number" id="lama_cuti" name="lama_cuti" required min="1"
                    value="{{ old('lama_cuti', 2) }}"
                    placeholder="Contoh: 2"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>
        </div>

        <p class="font-semibold text-slate-500 text-md mx-auto mb-4">Daftar Mahasiswa yang Mengajukan Cuti:</p>
        <div x-data="{ students: [{ nama: '', npm: '', program_studi: '{{ $authUser->programStudi->name ?? '' }}', alasan: '' }] }" class="mb-6">
            <template x-for="(student, index) in students" :key="index">
                <div class="mb-6 p-4 bg-slate-50 rounded-lg shadow-lg">
                    <p class="font-semibold text-slate-700 text-md mb-3" x-text="'Mahasiswa ' + (index + 1)"></p>
                    <div class="grid gap-6 mb-6 md:grid-cols-2">
                        <div>
                            <label :for="'nama-mahasiswa-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">Nama<span class="text-red-500">*</span></label>
                            <input type="text" :name="'mahasiswa[' + index + '][nama]'" :id="'nama-mahasiswa-' + index"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Masukkan nama lengkap" x-model="student.nama" required>
                        </div>
                        <div>
                            <label :for="'npm-mahasiswa-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">NPM<span class="text-red-500">*</span></label>
                            <input type="text" :name="'mahasiswa[' + index + '][npm]'" :id="'npm-mahasiswa-' + index"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Contoh: A1D021019" x-model="student.npm" required>
                        </div>
                        <div>
                            <label :for="'prodi-mahasiswa-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">Program Studi</label>
                            <input type="text" :name="'mahasiswa[' + index + '][program_studi]'" :id="'prodi-mahasiswa-' + index"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Program Studi" x-model="student.program_studi">
                        </div>
                        <div>
                            <label :for="'alasan-mahasiswa-' + index"
                                class="block mb-2 text-sm font-medium text-gray-900">Alasan Cuti<span class="text-red-500">*</span></label>
                            <input type="text" :name="'mahasiswa[' + index + '][alasan]'" :id="'alasan-mahasiswa-' + index"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                placeholder="Contoh: Bekerja" x-model="student.alasan" required>
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
                    @click="students.push({ nama: '', npm: '', program_studi: '{{ $authUser->programStudi->name ?? '' }}', alasan: '' })"
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
