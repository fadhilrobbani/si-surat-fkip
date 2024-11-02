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

                const form = document.querySelector('form');
                form.addEventListener('submit', function(event) {
                    const paragrafAwalEl = document.getElementById('wysiwyg-text-example');
                    const paragrafAkhirEl = document.getElementById('wysiwyg-text-example2');

                    const paragrafAwal = Array.from(paragrafAwalEl.querySelectorAll('p')).map(p => p.innerHTML)
                        .join("\n");
                    const paragrafAkhir = Array.from(paragrafAkhirEl.querySelectorAll('p')).map(p => p
                        .innerHTML).join("\n");

                    document.getElementById('paragraf-awal').value = paragrafAwal;
                    document.getElementById('paragraf-akhir').value = paragrafAkhir;
                });

                function toggleWaktuInput() {
                    const checkbox = document.getElementById('jadwal-terlampir-checkbox');
                    const timeInput = document.getElementById('waktu');
                    const endTimeInput = document.getElementById('waktu-selesai');
                    const hiddenInput = document.getElementById('hidden-waktu');

                    if (checkbox.checked) {
                        timeInput.classList.add('hidden');
                        endTimeInput.classList.add('hidden'); // Sembunyikan input waktu
                        hiddenInput.value = "Jadwal terlampir";
                    } else {
                        timeInput.classList.remove('hidden');
                        endTimeInput.classList.remove('hidden'); // Tampilkan kembali input waktu
                        hiddenInput.value = timeInput.value; // Perbarui nilai hidden input
                    }
                }

                // Add event listener for checkbox to toggle inputs
                document.getElementById('jadwal-terlampir-checkbox').addEventListener('change', toggleWaktuInput);

                // Check the initial value of 'waktu' and hide inputs if necessary
                const waktuInput = document.getElementById('waktu').value;
                const hiddenWaktuInput = document.getElementById('hidden-waktu').value;
                console.log("ini waktu input " + hiddenWaktuInput);
                // Pastikan keduanya tersembunyi saat halaman mulai dimuat
                if (hiddenWaktuInput === "Jadwal terlampir") {
                    const checkbox = document.getElementById('jadwal-terlampir-checkbox');
                    checkbox.checked = true;
                    toggleWaktuInput(); // Panggil fungsi untuk menyembunyikan input pada saat muat
                }

                // Periksa perubahan dari input waktu
                document.getElementById('waktu').addEventListener('change', function() {
                    const checkbox = document.getElementById('jadwal-terlampir-checkbox');
                    const hiddenInput = document.getElementById('hidden-waktu');

                    if (!checkbox.checked) {
                        hiddenInput.value = this.value; // Perbarui hidden input jika checkbox tidak dicentang
                    }
                });

                // Panggil fungsi untuk menginisialisasi kondisi awal
                toggleWaktuInput(); // Panggil ini untuk memastikan keadaan yang benar saat halaman dimuat
            });
        </script>

    </x-slot:script>
    {{-- <x-breadcumb /> --}}
    {{-- {{ Breadcrumbs::render('staff-dekan-pengajuan-surat-form', $jenisSurat) }} --}}
    <p class="font-bold text-lg mx-auto text-center mb-2">Surat Keluar</p>
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
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Badan Surat:</p>
        <div class="grid gap-6 mb-2 md:grid-cols-2 bg-slate-100 p-4 rounded-lg ">
            <div class="md:col-span-2">
                <label for="paragraf-awal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Paragraf
                    Awal (yang biasanya setelah salam)<span class="text-red-500">*</span></label>
                <label for="paragraf-awal"
                    class="block mb-2 text-sm italic font-medium text-gray-500 dark:text-white">Silahkan edit contoh
                    paragraf berikut</label>
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



        <div class="grid gap-6 mb-2 md:grid-cols-1 bg-slate-100 p-4 rounded-lg ">
            <div class="mt-4">
                <label for="tanggal-mulai-kegiatan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari/tanggal Mulai
                    <span class="text-red-500">*</span></label>
                <input type="date" id="tanggal-mulai-kegiatan" name="tanggal-mulai-kegiatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal kapan kegiatan dimulai"
                    value="{{ $surat->data['private']['tanggalMulaiKegiatan'] }}" required>
            </div>
            <div class="mt-4">
                <label for="tanggal-selesai-kegiatan"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari/tanggal Selesai
                    (Opsional, kosongkan jika hanya 1 hari)</label>
                <input type="date" id="tanggal-selesai-kegiatan" name="tanggal-selesai-kegiatan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih tanggal kapan kegiatan selesai"
                    value="{{ $surat->data['private']['tanggalSelesaiKegiatan'] }}">
            </div>
            <div>
                <label for="waktu" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu Mulai
                    dalam WIB
                    <span class="text-red-500">*</span></label>
                <input type="time" id="waktu" name="waktu"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih waktu kapan kegiatan dilaksanakan"
                    value="{{ $surat->data['private']['waktuMulaiKegiatan'] }}">
                <span class="text-pink-500 text-xs underline
                 cursor-pointer"
                    onclick="document.getElementById('waktu').value='';" title="Reset waktu">Klik di sini untuk
                    reset waktu
                    mulai</span>
            </div>
            <div>
                <label for="waktu-selesai" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Waktu
                    Selesai dalam WIB (Opsional, jika dikosongkan maka akan ditulis s.d selesai)</label>
                <input type="time" id="waktu-selesai" name="waktu-selesai"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Pilih waktu kapan kegiatan selesai dilaksanakan"
                    value="{{ $surat->data['private']['waktuSelesaiKegiatan'] }}">
                <span class="text-pink-500 text-xs underline cursor-pointer"
                    onclick="document.getElementById('waktu-selesai').value='';" title="Reset waktu">Klik di sini
                    untuk reset waktu
                    selesai</span>
            </div>
            <div class="flex items-center mt-[-16px]">
                <input type="checkbox" id="jadwal-terlampir-checkbox" class="mr-2" onchange="toggleWaktuInput()">
                <label for="jadwal-terlampir-checkbox"
                    class="text-sm font-medium text-gray-500 dark:text-white">Centang ini jika jadwal / waktu ada di
                    lampiran (Di surat akan tertulis "Jadwal terlampir")</label>
            </div>
            <input type="hidden" name="waktu" id="hidden-waktu"
                value="{{ $surat->data['private']['waktuMulaiKegiatan'] }}">
            <div>
                <label for="tempat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat<span
                        class="text-red-500">*</span></label>
                <input type="text" name="tempat" id="tempat"
                    class="bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Masukkan tempat pelaksanaan" value="{{ $surat->data['tempat'] }}" required>
            </div>
        </div>

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
