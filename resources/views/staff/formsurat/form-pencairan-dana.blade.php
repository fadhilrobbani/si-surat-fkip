@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Pengajuan Usulan Pencairan Dana
    </x-slot:title>
    {{ Breadcrumbs::render('staff-pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-4">Surat Usulan Pengajuan Dana</p>

    <form action="{{ route('staff-store-surat-pencairan-dana', $jenisSurat->slug) }}" method="POST"
        enctype="multipart/form-data" x-data="danaForm()">
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
                <label for="nama_kegiatan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Kegiatan / Usulan Dana <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_kegiatan" name="nama_kegiatan" required
                    placeholder="Contoh: Visitasi Akreditasi Lamdik Prodi S1 Pendidikan Kimia"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="tahun_anggaran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Tahun Anggaran <span class="text-red-500">*</span>
                </label>
                <input type="number" id="tahun_anggaran" name="tahun_anggaran" required value="{{ date('Y') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Rincian Kebutuhan Anggaran:
                </label>
                <div class="border rounded-lg bg-gray-50 overflow-hidden">
                    <div class="p-3 bg-gray-200/70 border-b flex justify-between items-center text-xs font-bold text-gray-700">
                        <span class="w-8 text-center">No</span>
                        <span class="flex-1 px-2">Uraian / Kebutuhan Anggaran</span>
                        <span class="w-48 text-right px-2">Jumlah Anggaran (Rp)</span>
                        <span class="w-8 text-center">Aksi</span>
                    </div>
                    <div class="p-3 space-y-2">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex gap-2 items-center">
                                <span class="w-8 text-center text-sm font-bold text-gray-500" x-text="index + 1"></span>
                                <input type="text" :name="'items[' + index + '][uraian]'" x-model="item.uraian" required
                                    placeholder="Contoh: Konsumsi Snack / Fotokopi / Spanduk"
                                    class="bg-white border border-gray-300 text-sm rounded-lg block flex-1 p-2 focus:ring-blue-500 focus:border-blue-500">
                                <input type="number" :name="'items[' + index + '][nominal]'" x-model.number="item.nominal" @input="calculateTotal" required
                                    placeholder="Nominal (Rp)" min="0"
                                    class="bg-white border border-gray-300 text-sm rounded-lg block w-48 p-2 text-right font-medium focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                    title="Hapus baris ini"
                                    class="w-8 text-rose-600 hover:text-rose-800 font-bold text-lg text-center leading-none">&times;</button>
                                <div class="w-8" x-show="items.length <= 1"></div>
                            </div>
                        </template>
                    </div>
                    <div class="p-3 bg-white border-t">
                        <button type="button" @click="addItem" class="text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 font-semibold px-3 py-1.5 rounded-lg border border-blue-200 inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            + Tambah Baris Anggaran
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="total_anggaran" class="block mb-2 text-sm font-semibold text-gray-900">
                    Total Anggaran yang Diajukan (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="number" id="total_anggaran" name="total_anggaran" x-model="total" readonly required
                    class="bg-emerald-50 border border-emerald-400 font-extrabold text-emerald-800 text-lg rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nama_bank" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nama Bank Tujuan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_bank" name="nama_bank" required
                    placeholder="Contoh: Bank Bengkulu / BNI / Mandiri"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nomor_rekening" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nomor Rekening <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nomor_rekening" name="nomor_rekening" required
                    value="{{ old('nomor_rekening', old('no_rekening')) }}"
                    placeholder="Masukkan Nomor Rekening"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label for="atas_nama_rekening" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Atas Nama Rekening <span class="text-red-500">*</span>
                </label>
                <input type="text" id="atas_nama_rekening" name="atas_nama_rekening" required
                    placeholder="Nama Pemilik Rekening"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="berkas_proposal">
                    Upload Berkas Proposal & Rincian Anggaran (RAB) Lengkap <span class="text-red-500">*</span>
                </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="berkas_proposal_help" id="berkas_proposal" type="file" name="berkas_proposal"
                    accept=".pdf" required>
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

    <script>
        function danaForm() {
            return {
                items: [
                    { uraian: '', nominal: null }
                ],
                total: 0,
                addItem() {
                    this.items.push({ uraian: '', nominal: null });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    }
                },
                calculateTotal() {
                    this.total = this.items.reduce((acc, curr) => acc + (Number(curr.nominal) || 0), 0);
                }
            }
        }
    </script>
</x-layout>
