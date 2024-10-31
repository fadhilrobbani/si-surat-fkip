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
                const paragrafAwal = document.getElementById("paragraf-awal").value;
                const paragrafAkhir = document.getElementById("paragraf-akhir").value;
                window.setupEditors([{
                        elementId: "wysiwyg-text-example",
                        content: (paragrafAwal ? paragrafAwal :
                            'Sehubungan akan dilaksanakan kegiatan Diklat Literasi dan Numerasi bagi Guru Pamong Pendidikan Profesi Guru FKIP Universitas Bengkulu, maka bersama ini kami mohon bantuan Bapak/Ibu mengirimkan narasumber untuk kegiatan tersebut. Kegiatan ini akan dilaksanakan pada:'
                        ),
                    },
                    {
                        elementId: "wysiwyg-text-example2",
                        content: (paragrafAkhir ? paragrafAkhir :
                            "Kami harapkan kepada narasumber untuk membawa Biodata (terlampir) serta membawa Surat Tugas dari instansi Bapak/Ibu. Konfirmasi dan informasi lebih lanjut terkait kegiatan ini dapat menghubungi Saudari Dian Maharani (+62 852-7352-2886).<br><br> Demikianlah atas perhatian dan kerjasama yang baik, kami sampaikan ucapan terima kasih."
                        ),
                    },
                ]);

                // const form = document.querySelector('form');
                // form.addEventListener('submit', function(event) {
                //     const paragrafAwal = document.getElementById('wysiwyg-text-example')
                //         .innerHTML; // Ambil isi editor paragraf awal
                //     const paragrafAkhir = document.getElementById('wysiwyg-text-example2')
                //         .innerHTML; // Ambil isi editor paragraf akhir
                //     document.getElementById('paragraf-awal').value =
                //         paragrafAwal; // Masukkan nilai ke hidden input
                //     document.getElementById('paragraf-akhir').value =
                //         paragrafAkhir; // Masukkan nilai ke hidden input
                // });

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


            });

            function toggleWaktuInput() {
                const checkbox = document.getElementById('jadwal-terlampir-checkbox');
                const timeInput = document.getElementById('waktu');
                const hiddenInput = document.getElementById('hidden-waktu');

                if (checkbox.checked) {
                    timeInput.classList.add('hidden'); // Sembunyikan input waktu dengan menambahkan kelas 'hidden'
                    hiddenInput.value = "Jadwal terlampir"; // Set nilai tersembunyi menjadi "Jadwal terlampir"
                } else {
                    timeInput.classList.remove('hidden'); // Tampilkan kembali input waktu dengan menghapus kelas 'hidden'
                    hiddenInput.value = timeInput
                        .value; // Set nilai tersembunyi menjadi nilai waktu jika checkbox tidak dicentang
                }
            }
        </script>
    </x-slot:script>
    {{-- <x-breadcumb /> --}}
    {{ Breadcrumbs::render('staff-dekan-pengajuan-surat-form', $jenisSurat) }}
    <p class="font-bold text-lg mx-auto text-center mb-2">Surat Keluar</p>
    <form action="{{ route('staff-dekan-store-surat', $jenisSurat->slug) }}" method="POST" enctype="multipart/form-data">
        {{-- <form action="" method="POST" enctype="multipart/form-data"> --}}
        @csrf @method('post')
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Data
            Staff Dekan:</p>

        <div class="grid gap-6 mb-6 md:grid-cols-2">
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


            {{--
            <div>

                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    for="berita-acara-nilai">Upload
                    Lampiran Berita Acara Nilai<span class="text-red-500">*</span> </label>
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="berita-acara-nilai" type="file" name="berita-acara-nilai"
                    accept=".jpg, .jpeg, .png, .pdf" max-size="10240" required>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau PDF
                    (MAX.
                    10 MB).</p>

            </div> --}}


        </div>


        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Header Surat:</p>
        <div class="grid gap-6 mb-6 md:grid-cols-2">
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
                    (opsional) </label>
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
                <label for="tujuan3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan
                    Surat Ke-3 (Opsional)</label>
                <input type="text" name="tujuan3"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="(Opsional / boleh dikosongkan)" value="{{ old('tujuan3') }}">
            </div>
        </div>
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Badan Surat:</p>
        <div class="grid gap-6 mb-6 md:grid-cols-2 ">
            <div class="md:col-span-2">
                <label for="paragraf-awal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paragraf
                    Awal (yang biasanya setelah salam)<span class="text-red-500">*</span></label>
                <label for="paragraf-awal"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Silahkan edit contoh
                    paragraf berikut</label>
                <label for="paragraf-awal"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Untuk
                    membuat baris baru, tekan Ctrl + Enter pada keyboard</label>
                <input type="hidden" name="paragraf-awal" id="paragraf-awal" value="{{ old('paragraf-awal') }}"
                    required>
                <x-editor :numberId="''" />
                {{-- <button type="button"
                    onclick="window.alert(document.getElementById('wysiwyg-text-example').innerHTML)">Click
                    to Get
                    HTML</button> --}}
            </div>

            <div class="mt-4">
                <label for="tanggal-mulai-kegiatan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari/tanggal
                    Mulai Kegiatan<span class="text-red-500">*</span></label>
                <input type="date" id="tanggal-mulai-kegiatan" name="tanggal-mulai-kegiatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal kapan kegiatan dimulai" value="{{ old('tanggal-mulai-kegiatan') }}"
                    required>
            </div>
            <div class="mt-4">
                <label for="tanggal-selesai-kegiatan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari/tanggal Selesai
                    Kegiatan<span class="text-red-500">*</span></label>
                <input type="date" id="tanggal-selesai-kegiatan"" name="tanggal-selesai-kegiatan""
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal kapan kegiatan selesai" value="{{ old('tanggal-selesai-kegiatan"') }}"
                    required>
            </div>

            <div class="">
                <label for="waktu" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu
                    Kegiatan<span class="text-red-500">*</span></label>
                <input type="time" id="waktu" name="waktu"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih waktu kapan kegiatan dilaksanakan" value="{{ old('waktu') }}" required>
            </div>


            <div>
                <label for="tempat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat<span
                        class="text-red-500">*</span></label>
                <input type="text"name="tempat" id="tempat"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan tempat pelaksanaan" value="{{ old('tempat') }}" required>
            </div>

            <div class="flex items-center mt-[-16px]">
                <input type="checkbox" id="jadwal-terlampir-checkbox" class="mr-2" onchange="toggleWaktuInput()">
                <label for="jadwal-terlampir-checkbox"
                    class="text-sm font-medium text-gray-500 dark:text-white">Jadwal ada di lampiran (Spesifik waktu
                    tidak akan ditulis di surat)</label>
            </div>

            <input type="hidden" name="waktu" id="hidden-waktu" value="Jadwal terlampir">




            <div class="md:col-span-2 mt-4">
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paragraf
                    Awal (yang biasanya setelah salam)<span class="text-red-500">*</span></label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Silahkan edit contoh
                    paragraf berikut</label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Untuk
                    membuat baris baru, tekan Ctrl + Enter pada keyboard</label>
                <x-editor :numberId="'2'" />
                <input type="hidden" name="paragraf-akhir" id="paragraf-akhir" value="{{ old('paragraf-akhir') }}"
                    required>
                {{-- <button type="button"
                    onclick="alert(document.getElementById('wysiwyg-text-example2').innerHTML)">Click
                    to Get
                    HTML</button> --}}
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
