@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Pengajuan Surat Peminjaman Ruang
    </x-slot:title>
    {{ Breadcrumbs::render('staff-pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-4">Surat Permohonan Peminjaman Ruang</p>

    <form action="{{ route('staff-store-surat-peminjaman-ruang', $jenisSurat->slug) }}" method="POST"
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
                <label for="nama_ruangan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Ruangan / Fasilitas yang Dipinjam <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_ruangan" name="nama_ruangan" required
                    placeholder="Contoh: Aula Bukit Daun / Ruang Rapat Dekanat Lt. 2 / Ruang Microteaching"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nama_kegiatan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Kegiatan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_kegiatan" name="nama_kegiatan" required
                    placeholder="Contoh: Seminar Nasional Pendidikan Kimia Raflesia 4 (SNPKR)"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="hari_tanggal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Hari / Tanggal Pemakaian <span class="text-red-500">*</span>
                </label>
                <input type="text" id="hari_tanggal" name="hari_tanggal" required
                    placeholder="Contoh: Rabu, 16 September 2026"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="jam_pemakaian" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Jam / Waktu Pemakaian <span class="text-red-500">*</span>
                </label>
                <input type="text" id="jam_pemakaian" name="jam_pemakaian" required
                    placeholder="Contoh: 08.00 s.d. 16.00 WIB"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label for="jumlah_peserta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Estimasi Jumlah Peserta (Orang)
                </label>
                <input type="number" id="jumlah_peserta" name="jumlah_peserta"
                    placeholder="Contoh: 150"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label for="berkas_proposal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Upload Pamflet / Proposal Kegiatan (Opsional, PDF max 10MB)
                </label>
                <input type="file" id="berkas_proposal" name="berkas_proposal" accept=".pdf"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 p-2">
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
