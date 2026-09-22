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

            <div class="md:col-span-2">
                <label for="no_surat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nomor Surat <span class="text-xs font-normal text-gray-500">(Opsional - kosongkan jika belum ada nomor surat)</span>
                </label>
                <input type="text" id="no_surat" name="no_surat" value="{{ old('no_surat') }}"
                    placeholder="Contoh: 015/DST/UN30.7.11/KU.01.02/{{ date('Y') }}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <div class="mt-1.5 flex items-center gap-2">
                    <button type="button"
                        onclick="fillNoSuratDana('/DST/UN30.7.11/KU.01.02/{{ date('Y') }}')"
                        class="text-xs inline-flex items-center gap-1 font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded border border-blue-200 transition">
                        📋 Gunakan Format: /DST/UN30.7.11/KU.01.02/{{ date('Y') }}
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Kosongkan jika nomor belum terbit (akan dicetak titik-titik pada surat). Jika diisi, wajib sertakan format lengkap.</p>
                @error('no_surat')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
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
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white">
                        Rincian Komponen / Kegiatan Anggaran <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs text-gray-500">Mendukung pengajuan flat (nominal langsung) maupun kegiatan ber-subkegiatan & MAK</span>
                </div>

                <div class="space-y-4">
                    <template x-for="(kegiatan, kIndex) in kegiatans" :key="kIndex">
                        <div class="border rounded-xl bg-white shadow-sm border-gray-200 overflow-hidden">
                            <!-- Header Bar Kegiatan -->
                            <div class="p-3 bg-slate-100 border-b border-gray-200 flex flex-col md:flex-row gap-2.5 items-stretch md:items-center justify-between">
                                <div class="flex items-center gap-2 flex-1">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center" x-text="kIndex + 1"></span>
                                    <input type="text" :name="'kegiatans[' + kIndex + '][nama]'" x-model="kegiatan.nama" required
                                        placeholder="Nama Kegiatan / Komponen Belanja (contoh: Belanja ATK / Pelaksanaan Workshop)"
                                        class="bg-white border border-gray-300 text-sm font-semibold rounded-lg block flex-1 p-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="flex items-center gap-2 justify-end">
                                    <div class="w-36">
                                        <input type="text" :name="'kegiatans[' + kIndex + '][mak]'" x-model="kegiatan.mak"
                                            placeholder="MAK (opsional)" title="Mata Anggaran Kegiatan / Kode Akun (opsional)"
                                            class="bg-white border border-gray-300 text-xs rounded-lg block w-full p-2 text-center">
                                    </div>

                                    <!-- Switch Mode -->
                                    <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden p-0.5 bg-gray-200">
                                        <button type="button" @click="setMode(kegiatan, 'flat')"
                                            :class="kegiatan.mode === 'flat' ? 'bg-white text-blue-700 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                            class="px-2.5 py-1 text-xs rounded-md transition">
                                            Nominal Langsung
                                        </button>
                                        <button type="button" @click="setMode(kegiatan, 'rincian')"
                                            :class="kegiatan.mode === 'rincian' ? 'bg-white text-blue-700 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                                            class="px-2.5 py-1 text-xs rounded-md transition">
                                            + Subkegiatan
                                        </button>
                                    </div>

                                    <button type="button" @click="removeKegiatan(kIndex)" x-show="kegiatans.length > 1"
                                        title="Hapus kegiatan ini"
                                        class="text-rose-600 hover:text-rose-800 p-1 font-bold text-lg leading-none">&times;</button>
                                </div>
                            </div>

                            <input type="hidden" :name="'kegiatans[' + kIndex + '][mode]'" :value="kegiatan.mode">

                            <!-- Mode Flat: Input Nominal Langsung -->
                            <div class="p-3.5" x-show="kegiatan.mode === 'flat'">
                                <div class="flex flex-col sm:flex-row gap-3 items-center justify-between">
                                    <span class="text-xs text-gray-500">Nominal langsung untuk kegiatan ini (tanpa rincian subkegiatan):</span>
                                    <div class="flex items-center gap-2 w-full sm:w-64">
                                        <span class="text-sm font-bold text-gray-500">Rp</span>
                                        <input type="number" :name="'kegiatans[' + kIndex + '][nominal]'" x-model.number="kegiatan.nominal" @input="calculateTotal"
                                            :required="kegiatan.mode === 'flat'" min="0" placeholder="Nominal (Rp)"
                                            class="bg-white border border-gray-300 text-sm font-bold text-right rounded-lg block w-full p-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Mode Rincian: Subkegiatan -->
                            <div class="p-3 bg-slate-50/50" x-show="kegiatan.mode === 'rincian'">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="text-gray-600 border-b border-gray-200">
                                                <th class="py-1 px-2 w-10 text-center">No</th>
                                                <th class="py-1 px-2">Uraian Barang / Rincian Belanja</th>
                                                <th class="py-1 px-2 w-20 text-center">Volume</th>
                                                <th class="py-1 px-2 w-24 text-center">Satuan</th>
                                                <th class="py-1 px-2 w-32 text-right">Harga Satuan (Rp)</th>
                                                <th class="py-1 px-2 w-32 text-right">Subtotal (Rp)</th>
                                                <th class="py-1 px-2 w-10 text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(sub, sIndex) in kegiatan.sub_items" :key="sIndex">
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-1.5 px-2 text-center text-gray-400 font-bold" x-text="(kIndex + 1) + '.' + (sIndex + 1)"></td>
                                                    <td class="py-1.5 px-2">
                                                        <input type="text" :name="'kegiatans[' + kIndex + '][sub_items][' + sIndex + '][uraian]'" x-model="sub.uraian"
                                                            :required="kegiatan.mode === 'rincian'"
                                                            placeholder="Contoh: Honor Narasumber / Snack Box / Kertas HVS"
                                                            class="w-full bg-white border border-gray-300 rounded p-1.5 text-xs focus:ring-blue-500 focus:border-blue-500">
                                                    </td>
                                                    <td class="py-1.5 px-2">
                                                        <input type="number" :name="'kegiatans[' + kIndex + '][sub_items][' + sIndex + '][volume]'" x-model.number="sub.volume"
                                                            @input="updateSubTotal(kegiatan, sub); calculateTotal()"
                                                            :required="kegiatan.mode === 'rincian'" min="1"
                                                            class="w-full bg-white border border-gray-300 rounded p-1.5 text-xs text-center">
                                                    </td>
                                                    <td class="py-1.5 px-2">
                                                        <input type="text" :name="'kegiatans[' + kIndex + '][sub_items][' + sIndex + '][satuan]'" x-model="sub.satuan"
                                                            placeholder="Satuan (Pkt/Kotak/Rim)"
                                                            class="w-full bg-white border border-gray-300 rounded p-1.5 text-xs text-center">
                                                    </td>
                                                    <td class="py-1.5 px-2">
                                                        <input type="number" :name="'kegiatans[' + kIndex + '][sub_items][' + sIndex + '][harga_satuan]'" x-model.number="sub.harga_satuan"
                                                            @input="updateSubTotal(kegiatan, sub); calculateTotal()"
                                                            :required="kegiatan.mode === 'rincian'" min="0" placeholder="0"
                                                            class="w-full bg-white border border-gray-300 rounded p-1.5 text-xs text-right font-medium">
                                                    </td>
                                                    <td class="py-1.5 px-2 text-right font-bold text-gray-800" x-text="formatRupiah(sub.total)"></td>
                                                    <td class="py-1.5 px-2 text-center">
                                                        <button type="button" @click="removeSubItem(kegiatan, sIndex)" x-show="kegiatan.sub_items.length > 1"
                                                            title="Hapus baris rincian ini"
                                                            class="text-rose-500 hover:text-rose-700 font-bold text-sm">&times;</button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2.5 flex items-center justify-between">
                                    <button type="button" @click="addSubItem(kegiatan)"
                                        class="text-xs bg-white text-blue-600 hover:bg-blue-50 font-semibold px-2.5 py-1 rounded border border-blue-200 inline-flex items-center gap-1">
                                        + Tambah Baris Rincian
                                    </button>
                                    <div class="text-xs font-semibold text-gray-700">
                                        Subtotal Kegiatan: <span class="text-blue-700 font-bold" x-text="formatRupiah(kegiatan.subtotal)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="pt-1">
                        <button type="button" @click="addKegiatan"
                            class="text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold px-4 py-2 rounded-lg border border-blue-300 inline-flex items-center gap-1.5 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            + Tambah Komponen / Kegiatan Anggaran
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
                kegiatans: [
                    {
                        nama: '',
                        mak: '',
                        mode: 'flat',
                        nominal: null,
                        sub_items: [
                            { uraian: '', volume: 1, satuan: 'Paket', harga_satuan: 0, total: 0 }
                        ],
                        subtotal: 0
                    }
                ],
                total: 0,
                setMode(kegiatan, mode) {
                    kegiatan.mode = mode;
                    this.calculateTotal();
                },
                addKegiatan() {
                    this.kegiatans.push({
                        nama: '',
                        mak: '',
                        mode: 'flat',
                        nominal: null,
                        sub_items: [
                            { uraian: '', volume: 1, satuan: 'Paket', harga_satuan: 0, total: 0 }
                        ],
                        subtotal: 0
                    });
                },
                removeKegiatan(index) {
                    if (this.kegiatans.length > 1) {
                        this.kegiatans.splice(index, 1);
                        this.calculateTotal();
                    }
                },
                addSubItem(kegiatan) {
                    kegiatan.sub_items.push({ uraian: '', volume: 1, satuan: '', harga_satuan: 0, total: 0 });
                },
                removeSubItem(kegiatan, sIndex) {
                    if (kegiatan.sub_items.length > 1) {
                        kegiatan.sub_items.splice(sIndex, 1);
                        this.updateKegiatanSubtotal(kegiatan);
                        this.calculateTotal();
                    }
                },
                updateSubTotal(kegiatan, sub) {
                    const vol = Number(sub.volume) || 0;
                    const harga = Number(sub.harga_satuan) || 0;
                    sub.total = vol * harga;
                    this.updateKegiatanSubtotal(kegiatan);
                },
                updateKegiatanSubtotal(kegiatan) {
                    kegiatan.subtotal = kegiatan.sub_items.reduce((acc, curr) => acc + (Number(curr.total) || 0), 0);
                },
                calculateTotal() {
                    let sum = 0;
                    this.kegiatans.forEach(k => {
                        if (k.mode === 'flat') {
                            sum += (Number(k.nominal) || 0);
                        } else {
                            k.subtotal = k.sub_items.reduce((acc, curr) => acc + (Number(curr.total) || 0), 0);
                            sum += k.subtotal;
                        }
                    });
                    this.total = sum;
                },
                formatRupiah(amount) {
                    return 'Rp ' + (Number(amount) || 0).toLocaleString('id-ID');
                }
            }
        }

        function fillNoSuratDana(format) {
            const input = document.getElementById('no_surat');
            if (!input) return;
            const currentVal = input.value.trim();
            if (!currentVal) {
                input.value = format;
                input.focus();
                input.setSelectionRange(0, 0);
            } else if (!currentVal.includes('/')) {
                input.value = currentVal + format;
                input.focus();
            }
        }
    </script>
</x-layout>
