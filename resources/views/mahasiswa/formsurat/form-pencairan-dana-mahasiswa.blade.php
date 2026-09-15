@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Pengajuan Pencairan Dana Ormawa/Kegiatan
    </x-slot:title>
    {{ Breadcrumbs::render('pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-4">Surat Usulan Pengajuan / Pencairan Dana Kegiatan Mahasiswa</p>

    <form action="{{ route('store-surat', $jenisSurat->slug) }}" method="POST"
        enctype="multipart/form-data" x-data="danaFormMhs()">
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
                    placeholder="Contoh: HIMA Pendidikan Biologi / BEM FKIP"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="jabatan_pengaju" class="block mb-2 text-sm font-medium text-gray-900">
                    Jabatan Anda di Organisasi/Kepanitiaan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="jabatan_pengaju" name="jabatan_pengaju" required
                    placeholder="Contoh: Ketua Pelaksana / Bendahara Panitia / Ketua Umum"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nama_kegiatan" class="block mb-2 text-sm font-medium text-gray-900">
                    Nama Kegiatan Mahasiswa <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_kegiatan" name="nama_kegiatan" required
                    placeholder="Contoh: Gebyar Seni dan Budaya FKIP 2026"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="tahun_anggaran" class="block mb-2 text-sm font-medium text-gray-900">
                    Tahun Anggaran <span class="text-red-500">*</span>
                </label>
                <input type="number" id="tahun_anggaran" name="tahun_anggaran" required value="{{ date('Y') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900">
                    Rincian Kebutuhan Anggaran:
                </label>
                <div class="space-y-2 border p-3 rounded-lg bg-gray-50">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex gap-2 items-center">
                            <input type="text" :name="'items[' + index + '][uraian]'" x-model="item.uraian" required
                                placeholder="Uraian (Contoh: Konsumsi Acara, Banner/Spanduk, Sertifikat)"
                                class="bg-white border border-gray-300 text-sm rounded-lg block w-2/3 p-2">
                            <input type="number" :name="'items[' + index + '][nominal]'" x-model.number="item.nominal" @input="calculateTotal" required
                                placeholder="Jumlah (Rp)"
                                class="bg-white border border-gray-300 text-sm rounded-lg block w-1/3 p-2 text-right">
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                class="p-2 text-rose-600 hover:text-rose-800 font-bold">&times;</button>
                        </div>
                    </template>
                    <div class="flex justify-between items-center pt-2">
                        <button type="button" @click="addItem" class="text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 font-semibold px-3 py-1.5 rounded-lg border border-blue-200">
                            + Tambah Item Anggaran
                        </button>
                        <div class="text-right font-bold text-gray-700">
                            Total: Rp <span x-text="formatRupiah(total)">0</span>
                            <input type="hidden" name="total_anggaran" :value="total">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="nama_bank" class="block mb-2 text-sm font-medium text-gray-900">
                    Bank Penerima Transfer <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_bank" name="nama_bank" required
                    placeholder="Contoh: Bank Bengkulu / BNI / Mandiri / BRI"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="nomor_rekening" class="block mb-2 text-sm font-medium text-gray-900">
                    Nomor Rekening Penerima <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nomor_rekening" name="nomor_rekening" required
                    placeholder="Nomor rekening tujuan pencairan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="atas_nama_rekening" class="block mb-2 text-sm font-medium text-gray-900">
                    Nama Pemilik Rekening <span class="text-red-500">*</span>
                </label>
                <input type="text" id="atas_nama_rekening" name="atas_nama_rekening" required
                    placeholder="Atas nama buku tabungan / rekening"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>

            <div>
                <label for="lampiran_proposal" class="block mb-2 text-sm font-medium text-gray-900">
                    Unggah Dokumen Proposal / RAB Lengkap (PDF, maks 5MB) <span class="text-red-500">*</span>
                </label>
                <input type="file" id="lampiran_proposal" name="lampiran_proposal" accept=".pdf" required
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-2">
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('mahasiswa-pengajuan-surat') }}"
                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg">
                Batal
            </a>
            <x-modal-send :daftarPenerima='$daftarPenerima' />
            <button type="button" data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                Pilih Penerima & Ajukan
            </button>
        </div>
    </form>

    <script>
        function danaFormMhs() {
            return {
                items: [
                    { uraian: '', nominal: 0 }
                ],
                total: 0,
                addItem() {
                    this.items.push({ uraian: '', nominal: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    }
                },
                calculateTotal() {
                    this.total = this.items.reduce((sum, item) => sum + (Number(item.nominal) || 0), 0);
                },
                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }
            }
        }
    </script>
</x-layout>
