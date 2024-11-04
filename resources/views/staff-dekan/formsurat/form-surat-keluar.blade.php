@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff Dekan| Pengajuan Surat
    </x-slot:title>
    <x-slot:script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const toggleOptional = document.getElementById('toggle-optional');
                const optionalSection = document.getElementById('optional-section');

                // Menampilkan optional section jika checkbox tercentang secara default
                optionalSection.classList.toggle('hidden', !toggleOptional.checked);

                toggleOptional.addEventListener('change', function() {
                    optionalSection.classList.toggle('hidden', !this.checked);
                });


                const paragrafAwal = document.getElementById("paragraf-awal").value;
                const paragrafAkhir = document.getElementById("paragraf-akhir").value;
                window.setupEditors([{
                        elementId: "wysiwyg-text-example",
                        content: (paragrafAwal ? paragrafAwal :
                            'Dengan hormat,<br>Sehubungan akan dilaksanakan kegiatan Diklat Literasi dan Numerasi bagi Guru Pamong Pendidikan Profesi Guru FKIP Universitas Bengkulu, maka bersama ini kami mohon bantuan Bapak/Ibu mengirimkan narasumber untuk kegiatan tersebut. Kegiatan ini akan dilaksanakan pada:'
                        ),
                    },
                    {
                        elementId: "wysiwyg-text-example2",
                        content: (paragrafAkhir ? paragrafAkhir :
                            "Kami harapkan kepada narasumber untuk membawa Biodata (terlampir) serta membawa Surat Tugas dari instansi Bapak/Ibu. Konfirmasi dan informasi lebih lanjut terkait kegiatan ini dapat menghubungi Saudari Dian Maharani (+62 852-7352-2886).<br><br> Demikianlah atas perhatian dan kerjasama yang baik, kami sampaikan ucapan terima kasih."
                        ),
                    },
                ]);

                const form = document.querySelector('form');
                form.addEventListener('submit', function(event) {
                    // Ambil elemen editor paragraf awal dan akhir
                    const paragrafAwalEl = document.getElementById('wysiwyg-text-example');
                    const paragrafAkhirEl = document.getElementById('wysiwyg-text-example2');

                    // Gabungkan isi dari semua elemen <p> untuk paragraf awal dan akhir
                    const paragrafAwal = Array.from(paragrafAwalEl.querySelectorAll('p')).map(p => p.innerHTML)
                        .join("\n");
                    const paragrafAkhir = Array.from(paragrafAkhirEl.querySelectorAll('p')).map(p => p
                        .innerHTML).join("\n");

                    // Masukkan hasil ke dalam hidden input
                    document.getElementById('paragraf-awal').value = paragrafAwal;
                    document.getElementById('paragraf-akhir').value = paragrafAkhir;
                });

                form.addEventListener('submit', function(event) {
                    const optionalSection = document.getElementById('optional-section');
                    const toggleOptional = document.getElementById('toggle-optional');

                    if (!toggleOptional.checked) {
                        // If the toggle is unchecked, clear the values for all inputs in the optional section
                        const inputs = optionalSection.querySelectorAll('input');
                        inputs.forEach(input => {
                            input.value = ''; // Clear input values
                            input.removeAttribute(
                                'name'); // Remove the name attribute so they are not submitted
                        });
                    }
                });


            });
        </script>
    </x-slot:script>

    {{ Breadcrumbs::render('staff-dekan-pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-2">Surat Keluar</p>
    <form action="{{ route('staff-dekan-store-surat', $jenisSurat->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('post')
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Data Staff Dekan:</p>

        <div class="grid gap-6 mb-6 md:grid-cols-2 bg-slate-100 p-4 rounded-lg">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama<span
                        class="text-red-500">*</span></label>
                <input type="text" id="disabled-input-2" aria-label="disabled input 2" readonly name="name"
                    class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan Nama Lengkap" value="{{ $authUser->name }}" required>
            </div>
            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username<span
                        class="text-red-500">*</span></label>
                <input type="text" id="username" name="username"
                    class=" cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan username Anda" value="{{ $authUser->username }}" readonly required>
            </div>
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email<span
                        class="text-red-500">*</span></label>
                <input type="email" id="email" name="email"
                    class="bg-gray-50 border  border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan email yang aktif" value="{{ $authUser->email }}" required>
            </div>
        </div>

        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Header Surat:</p>
        <div class="grid gap-6 mb-6 md:grid-cols-2 bg-slate-100 p-4 rounded-lg">
            <div>
                <label for="perihal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Perihal
                    Surat<span class="text-red-500">*</span></label>
                <input type="text" name="perihal"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan Perihal / Hal" value="{{ old('perihal') }}" required>
            </div>
            <div>
                <label for="jumlah-lampiran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah
                    Lampiran yang disertakan<span class="text-red-500">*</span></label>
                <input type="number" name="jumlah-lampiran"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan 0 jika tidak ada lampiran" min="0" value="{{ old('jumlah-lampiran') }}"
                    required>
            </div>
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="lampiran">Lampiran
                    (opsional)</label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="lampiran" type="file" name="lampiran"
                    accept=".jpg, .jpeg, .png, .pdf" max-size="10240">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF
                    (MAX. 10 MB).</p>
            </div>
            <div>
                <label for="tujuan1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan
                    Surat<span class="text-red-500">*</span></label>
                <input type="text" name="tujuan1"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: Kepala Kantor Bahasa Provinsi Bengkulu" value="{{ old('tujuan1') }}" required>
            </div>
            <div>
                <label for="tujuan2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan Surat
                    Ke-2 (Opsional)</label>
                <input type="text" name="tujuan2"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="(Opsional / boleh dikosongkan)" value="{{ old('tujuan2') }}">
            </div>
            <div>
                <label for="tujuan3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan Surat
                    Ke-3 (Opsional)</label>
                <input type="text" name="tujuan3"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="(Opsional / boleh dikosongkan)" value="{{ old('tujuan3') }}">
            </div>
        </div>

        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Paragraf Bagian Awal Surat:</p>
        <div class="grid gap-6 mb-6 md:grid-cols-2 bg-slate-100 p-4 rounded-lg ">
            <div class="md:col-span-2">
                <label for="paragraf-awal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paragraf
                    Awal (termasuk salam pembuka)<span class="text-red-500">*</span></label>
                <label for="paragraf-awal"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Silahkan edit contoh
                    paragraf berikut</label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Teks editor ini
                    sensitif terhadap spasi/enter, pastikan tidak ada whitespace (spasi) yang tidak berguna di bagian
                    awal atau akhir paragraf</label>
                <label for="paragraf-awal"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Untuk membuat baris
                    baru, tekan Ctrl + Enter pada keyboard</label>
                <input type="hidden" name="paragraf-awal" id="paragraf-awal" value="{{ old('paragraf-awal') }}"
                    required>
                <x-editor :numberId="''" />
            </div>
        </div>
        <p class="font-semibold text-slate-500 text-md mx-auto ">Detail Info Tanggal, Waktu, Tempat (Opsional):</p>

        <label class="inline-flex items-center cursor-pointer mt-2">
            <input type="checkbox" id="toggle-optional" class="sr-only peer" checked />
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Tampilkan/Sembunyikan Tanggal,
                Waktu, dan Tempat</span>
        </label>
        <br>

        <div id="optional-section" class=" grid gap-6 mb-2 md:grid-cols-1 bg-slate-100 p-4 rounded-lg ">
            <div class="mt-4">
                <label for="tanggal-mulai-kegiatan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari/tanggal Mulai
                </label>
                <input type="date" id="tanggal-mulai-kegiatan" name="tanggal-mulai-kegiatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal kapan kegiatan dimulai" value="{{ old('tanggal-mulai-kegiatan') }}">
            </div>
            <div class="mt-4">
                <label for="tanggal-selesai-kegiatan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari/tanggal Selesai <br>
                    (Kosongkan jika kegiatan hanya 1 hari atau tanggal mulai dan selesai adalah sama )</label>
                <input type="date" id="tanggal-selesai-kegiatan" name="tanggal-selesai-kegiatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal kapan kegiatan selesai" value="{{ old('tanggal-selesai-kegiatan') }}">
            </div>
            <div id="waktu-container">
                <label for="waktu" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu
                </label>
                <input type="text" id="waktu" name="waktu"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh:  15:30 s.d selesai" value="{{ old('waktu') }}">

            </div>

            <div>
                <label for="tempat"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat</label>
                <input type="text" name="tempat" id="tempat"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan tempat pelaksanaan" value="{{ old('tempat') }}">
            </div>
        </div>
        <p class="font-semibold text-slate-500 text-md mx-auto mt-6 mb-2">Paragraf Bagian Akhir Surat:</p>


        <div class="grid gap-6 mb-6 md:grid-cols-2 bg-slate-100 p-4 rounded-lg ">
            <div class="md:col-span-2 mt-4">
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paragraf Bagian Akhir (yang
                    biasanya setelah data informasi surat di atas, termasuk kalimat penutup)<span
                        class="text-red-500">*</span></label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Silahkan edit contoh
                    paragraf berikut</label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Teks editor ini
                    sensitif terhadap spasi/enter, pastikan tidak ada whitespace (spasi) yang tidak berguna di bagian
                    awal atau akhir paragraf</label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Untuk membuat baris
                    baru, tekan Ctrl + Enter pada keyboard</label>
                <x-editor :numberId="'2'" />
                <input type="hidden" name="paragraf-akhir" id="paragraf-akhir" value="{{ old('paragraf-akhir') }}"
                    required>
            </div>
        </div>

        <x-modal-send :daftarPenerima='$daftarPenerima' />
        <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
            class="flex items-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">

            <svg class="w-6 h-6 text-white dark:text-white mr-2" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m12 18-7 3 7-18 7 18-7-3Zm0 0v-5" />
            </svg>

            <p>Ajukan surat</p>
        </button>

    </form>
</x-layout>
