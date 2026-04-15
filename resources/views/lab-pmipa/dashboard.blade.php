@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Lab PMIPA | Dashboard
    </x-slot:title>
    <div class="flex sm:flex-row flex-col gap-4 min-h-fit mb-4">
        <!-- Welcome Banner Section -->
        <div class="flex-grow sm:w-3/4 flex items-center justify-between rounded bg-gray-50 dark:bg-gray-800 p-4">
            <div class="flex-grow pr-4">
                <p class="font-bold text-2xl text-indigo-400">Selamat Datang {{ $authUser->name }}!</p>
                <br>
                <p class="text-slate-500 font-semibold">Website ini merupakan terobosan baru dari Fakultas Keguruan dan
                    Ilmu Pendidikan dalam mempermudah administrasi surat menyurat di lingkungan FKIP Universitas
                    Bengkulu</p>
            </div>
            <img class="h-48 hidden sm:block" src="{{ asset('images/man-laptop.png') }}" alt="">
        </div>

        <!-- Pengaduan Section -->
        <div class="sm:w-1/4 flex flex-col items-center rounded bg-gray-50 dark:bg-gray-800 p-4">
            <p class="font-semibold text-center text-slate-600 text-lg mb-2">Layanan Pengaduan FKIP UNIB</p>
            <p class="text-slate-500 text-center text-sm mb-3">Scan QR code untuk mengakses layanan <a
                    href="https://qr.me-qr.com/iMId1oUh" class="text-indigo-500" target="_blank">di sini</a></p>
            <div class="w-32 sm:w-40 aspect-square">
                <img class="w-full h-full object-contain" src="{{ asset('images/qrfkip.jpeg') }}"
                    alt="QR Code Pengaduan FKIP UNIB">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <a href="/lab-pmipa/riwayat-pengajuan-surat">
            <div
                class="flex items-center gap-2 flex-col hover:bg-slate-300 justify-center rounded p-4 bg-gray-50 dark:bg-gray-800 min-h-28">
                <p class="font-semibold text-slate-600 text-lg text-center">Riwayat Pengajuan Anda</p>
                <div class="flex flex-wrap justify-center text-white text-sm font-semibold gap-2">
                    <div class="bg-green-400 p-2 rounded-lg">Selesai: {{ count($pengajuanSelesai->toArray()) }}</div>
                    <div class="bg-yellow-400 p-2 rounded-lg">Diproses: {{ count($pengajuanDiproses->toArray()) }}</div>
                    <div class="bg-blue-400 p-2 rounded-lg">Dikirim: {{ count($pengajuanDikirim->toArray()) }}</div>
                    <div class="bg-pink-500 p-2 rounded-lg">Menunggu Dibayar:
                        {{ count($pengajuanMenungguDibayar->toArray()) }}</div>
                    <div class="bg-rose-500 p-2 rounded-lg">Ditolak: {{ count($pengajuanDitolak->toArray()) }}</div>
                    <div class="bg-rose-700 p-2 rounded-lg">Kadaluarsa: {{ count($pengajuanKadaluarsa->toArray()) }}
                    </div>
                </div>
            </div>
        </a>

        <a href="/lab-pmipa/profile">
            <div
                class="flex hover:bg-slate-300 p-4 items-center justify-center gap-4 rounded bg-gray-50 dark:bg-gray-800 h-full">
                <p class="font-semibold text-slate-600 text-lg cursor-pointer text-center">Pengaturan Akun</p>
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" viewBox="0 0 20 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.5 8C5.80777 8 5.13108 7.79473 4.55551 7.41015C3.97993 7.02556 3.53133 6.47893 3.26642 5.83939C3.00152 5.20985 2.9322 4.49612 3.06725 3.81720C3.2023 3.13825 3.53564 2.51461 4.02513 2.02513C4.51461 1.53564 5.13825 1.2023 5.81720 1.06725C6.49612 0.932205 7.20985 1.00152 7.83939 1.26642C8.47893 1.53133 9.02556 1.97993 9.41015 2.55551C9.79473 3.13108 10 3.80777 10 4.5"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6.5 17H1V15C1 13.9391 1.42143 12.9217 2.17157 12.1716C2.92172 11.4214 3.93913 11 5 11"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </a>
    </div>

    <div class="flex items-center justify-center flex-col gap-4 p-4 mb-4 rounded bg-gray-50 dark:bg-gray-800">
        <p class="font-semibold  text-slate-600 text-lg">F.A.Q</p>

        <div id="accordion-color" class="w-full" data-accordion="collapse"
            data-active-classes="bg-blue-100 dark:bg-gray-800 text-blue-600 dark:text-white">
            <h2 id="accordion-color-heading-1">
                <button type="button"
                    class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-500 border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800"
                    data-accordion-target="#accordion-color-body-1" aria-expanded="true"
                    aria-controls="accordion-color-body-1">
                    <span>Bagaimana cara mengajukan surat?</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-color-body-1" class="hidden" aria-labelledby="accordion-color-heading-1">
                <div class="p-5 border border-b-0 w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                    <ol class="max-w-md space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400">
                        <li>
                            Buka tab <a class="underline" href="/lab-pmipa/pengajuan-surat">Pengajuan Surat</a>
                        </li>
                        <li>
                            Pilih surat yang ingin diajukan. Lalu tekan tombol "Selanjutnya"
                        </li>
                        <li>
                            Isi form yang disediakan beserta persyaratannya seperti lampiran file jika diperlukan. Jika
                            sudah tekan tombol Ajukan Surat.
                        </li>
                        <li>
                            Surat berhasil diajukan
                        </li>
                        <li>
                            Anda dapat mereview dan melihat surat di halaman <a class="underline"
                                href="/lab-pmipa/riwayat-pengajuan-surat">Riwayat Pengajuan</a>
                        </li>
                    </ol>
                </div>
            </div>
            <h2 id="accordion-color-heading-2">
                <button type="button"
                    class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800"
                    data-accordion-target="#accordion-color-body-2" aria-expanded="false"
                    aria-controls="accordion-color-body-2">
                    <span>Apa yang harus saya lakukan saat surat berhasil diajukan?</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-color-body-2" class="hidden" aria-labelledby="accordion-color-heading-2">
                <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700">
                    <p class="max-w-md space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400">
                        Anda cukup menunggu hingga surat yang diajukan disetujui oleh semua yang terkait. Untuk melihat
                        proses dan riwayat pengajuan, Anda bisa lihat di menu <a class="underline"
                            href="/lab-pmipa/riwayat-pengajuan-surat">Riwayat Pengajuan</a>. Anda dapat melihat detail
                        surat yang anda ajukan dengan menekan tombol lihat atau membatalkan surat dengan tombol batal
                        jika terjadi kesalahan. Surat yang dibatalkan tidak akan diteruskan lagi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
