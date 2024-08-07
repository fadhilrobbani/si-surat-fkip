@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Pengajuan Surat
    </x-slot:title>
    {{-- <x-breadcumb /> --}}
    {{ Breadcrumbs::render('staff-pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-2">Surat Tugas (Individu)</p>
    <form action="{{ route('staff-store-surat-tugas', $jenisSurat->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('post')
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Data Staff:</p>

        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama<span
                        class="text-red-500">*</span></label>
                <input type="text" id="disabled-input-2" aria-label="disabled input 2" readonly name="name"
                    class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan Nama Lengkap" value="{{ $authUser->name }}" required>
            </div>
            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username<span
                        class="text-red-500">*</span></label>
                <input type="text" id="username" name="username"
                    class=" cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan username Anda" value="{{ $authUser->username }}" readonly required>
            </div>
            <div>
                <label for="program-studi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih
                    Program Studi<span class="text-red-500">*</span></label>
                <select id="program-studi" disabled
                    class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    @foreach ($daftarProgramStudi as $programStudi)
                        <option {{ $authUser->program_studi_id == $programStudi->id ? 'selected' : '' }}
                            value="{{ $programStudi->id }}">{{ $programStudi->name }}</option>
                    @endforeach
                </select>
                <input class="hidden" name="program-studi" type="text" value="{{ $authUser->programStudi->id }}">
            </div>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email<span
                        class="text-red-500">*</span></label>
                <input type="email" id="email" name="email"
                    class="bg-gray-50 border  border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan email yang aktif" value="{{ $authUser->email }}" required>
            </div>


            {{--
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    for="berita-acara-nilai">Upload
                    Lampiran Berita Acara Nilai<span class="text-red-500">*</span> </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="berita-acara-nilai" type="file" name="berita-acara-nilai"
                    accept=".jpg, .jpeg, .png, .pdf" max-size="10240" required>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF
                    (MAX.
                    10 MB).</p>

            </div> --}}


        </div>
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Data dosen:</p>
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="nama-dosen" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama<span
                        class="text-red-500">*</span></label>
                <input type="text"name="nama-dosen" id="nama-dosen"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan nama lengkap dan gelar" value="{{ old('nama-dosen') }}" required>
            </div>
            <div>
                <label for="nip-dosen" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP<span
                        class="text-red-500">*</span></label>
                <input type="text"name="nip-dosen" id="nip-dosen"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan NIP" value="{{ old('nip-dosen') }}" required>
            </div>
            <div>
                <label for="pangkat-dosen"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pangkat/golongan<span
                        class="text-red-500">*</span></label>
                <input type="text"name="pangkat-dosen" id="pangkat-dosen"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: Penata/IIIc" value="{{ old('pangkat-dosen') }}" required>
            </div>
            <div>
                <label for="jabatan-fungsional-dosen"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jabatan Fungsional<span
                        class="text-red-500">*</span></label>
                <input type="text"name="jabatan-fungsional-dosen" id="jabatan-fungsional-dosen"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: Lektor" value="{{ old('jabatan-fungsional-dosen') }}" required>
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
                    placeholder="Contoh: International Doctoral Supervision Bootcamp" value="{{ old('acara') }}"
                    required>
            </div>
            <div>
                <label for="tempat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat<span
                        class="text-red-500">*</span></label>
                <input type="text"name="tempat" id="tempat"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan tempat pelaksanaan" value="{{ old('tempat') }}" required>
            </div>
            <div>
                <label for="waktu-mulai-penugasan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu Mulai Penugasan<span
                        class="text-red-500">*</span></label>
                <input type="date" id="waktu-mulai-penugasan" name="waktu-mulai-penugasan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih waktu kapan penugasan dimulai" value="{{ old('waktu-mulai-penugasan') }}"
                    required>
            </div>
            <div>
                <label for="waktu-selesai-penugasan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu Selesai Penugasan<span
                        class="text-red-500">*</span></label>
                <input type="date" id="waktu-selesai-penugasan" name="waktu-selesai-penugasan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih waktu kapan penugasan selesai" value="{{ old('waktu-selesai-penugasan') }}"
                    required>
            </div>
            <div>
                <label for="dasar-penugasan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dasar Penugasan<span
                        class="text-red-500">*</span></label>
                <input type="text"name="dasar-penugasan" id="dasar-penugasan"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh:  Surat Koordinator Prodi S2 Pendidikan Bahasa Inggris Nomor 85/UN30.7.7/PP/2024 tanggal 17 April 2024"
                    value="{{ old('dasar-penugasan') }}" required>
            </div>
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="lampiran">Lampiran
                    (opsional) </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="lampiran" type="file" name="lampiran"
                    accept=".jpg, .jpeg, .png, .pdf" max-size="10240">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF
                    (MAX.
                    10 MB).</p>

            </div>
        </div>


        <x-modal-send :daftarPenerima='$daftarPenerima' />
        <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
            class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">
            Ajukan Surat
        </button>
        {{-- <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button> --}}
    </form>



</x-layout>
