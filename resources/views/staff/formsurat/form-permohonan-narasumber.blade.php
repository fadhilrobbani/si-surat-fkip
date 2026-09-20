@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Pengajuan Surat Permohonan Narasumber
    </x-slot:title>
    {{ Breadcrumbs::render('staff-pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-4">Surat Permohonan Menjadi Narasumber</p>

    <form action="{{ route('staff-store-surat-permohonan-narasumber', $jenisSurat->slug) }}" method="POST"
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
                <label for="nama_narasumber" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Lengkap & Gelar Narasumber <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_narasumber" name="nama_narasumber" required
                    value="{{ old('nama_narasumber') }}"
                    placeholder="Contoh: Prof. Dr.rer.nat. Bobby Eka Gunara, M.Si."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="jabatan_narasumber" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Jabatan / Keahlian Narasumber (Opsional)
                </label>
                <input type="text" id="jabatan_narasumber" name="jabatan_narasumber"
                    value="{{ old('jabatan_narasumber') }}"
                    placeholder="Contoh: Guru Besar FMIPA / Dosen Senior"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="instansi_narasumber" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Instansi / Lembaga Narasumber <span class="text-red-500">*</span>
                </label>
                <input type="text" id="instansi_narasumber" name="instansi_narasumber" required
                    value="{{ old('instansi_narasumber') }}"
                    placeholder="Contoh: Institut Teknologi Bandung"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="kota_instansi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Kota / Domisili Instansi Narasumber <span class="text-red-500">*</span>
                </label>
                <input type="text" id="kota_instansi" name="kota_instansi" required
                    value="{{ old('kota_instansi') }}"
                    placeholder="Contoh: Bandung / di Tempat"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nama_kegiatan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Kegiatan / Acara <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_kegiatan" name="nama_kegiatan" required
                    value="{{ old('nama_kegiatan') }}"
                    placeholder="Contoh: Workshop Penulisan Artikel Ilmiah Internasional"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="tempat_kegiatan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Tempat / Ruangan Pelaksanaan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="tempat_kegiatan" name="tempat_kegiatan" required
                    value="{{ old('tempat_kegiatan') }}"
                    placeholder="Contoh: Ruang Rapat Dekanat FKIP / Zoom Meeting"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="hari_tanggal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Hari / Tanggal Pelaksanaan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="hari_tanggal" name="hari_tanggal" required
                    value="{{ old('hari_tanggal') }}"
                    placeholder="Contoh: Rabu dan Kamis, 1-2 Oktober 2025"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="waktu" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Waktu / Pukul Pelaksanaan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="waktu" name="waktu" required
                    value="{{ old('waktu') }}"
                    placeholder="Contoh: 09.00 WIB s.d. 13.00 WIB"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label for="tema_materi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Tema / Materi Narasumber <span class="text-red-500">*</span>
                </label>
                <input type="text" id="tema_materi" name="tema_materi" required
                    value="{{ old('tema_materi') }}"
                    placeholder="Contoh: Penulisan Artikel Ilmiah dan Publikasi di Jurnal Internasional Bereputasi"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="berkas_proposal">
                    Upload Leaflet / Kerangka Acuan (TOR) / Proposal (Opsional)
                </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="berkas_proposal_help" id="berkas_proposal" type="file" name="berkas_proposal"
                    accept=".pdf">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="berkas_proposal_help">PDF (MAX. 10 MB).</p>
            </div>
        </div>

        <input type="hidden" name="penerima" value="{{ $daftarPenerima->first()->id ?? '' }}">

        <div class="flex justify-end gap-3 mt-4">
            <x-modal-send :daftarPenerima='$daftarPenerima' />
            <button type="button" data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                Pilih Penerima & Ajukan
            </button>
        </div>
    </form>
</x-layout>
