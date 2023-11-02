@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Pengajuan Surat
    </x-slot:title>
    {{-- <x-breadcumb /> --}}
    <p class="font-bold text-lg mx-auto text-center mb-2">Surat Keterangan Lulus</p>
    <form action="{{ route('store-surat-lulus') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('post')
        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama</label>
                <input type="text" id="disabled-input-2" aria-label="disabled input 2" readonly name="name"
                    class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan Nama Lengkap" value="{{ $authUser->name }}" required>
            </div>
            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NPM</label>
                <input type="text" id="username" name="username"
                    class=" cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan NPM Anda" value="{{ $authUser->username }}" readonly required>
            </div>
            <div>
                <label for="program-studi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih
                    Program Studi</label>
                <select id="program-studi" name="program-studi"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    @foreach ($daftarProgramStudi as $programStudi)
                        <option {{ $authUser->program_studi_id == $programStudi->id ? 'selected' : '' }}
                            value="{{ $programStudi->id }}">{{ $programStudi->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                <input type="email" id="email" name="email"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan email yang aktif" value="{{ $authUser->email }}" required>
            </div>
            <div>
                <label for="tempat-lahir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kota Tempat
                    Lahir</label>
                <input type="text" id="tempat-lahir" name="tempat-lahir"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan Kota Tempat lahir" value="{{ old('tempat-lahir') }}" required>
            </div>
            <div>
                <label for="tanggal-lahir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                    Lahir</label>
                <input type="date" id="tanggal-lahir" name="tanggal-lahir"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Tempat lahir" value="{{ old('tanggal-lahir') }}" required>
            </div>
            <div>
                <label for="jenis-ujian" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih
                    Jenis Ujian yang ditempuh</label>
                <select id="jenis-ujian" name="jenis-ujian"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                  <option value="skripsi" selected>Skripsi</option>
                  <option value="tesis">Tesis</option>
                  <option value="disertasi">Disertasi</option>
                </select>
            </div>
            <div>
                <label for="gelar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih
                    Gelar</label>
                <select id="gelar" name="gelar"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                  <option value="sarjana" selected>Sarjana</option>
                  <option value="magister">Magister</option>
                  <option value="doktor">Doktor</option>
                </select>
            </div>
            <div>
                <label for="tanggal-ujian" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                    Ujian</label>
                <input type="date"  id="tanggal-ujian" name="tanggal-ujian"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Tanggal Ujian" value="{{ old('tanggal-ujian') }}" required>
            </div>
            <div>
                <label for="periode-wisuda"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Periode Wisuda</label>
                <input type="number" id="periode-wisuda" name="periode-wisuda"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: 101" value="{{ old('periode-wisuda') }}"
                    required>
            </div>
            <div>
                <label for="tanggal-wisuda" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                    akan wisuda</label>
                <input type="month"  id="tanggal-wisuda" name="tanggal-wisuda"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Tanggal Wisuda" value="{{ old('tanggal-wisuda') }}" required>
            </div>


            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="bukti-lulus">Upload
                    Lampiran Bukti Lulus Ujian (opsional) </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="bukti-lulus" type="file" name="bukti-lulus" accept=".jpg, .jpeg, .png, .pdf" max-size="2048">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF (MAX.
                    2 MB).</p>

            </div>


        </div>

        <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
    </form>



</x-layout>
