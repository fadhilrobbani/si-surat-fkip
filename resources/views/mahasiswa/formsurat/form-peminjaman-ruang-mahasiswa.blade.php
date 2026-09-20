@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Pengajuan Peminjaman Ruang Kegiatan
    </x-slot:title>
    {{ Breadcrumbs::render('pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-4">Surat Permohonan Peminjaman Ruang Kegiatan Mahasiswa</p>

    <form action="{{ route('store-surat', $jenisSurat->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('post')

        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Mahasiswa Pengaju</label>
                <input type="text" id="name" name="name" readonly value="{{ $authUser->name }}"
                    class="bg-gray-100 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900">NPM</label>
                <input type="text" id="username" name="username" readonly value="{{ $authUser->username }}"
                    class="bg-gray-100 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nama_organisasi" class="block mb-2 text-sm font-medium text-gray-900">
                    Nama Himpunan / Lembaga / Ormawa <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_organisasi" name="nama_organisasi" required
                    placeholder="Contoh: HIMA Pendidikan Kimia / BEM FKIP / UKM Seni"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="jabatan_pengaju" class="block mb-2 text-sm font-medium text-gray-900">
                    Jabatan Anda di Kepanitiaan / Organisasi <span class="text-red-500">*</span>
                </label>
                <input type="text" id="jabatan_pengaju" name="jabatan_pengaju" required
                    placeholder="Contoh: Ketua Pelaksana / Ketua Umum / Sekretaris Kegiatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nama_ruangan" class="block mb-2 text-sm font-medium text-gray-900">
                    Nama Ruangan / Fasilitas yang Dipinjam <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_ruangan" name="nama_ruangan" required
                    placeholder="Contoh: Aula Bukit Daun / Gedung Serbaguna FKIP"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nama_kegiatan" class="block mb-2 text-sm font-medium text-gray-900">
                    Nama Kegiatan Mahasiswa <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_kegiatan" name="nama_kegiatan" required
                    placeholder="Contoh: Latihan Dasar Kepemimpinan Mahasiswa (LDKM) 2026"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="hari_tanggal" class="block mb-2 text-sm font-medium text-gray-900">
                    Hari / Tanggal Pemakaian <span class="text-red-500">*</span>
                </label>
                <input type="text" id="hari_tanggal" name="hari_tanggal" required
                    placeholder="Contoh: Sabtu, 24 Oktober 2026"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="jam_pemakaian" class="block mb-2 text-sm font-medium text-gray-900">
                    Jam / Waktu Pemakaian <span class="text-red-500">*</span>
                </label>
                <input type="text" id="jam_pemakaian" name="jam_pemakaian" required
                    placeholder="Contoh: 08.00 s.d. 17.00 WIB"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label for="jumlah_peserta" class="block mb-2 text-sm font-medium text-gray-900">
                    Estimasi Jumlah Peserta (Orang)
                </label>
                <input type="number" id="jumlah_peserta" name="jumlah_peserta" placeholder="Contoh: 200"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="berkas_proposal">
                    Upload Pamflet / Proposal Kegiatan Mahasiswa (Opsional)
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
