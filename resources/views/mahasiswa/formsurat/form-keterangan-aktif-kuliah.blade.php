@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Pengajuan Surat
    </x-slot:title>
    {{-- <x-breadcumb /> --}}
    <p class="font-bold text-lg mx-auto text-center mb-2">Surat Keterangan Masih Kuliah</p>

    <form action="{{ route('store-surat', $jenisSurat->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('post')
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama<span
                        class="text-red-500">*</span></label>
                <input type="text" id="disabled-input-2" aria-label="disabled input 2" readonly name="name"
                    class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan Nama Lengkap" value="{{ $authUser->name }}" required>
            </div>
            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NPM<span
                        class="text-red-500">*</span></label>
                <input type="text" id="username" name="username"
                    class=" cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan NPM Anda" value="{{ $authUser->username }}" readonly required>
            </div>
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email<span
                        class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" readonly
                    class="bg-gray-50 border cursor-not-allowed border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan email yang aktif" value="{{ $authUser->email }}" required>
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
                <label for="semester" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih
                    Semester Masuk Kuliah<span class="text-red-500">*</span></label>
                <select id="semester" name="semester" required
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="" selected>Pilih Semester Anda Kuliah Sekarang</option>
                    <option value="1">1 (Satu)</option>
                    <option value="2">2 (Dua)</option>
                    <option value="3">3 (Tiga)</option>
                    <option value="4">4 (Empat)</option>
                    <option value="5">5 (Lima)</option>
                    <option value="6">6 (Enam)</option>
                    <option value="7">7 (Tujuh)</option>
                    <option value="8">8 (Delapan)</option>
                    <option value="9">9 (Sembilan)</option>
                    <option value="10">10 (Sepuluh)</option>
                    <option value="11">11 (Sebelas)</option>
                    <option value="12">12 (Dua Belas)</option>
                    <option value="13">13 (Tiga Belas)</option>
                    <option value="14">14 (Empat Belas)</option>
                </select>

            </div>

            <div>
                <label for="tahunAkademik" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun
                    Ajaran Akademik Sekarang<span class="text-red-500">*</span></label>
                <input type="text" id="tahunAkademik" name="tahunAkademik"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: 2019/2020" value="{{ old('tahunAkademik') }}" required>
            </div>
            <div>
                <label for="nama-orang-tua" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                    Orang Tua / Wali<span class="text-red-500">*</span></label>
                <input type="text" id="nama-orang-tua" name="nama-orang-tua"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Nama Lengkap Orang Tua / Wali" value="{{ old('nama-orang-tua') }}" required>
            </div>
            <div>
                <label for="pekerjaan-orang-tua"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Instansi/Pekerjaan
                    Orang Tua / Wali<span class="text-red-500">*</span></label>
                <input type="text" id="pekerjaan-orang-tua" name="pekerjaan-orang-tua"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pekerjaan Orang Tua / Wali" value="{{ old('pekerjaan-orang-tua') }}" required>
            </div>
            <div>
                <label for="pangkat-orang-tua"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pangkat/Golongan Pekerjaan
                    Orang Tua / Wali (Jika Ada)
                    <input type="text" id="pangkat-orang-tua" name="pangkat-orang-tua"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Contoh: Project Manager atau Pembina/Golongan IV"
                        value="{{ old('pangkat-orang-tua') }}">
            </div>
            <div>
                <label for="nip-orang-tua" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP
                    Orang Tua (Jika Ada)
                    <input type="text" id="nip-orang-tua" name="nip-orang-tua"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="NIP Orang Tua / Wali" value="{{ old('nip-orang-tua') }}">
            </div>


            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                    Slip Pembayaran UKT Terakhir<span class="text-red-500">*</span>
                </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="file_input" type="file" required
                    name="slip-pembayaran" accept=".jpg, .jpeg, .png, .pdf" max-size="2048">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF
                    (MAX.
                    2 MB).</p>

            </div>
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload SK
                    Terakhir </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="file_input" type="file" name="sk-orang-tua"
                    accept=".jpg, .jpeg, .png, .pdf" max-size="2048">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF
                    (MAX.
                    2 MB).</p>

            </div>
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                    KTM<span class="text-red-500">*</span>
                </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="file_input" type="file" name="ktm" required
                    accept=".jpg, .jpeg, .png, .pdf" max-size="2048">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF
                    (MAX.
                    2 MB).</p>

            </div>
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload
                    KRS Terakhir<span class="text-red-500">*</span> </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="file_input" type="file" name="krs" required
                    accept=".jpg, .jpeg, .png, .pdf" max-size="2048">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF
                    (MAX.
                    2 MB).</p>

            </div>



        </div>

        <x-modal-send :daftarPenerima='$daftarPenerima' />
        <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
            class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">
            Ajukan Surat
        </button>

    </form>



</x-layout>
