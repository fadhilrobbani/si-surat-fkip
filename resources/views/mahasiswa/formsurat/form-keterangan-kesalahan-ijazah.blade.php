@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Pengajuan Surat
    </x-slot:title>
    {{-- <x-breadcumb /> --}}
    {{ Breadcrumbs::render('pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-2">Surat Keterangan Kesalahan Penulisan di Ijazah</p>
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
                <input type="email" id="email" name="email" readonly
                    class="bg-gray-50 border cursor-not-allowed border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan email yang aktif" value="{{ $authUser->email }}" required>
            </div>
            <div>
                <label for="tempat-lahir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kota
                    Tempat
                    Lahir<span class="text-red-500">*</span></label>
                <input type="text" id="tempat-lahir" name="tempat-lahir"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan Kota Tempat lahir" value="{{ old('tempat-lahir') }}" required>
            </div>
            <div>
                <label for="tanggal-lahir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                    Lahir<span class="text-red-500">*</span></label>
                <input type="date" id="tanggal-lahir" name="tanggal-lahir"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Tempat lahir" value="{{ old('tanggal-lahir') }}" required>
            </div>
            <div>
                <label for="tahun-angkatan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun
                    Angkatan<span class="text-red-500">*</span></label>
                <input type="number" id="tahun-angkatan" name="tahun-angkatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: 2013" value="{{ old('tahun-angkatan') }}" required>
            </div>
            <div>
                <label for="tanggal-lulus" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                    Lulus<span class="text-red-500">*</span></label>
                <input type="date" id="tanggal-lulus" name="tanggal-lulus"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal lulus" value="{{ old('tanggal-lulus') }}" required>
            </div>
            <div>
                <label for="jenis-kesalahan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis
                    Kesalahan<span class="text-red-500">*</span></label>
                <input type="text" id="jenis-kesalahan" name="jenis-kesalahan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: tempat lahir" value="{{ old('jenis-kesalahan') }}" required>
            </div>
            {{-- <div>
                <label for="deskripsi-kesalahan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deskripsi singkat
                    kesalahan (Ikuti format
                    pada placeholder)<span class="text-red-500">*</span></label>
                <input type="text" id="deskripsi-kesalahan" name="deskripsi-kesalahan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pada ijazah tertulis Manna, sedangkan yang seharusnya adalah Bengkulu"
                    value="{{ old('deskripsi-kesalahan') }}" required>
            </div> --}}
            <div>
                <label for="kesalahan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tuliskan apa
                    data/tulisan yang salah<span class="text-red-500">*</span></label>
                <input type="text" id="kesalahan" name="kesalahan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: Bengkulu" value="{{ old('kesalahan') }}" required>
            </div>
            <div>
                <label for="kebenaran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tuliskan apa
                    data/tulisan yang seharusnya (yang benar)<span class="text-red-500">*</span></label>
                <input type="text" id="kebenaran" name="kebenaran"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: Jakarta" value="{{ old('kebenaran') }}" required>
            </div>


            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="ijazah">Upload
                    Lampiran Ijazah Ujian<span class="text-red-500">*</span> </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="ijazah" type="file" name="ijazah"
                    accept=".jpg, .jpeg, .png, .pdf" max-size="2048" required>
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
        {{-- <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button> --}}
    </form>



</x-layout>
