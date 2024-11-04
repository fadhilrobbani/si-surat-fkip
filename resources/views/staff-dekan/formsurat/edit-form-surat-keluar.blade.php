@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff Dekan| Edit Surat
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
    {{-- <x-breadcumb /> --}}
    {{-- {{ Breadcrumbs::render('staff-dekan-pengajuan-surat-form', $jenisSurat) }} --}}
    <p class="font-bold text-lg mx-auto text-center mb-2">Edit Surat Keluar</p>
    <form action="{{ route('update-surat-staff-dekan', $surat->id) }}" method="POST" enctype="multipart/form-data">
        {{-- <form action="" method="POST" enctype="multipart/form-data"> --}}
        @csrf @method('put')



        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Header Surat:</p>
        <div class="grid gap-6 mb-6 md:grid-cols-2 bg-slate-100 p-4 rounded-lg">
            <div>
                <label for="perihal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Perihal
                    Surat<span class="text-red-500">*</span></label>
                <input type="text" name="perihal"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan Perihal / Hal" value="{{ $surat->data['perihal'] }}" required>
            </div>
            <div>
                <label for="jumlah-lampiran" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah
                    Lampiran yang disertakan<span class="text-red-500">*</span></label>
                <input type="number" name="jumlah-lampiran"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan 0 jika tidak ada lampiran" min="0"
                    value="{{ $surat->data['jumlahLampiran'] }}" required>
            </div>

            <div>
                <label for="tujuan1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan
                    Surat<span class="text-red-500">*</span></label>
                <input type="text" name="tujuan1"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Contoh: Kepala Kantor Bahasa Provinsi Bengkulu" value="{{ $surat->data['tujuan1'] }}"
                    required>
            </div>
            <div>
                <label for="tujuan2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan Surat
                    Ke-2 (Opsional)</label>
                <input type="text" name="tujuan2"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="(Opsional / boleh dikosongkan)" value="{{ $surat->data['tujuan2'] }}">
            </div>
            <div>
                <label for="tujuan3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan
                    Surat Ke-3 (Opsional)</label>
                <input type="text" name="tujuan3"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="(Opsional / boleh dikosongkan)" value="{{ $surat->data['tujuan3'] }}">
            </div>
        </div>
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Paragraf Bagian Awal Surat:</p>

        <div class="grid gap-6 mb-2 md:grid-cols-2 bg-slate-100 p-4 rounded-lg ">
            <div class="md:col-span-2">
                <label for="paragraf-awal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paragraf
                    Awal (termasuk salam)<span class="text-red-500">*</span></label>
                <label for="paragraf-awal"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Silahkan edit contoh
                    paragraf berikut</label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Teks editor ini
                    sensitif terhadap spasi/enter, pastikan tidak ada whitespace (spasi) yang tidak berguna di bagian
                    awal atau akhir paragraf</label>
                <label for="paragraf-awal"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Untuk
                    membuat baris baru, tekan Ctrl + Enter pada keyboard</label>
                <input type="hidden" name="paragraf-awal" id="paragraf-awal" value="{{ $surat->data['paragrafAwal'] }}"
                    required>
                <x-editor :numberId="''" />
                {{-- <button type="button"
                    onclick="window.alert(document.getElementById('wysiwyg-text-example').innerHTML)">Click
                    to Get
                    HTML</button> --}}
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
        <div id="optional-section" class=" grid gap-6 mb-2 mt-4 md:grid-cols-1 bg-slate-100 p-4 rounded-lg ">
            <div class="mt-4">
                <label for="tanggal-mulai-kegiatan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari/tanggal Mulai
                </label>
                <input type="date" id="tanggal-mulai-kegiatan" name="tanggal-mulai-kegiatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal kapan kegiatan dimulai"
                    value="{{ $surat->data['private']['tanggalMulaiKegiatan'] }}">
            </div>
            <div class="mt-4">
                <label for="tanggal-selesai-kegiatan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari/tanggal Selesai
                    (Kosongkan jika kegiatan hanya 1 hari atau tanggal mulai dan selesai adalah sama )</label>
                <input type="date" id="tanggal-selesai-kegiatan" name="tanggal-selesai-kegiatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal kapan kegiatan selesai"
                    value="{{ $surat->data['private']['tanggalSelesaiKegiatan'] }}">
            </div>
            <div>
                <label for="waktu" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu
                </label>
                <input type="text" id="waktu" name="waktu"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih waktu kapan kegiatan dilaksanakan" value="{{ $surat->data['waktu'] }}">

            </div>

            <div>
                <label for="tempat"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat</label>
                <input type="text" name="tempat" id="tempat"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan tempat pelaksanaan" value="{{ $surat->data['tempat'] }}">
            </div>
        </div>

        <p class="font-semibold text-slate-500 text-md mx-auto mt-6 mb-2">Paragraf Bagian Akhir Surat:</p>

        <div class="grid gap-6 mb-6 md:grid-cols-2 bg-slate-100 p-4 rounded-lg">


            <div class="md:col-span-2 mt-4">
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paragraf
                    Bagian Akhir (yang biasanya setelah data informasi surat di atas, termasuk kalimat penutup)<span
                        class="text-red-500">*</span></label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Silahkan edit contoh
                    paragraf berikut</label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Teks editor ini
                    sensitif terhadap spasi/enter, pastikan tidak ada whitespace (spasi) yang tidak berguna di bagian
                    awal atau akhir paragraf</label>
                <label for="paragraf-akhir"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Untuk
                    membuat baris baru, tekan Ctrl + Enter pada keyboard</label>
                <x-editor :numberId="'2'" />
                <input type="hidden" name="paragraf-akhir" id="paragraf-akhir"
                    value="{{ $surat->data['paragrafAkhir'] }}" required>
                {{-- <button type="button"
                    onclick="alert(document.getElementById('wysiwyg-text-example2').innerHTML)">Click
                    to Get
                    HTML</button> --}}
            </div>

        </div>



        <div class="flex justify-center gap-4">
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan</button>
            <button type="button" onclick="window.location.href='{{ route('show-surat-staff-dekan', $surat->id) }}'"
                class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Batal</button>
        </div>
        {{-- <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button> --}}
    </form>



</x-layout>
