@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Pengajuan Legalisir Ijazah
    </x-slot:title>
    <x-slot:script>
        <script>
            function formatRupiah(input) {
                let value = input.value.replace(/[^0-9]/g, '');
                let formatted = new Intl.NumberFormat('id-ID').format(value);
                input.value = formatted;
            }

            function openModalCekOngkir(event) {
                event.preventDefault();
                document.getElementById('infoModalCekOngkir').classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Disable background scroll
            }

            function closeModalCekOngkir(event) {
                event.preventDefault();
                document.getElementById('infoModalCekOngkir').classList.add('hidden');
                document.body.style.overflow = 'auto'; // Restore scroll functionality
            }

            function openModalHelpOngkirURL(event) {
                event.preventDefault();
                document.getElementById('infoModalHelpOngkirURL').classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Disable background scroll
            }

            function closeModalHelpOngkirURL(event) {
                event.preventDefault();
                document.getElementById('infoModalHelpOngkirURL').classList.add('hidden');
                document.body.style.overflow = 'auto'; // Restore scroll functionality
            }

            //     function hitungHarga() {
            //         let ongkir = document.getElementById('ongkir').value.replace(/[^0-9]/g, '');
            //         ongkir = parseInt(ongkir) || 0; // Parse setelah menghilangkan karakter non-numerik

            //         let jumlahLembar = parseInt(document.getElementById('jumlah-lembar').value) || 0;
            //         let biayaJasa = 5000;
            //         let biayaLembar = 2000 * jumlahLembar;
            //         let totalHarga = ongkir + biayaLembar + biayaJasa;

            //         let deskripsi = `
    //     <table class="w-full">
    //         <tr>
    //             <td class="py-2">Ongkos Kirim:</td>
    //             <td class="py-2 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(ongkir)}</td>
    //         </tr>
    //         <tr>
    //             <td class="py-2">Biaya Legalisir (${jumlahLembar} lembar x Rp 2.000):</td>
    //             <td class="py-2 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(biayaLembar)}</td>
    //         </tr>
    //         <tr>
    //             <td class="py-2">Biaya Jasa:</td>
    //             <td class="py-2 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(biayaJasa)}</td>
    //         </tr>
    //         <tr>
    //             <td class="py-2 font-semibold">Total Harga:</td>
    //             <td class="py-2 text-right font-semibold">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(totalHarga)}</td>
    //         </tr>
    //     </table>
    // `;

            //         document.getElementById('deskripsi-harga').innerHTML = deskripsi;
            //         document.getElementById('total-harga-input').value = totalHarga;
            //     }

            function hitungHarga() {
                let ongkir = document.getElementById('ongkir').value.replace(/[^0-9]/g, '');
                ongkir = parseInt(ongkir) || 0;
                let jumlahLembar = parseInt(document.getElementById('jumlah-lembar').value) || 0;
                let biayaJasa = 5000;
                let biayaLembar = 2000 * jumlahLembar;
                let totalHarga = ongkir + biayaLembar + biayaJasa;
                let deskripsi = `
    <table class="w-full border-collapse border border-gray-300">

        <tbody>
            <tr>
                <td class="py-2 px-4 border border-gray-300">Ongkos Kirim:</td>
                <td class="py-2 px-4 border border-gray-300 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(ongkir)}</td>
            </tr>
            <tr>
                <td class="py-2 px-4 border border-gray-300">Biaya Legalisir (${jumlahLembar} lembar x Rp 2.000):</td>
                <td class="py-2 px-4 border border-gray-300 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(biayaLembar)}</td>
            </tr>
            <tr>
                <td class="py-2 px-4 border border-gray-300">Biaya Jasa:</td>
                <td class="py-2 px-4 border border-gray-300 text-right">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(biayaJasa)}</td>
            </tr>
            <tr>
                <td class="py-2 px-4 border border-gray-300 font-semibold">Total Harga:</td>
                <td class="py-2 px-4 border border-gray-300 text-right font-semibold">${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(totalHarga)}</td>
            </tr>
        </tbody>
    </table>
`;

                document.getElementById('deskripsi-harga').innerHTML = deskripsi;
                document.getElementById('total-harga-input').value = totalHarga;
            }

            // Event listener untuk input ongkir
            document.getElementById('ongkir').addEventListener('input', hitungHarga);

            // Event listener untuk input jumlah-lembar
            document.getElementById('jumlah-lembar').addEventListener('input', hitungHarga);

            // Panggil hitungHarga saat halaman dimuat untuk inisialisasi
            hitungHarga();
        </script>

    </x-slot:script>
    <p class="font-bold text-lg mx-auto text-center mb-2">Pengajuan Legalisir Ijazah</p>
    <form action="{{ route('store-pengajuan-legalisir-ijazah', $jenisSurat->slug) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('post')
        <p class="font-semibold text-slate-500 text-md mx-auto mb-2">Data Pribadi:</p>

        <div class="bg-slate-50 p-4 rounded-lg shadow-lg">
            <div class="grid gap-6  md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama<span
                            class="text-red-500">*</span></label>
                    <input type="text" id="disabled-input-2" aria-label="disabled input 2" readonly name="name"
                        class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan Nama Lengkap" value="{{ $authUser->name }}" required>
                </div>
                <div>
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NPM<span
                            class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username"
                        class=" cursor-not-allowed bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan NPM Anda" value="{{ $authUser->username }}" readonly required>
                </div>
                <div>
                    <label for="program-studi"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih
                        Program Studi<span class="text-red-500">*</span></label>
                    <select id="program-studi" disabled
                        class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach ($daftarProgramStudi as $programStudi)
                            <option {{ $authUser->program_studi_id == $programStudi->id ? 'selected' : '' }}
                                value="{{ $programStudi->id }}">{{ $programStudi->name }}</option>
                        @endforeach
                    </select>
                    <input class="hidden" name="program-studi" type="text" value="{{ $authUser->programStudi->id }}">
                </div>

                <div>
                    <label for="email"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email<span
                            class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" readonly
                        class="bg-gray-50 border cursor-not-allowed border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan email yang aktif" value="{{ $authUser->email }}" required>
                </div>



                <div>

                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="ijazah">Upload
                        Foto Ijazah Berwarna (Tampak depan & belakang)<span class="text-red-500">*</span> </label>
                    <input
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        aria-describedby="file_input_help" id="ijazah" type="file" name="ijazah" accept=".pdf"
                        max-size="2048" required>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">
                        PDF
                        (MAX.
                        2 MB).</p>

                </div>
                <div>

                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="ktp">Upload
                        KTP<span class="text-red-500">*</span> </label>
                    <input
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                        aria-describedby="file_input_help" id="ktp" type="file" name="ktp"
                        accept=".jpg, .jpeg, .png, .pdf" max-size="2048" required>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">PNG, JPG, JPEG, atau
                        PDF
                        (MAX.
                        2 MB).</p>

                </div>
                <div>
                    <label for="jumlah-lembar"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Lembar
                        Legalisir (Maksimal 10 Lembar)<span class="text-red-500">*</span></label>
                    <input type="number" id="jumlah-lembar" min="1" max="10" name="jumlah-lembar"
                        class="bg-gray-50 border  border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Biaya: Rp 2.000/lembar (Max. 10 lembar)" value="{{ old('jumlah-lembar') }}"
                        required>
                </div>

            </div>



        </div>
        <p class="font-semibold text-slate-500 text-md mx-auto  mt-6 mb-2">Data Pengiriman:</p>
        <div class="bg-slate-50 p-4 rounded-lg shadow-lg">
            <div class="grid gap-6 md:grid-cols-1">
                {{-- <div class="flex items-center md:col-span-1">
                    <input id="checked-checkbox" name="ambil" type="checkbox" value="1"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        onchange="toggleInputs(this)">
                    <label for="checked-checkbox"
                        class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Berkas ambil di tempat
                        (Akademik FKIP UNIB)?</label>
                </div> --}}

                <div>
                    <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat
                        Lengkap <span class="text-red-500">*</span></label></label>
                    <input type="text" id="alamat" name="alamat"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan Alamat Lengkap" value="{{ old('alamat') }}" required>
                </div>

                <div>
                    <label for="kode-pos" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode
                        Pos
                        <span class="text-red-500">*</span></label></label>
                    <input type="number" id="kode-pos" name="kode-pos"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan Kode Pos" value="{{ old('kode-pos') }}" required maxlength="5"
                        minlength="5" pattern="[0-9]{5}" title="Masukkan 5 digit kode pos">
                </div>

                <div>
                    <label for="provinsi"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Provinsi <span
                            class="text-red-500">*</span></label></label>
                    <input type="text" id="provinsi" name="provinsi"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan Provinsi" value="{{ old('provinsi') }}" required>
                </div>

                <div>
                    <label for="kota"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kota/Kabupaten <span
                            class="text-red-500">*</span></label></label>
                    <input type="text" id="kota" name="kota"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan Kota/Kabupaten" value="{{ old('kota') }}" required>
                </div>

                <div>
                    <label for="kecamatan"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kecamatan <span
                            class="text-red-500">*</span></label></label>
                    <input type="text" id="kecamatan" name="kecamatan"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan Kecamatan" value="{{ old('kecamatan') }}" required>
                </div>

                <div>
                    <label for="kelurahan"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelurahan <span
                            class="text-red-500">*</span></label></label>
                    <input type="text" id="kelurahan" name="kelurahan"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Masukkan Kelurahan" value="{{ old('kelurahan') }}" required>
                </div>
                <div>
                    <label for="ongkir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Ongkos Kirim JNE REG<span class="text-red-500">*</span>
                        <button type="button" onclick="openModalCekOngkir(event)"
                            class="sm:ml-2 text-blue-700 underline  px-2 hover:text-gray-700 dark:hover:text-gray-300">
                            Klik di sini untuk detail
                        </button>
                    </label>
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 dark:text-gray-400">Rp</span>
                        <input type="text" id="ongkir" name="ongkir"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="0" value="{{ old('ongkir') }}" required oninput="formatRupiah(this)">
                    </div>
                </div>
                <div>
                    <label for="url-ongkir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        URL Ongkos Kirim JNE <span class="text-red-500">*</span>
                        <button type="button" onclick="openModalHelpOngkirURL(event)"
                            class="sm:ml-2 text-blue-700 underline  px-2 hover:text-gray-700 dark:hover:text-gray-300">
                            Klik di sini untuk detail
                        </button>
                    </label>


                    <input type="text" id="url-ongkir" name="url-ongkir"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder=" URL Ongkos Kirim" value="{{ old('url-ongkir') }}" required>

                </div>

                <!-- Modal -->
                <div id="infoModalCekOngkir" class="fixed inset-0 flex items-center justify-center hidden mx-2 ">
                    <div class="bg-white dark:bg-gray-800 p-6 px-10 rounded-lg shadow-lg w-96 relative">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Cek Ongkir JNE</h2>
                        <ol class="list-decimal">
                            <li>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Anda dapat mengecek ongkos
                                    kirim
                                    melalui: <a
                                        href="https://www.jne.co.id/shipping-fee?origin=BKS10000&destination=undefined&weight=1"
                                        target="_blank"
                                        class="text-blue-500 underline">https://jne.co.id/shipping-fee</a>.</p>
                            </li>

                            <li>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Masukkan Kota Awal adalah
                                    <span class="font-bold">
                                        BENGKULU
                                    </span> dan Kota Tujuan adalah kota di mana paket dikirim kepada Anda.
                                </p>
                            </li>
                            <li>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Masukkan berat paket sebesar
                                    <span class="font-bold">
                                        1 KG
                                    </span>
                                </p>
                            </li>
                            <li>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Lalu klik
                                    <span class="font-bold">
                                        Check atau Lihat Harga
                                    </span>.
                                </p>
                            </li>
                            <li>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Lalu lihat harga pada
                                    kode/nama layanan
                                    <span class="font-bold">
                                        REG
                                    </span>. Itulah harga yang Anda input pada kolom input ini.
                                </p>
                            </li>
                            <li>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Copy (salin) URL tersebut
                                    untuk diisi pada input URL Cek Ongkir di bawah ini

                                </p>
                            </li>
                        </ol>
                        <button onclick="closeModalCekOngkir(event)"
                            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-lg">Tutup</button>
                    </div>
                </div>




                <!-- Modal -->
                <div id="infoModalHelpOngkirURL" class="fixed inset-0 flex items-center justify-center hidden mx-2 ">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-96 relative">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Cek Ongkir JNE</h2>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Masukkan URL Cek Ongkir JNE yang
                            menunjukkan informasi ongkir berdasarkan Kota Tujuan Anda, Contoh format URL: <br> <a
                                href="https://jne.co.id/shipping-fee?origin=BKS10000&destination=CGK10000&weight=1"
                                target="_blank"
                                class="text-blue-500 underline">https://jne.co.id/shipping-fee?origin=BKS10000&destination=CGK10000&weight=1</a>.
                        </p>
                        <button onclick="closeModalHelpOngkirURL(event)"
                            class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-lg">Tutup</button>
                    </div>
                </div>



            </div>
        </div>
        <p class="font-semibold text-slate-500 text-md mx-auto  mt-6 mb-2">Data Pembayaran:</p>
        <div class="bg-slate-50 p-4 rounded-lg shadow-lg">
            <div class="grid gap-6 md:grid-cols-1">
                {{--
                <button type="button" onclick="hitungHarga()"
                    class="mt-4 bg-yellow-500 hover:bg-yellow-700 w-fit text-white px-4 py-2 rounded-lg">Hitung
                    Harga</button> --}}

                {{-- <p class="mt-4 font-semibold">Deskripsi Harga:</p> --}}
                <div id="deskripsi-harga"></div>
                <input type="hidden" name="total-harga" id="total-harga-input" required>



            </div>
        </div>

        {{-- <x-modal-send :daftarPenerima='$daftarPenerima' />
        <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
            class="block text-white mt-6 bg-bluve-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            type="button">
            Ajukan Surat
        </button> --}}
        <button type="submit"
            class="text-white mx-auto bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none mt-4 focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Ajukan
            Pembayaran</button>
    </form>



</x-layout>
