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
                <div class="space-y-2 border p-3 rounded-lg bg-gray-50">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2 items-center">
                            <input type="text" :name="'items[' + index + '][uraian]'" x-model="item.uraian" required
                                placeholder="Uraian (Contoh: Konsumsi Snack, Fotokopi, Spanduk)"
                                class="bg-white border border-gray-300 text-sm rounded-lg block w-2/3 p-2">
                            <input type="number" :name="'items[' + index + '][nominal]'" x-model.number="item.nominal" @input="calculateTotal" required
                                placeholder="Jumlah (Rp)"
                                class="bg-white border border-gray-300 text-sm rounded-lg block w-1/3 p-2 text-right">
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                class="p-2 text-rose-600 hover:text-rose-800 font-bold">&times;</button>
                        </div>
                    </template>
                    <button type="button" @click="addItem"
                        class="text-xs bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-1.5 rounded-md font-medium mt-2">
                        + Tambah Baris Kebutuhan
                    </button>
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
                <label for="no_rekening" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nomor Rekening <span class="text-red-500">*</span>
                </label>
                <input type="text" id="no_rekening" name="no_rekening" required
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
                <label for="berkas_proposal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Upload Berkas Proposal & Rincian Anggaran (RAB) Lengkap <span class="text-red-500">*</span> (PDF max 10MB)
                </label>
                <input type="file" id="berkas_proposal" name="berkas_proposal" accept=".pdf" required
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
