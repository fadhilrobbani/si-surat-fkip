@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Dashboard
    </x-slot:title>
    <div
        class="flex sm:flex-row flex-col-reverse items-center justify-evenly min-h-fit  p-4 mb-4 rounded bg-gray-50 dark:bg-gray-800">
        <div>
            <p class="font-bold text-2xl text-indigo-400">Selamat Datang {{ $authUser->name }}!</p>
            <br>
            <p class="text-slate-500 font-semibold">Website ini merupakan terobosan baru dari Fakultas Keguruan dan Ilmu
                Pendidikan dalam mempermudah administrasi surat menyurat di lingkungan FKIP Universitas Bengkulu</p>
        </div>
        <img class="h-48 m-2 p-2" src="{{ asset('images/man-laptop.png') }}" alt="">
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <a href="/mahasiswa/riwayat-pengajuan-surat">
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

        <a href="/mahasiswa/profile">
            <div
                class="flex hover:bg-slate-300 items-center justify-center gap-4 rounded bg-gray-50 dark:bg-gray-800 h-full">
                <p class="font-semibold text-slate-600 text-lg cursor-pointer text-center">Pengaturan Akun</p>
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" viewBox="0 0 20 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.5 8C5.80777 8 5.13108 7.79473 4.55551 7.41015C3.97993 7.02556 3.53133 6.47893 3.26642 5.83939C3.00152 5.19985 2.9322 4.49612 3.06725 3.81719C3.2023 3.13825 3.53564 2.51461 4.02513 2.02513C4.51461 1.53564 5.13825 1.2023 5.81719 1.06725C6.49612 0.932205 7.19985 1.00152 7.83939 1.26642C8.47893 1.53133 9.02556 1.97993 9.41015 2.55551C9.79473 3.13108 10 3.80777 10 4.5"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6.5 17H1V15C1 13.9391 1.42143 12.9217 2.17157 12.1716C2.92172 11.4214 3.93913 11 5 11"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M19.5 11H18.38C18.2672 10.5081 18.0714 10.0391 17.801 9.613L18.601 8.818C18.6947 8.72424 18.7474 8.59708 18.7474 8.4645C18.7474 8.33192 18.6947 8.20476 18.601 8.111L17.894 7.404C17.8002 7.31026 17.6731 7.25761 17.5405 7.25761C17.4079 7.25761 17.2808 7.31026 17.187 7.404L16.392 8.204C15.9647 7.93136 15.4939 7.73384 15 7.62V6.5C15 6.36739 14.9473 6.24021 14.8536 6.14645C14.7598 6.05268 14.6326 6 14.5 6H13.5C13.3674 6 13.2402 6.05268 13.1464 6.14645C13.0527 6.24021 13 6.36739 13 6.5V7.62C12.5081 7.73283 12.0391 7.92863 11.613 8.199L10.818 7.404C10.7242 7.31026 10.5971 7.25761 10.4645 7.25761C10.3319 7.25761 10.2048 7.31026 10.111 7.404L9.404 8.111C9.31026 8.20476 9.25761 8.33192 9.25761 8.4645C9.25761 8.59708 9.31026 8.72424 9.404 8.818L10.204 9.618C9.9324 10.0422 9.73492 10.5096 9.62 11H8.5C8.36739 11 8.24021 11.0527 8.14645 11.1464C8.05268 11.2402 8 11.3674 8 11.5V12.5C8 12.6326 8.05268 12.7598 8.14645 12.8536C8.24021 12.9473 8.36739 13 8.5 13H9.62C9.73283 13.4919 9.92863 13.9609 10.199 14.387L9.404 15.182C9.31026 15.2758 9.25761 15.4029 9.25761 15.5355C9.25761 15.6681 9.31026 15.7952 9.404 15.889L10.111 16.596C10.2048 16.6897 10.3319 16.7424 10.4645 16.7424C10.5971 16.7424 10.7242 16.6897 10.818 16.596L11.618 15.796C12.0422 16.0676 12.5096 16.2651 13 16.38V17.5C13 17.6326 13.0527 17.7598 13.1464 17.8536C13.2402 17.9473 13.3674 18 13.5 18H14.5C14.6326 18 14.7598 17.9473 14.8536 17.8536C14.9473 17.7598 15 17.6326 15 17.5V16.38C15.4919 16.2672 15.9609 16.0714 16.387 15.801L17.182 16.601C17.2758 16.6947 17.4029 16.7474 17.5355 16.7474C17.6681 16.7474 17.7952 16.6947 17.889 16.601L18.596 15.894C18.6897 15.8002 18.7424 15.6731 18.7424 15.5405C18.7424 15.4079 18.6897 15.2808 18.596 15.187L17.796 14.392C18.0686 13.9647 18.2662 13.4939 18.38 13H19.5C19.6326 13 19.7598 12.9473 19.8536 12.8536C19.9473 12.7598 20 12.6326 20 12.5V11.5C20 11.3674 19.9473 11.2402 19.8536 11.1464C19.7598 11.0527 19.6326 11 19.5 11ZM14 14.5C13.5055 14.5 13.0222 14.3534 12.6111 14.0787C12.2 13.804 11.8795 13.4135 11.6903 12.9567C11.5011 12.4999 11.4516 11.9972 11.548 11.5123C11.6445 11.0273 11.8826 10.5819 12.2322 10.2322C12.5819 9.8826 13.0273 9.6445 13.5123 9.54804C13.9972 9.45157 14.4999 9.50108 14.9567 9.6903C15.4135 9.87952 15.804 10.2 16.0787 10.6111C16.3534 11.0222 16.5 11.5055 16.5 12C16.5 12.663 16.2366 13.2989 15.7678 13.7678C15.2989 14.2366 14.663 14.5 14 14.5Z"
                        fill="currentColor" />
                </svg>
            </div>
        </a>
    </div>

    {{-- <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="flex items-center justify-center h-24 rounded bg-gray-50 dark:bg-gray-800">
            <p class="font-semibold">Pengajuan Surat</p>
        </div>
        <div class="flex items-center justify-center h-24 rounded bg-gray-50 dark:bg-gray-800">
            <p class="text-2xl text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 1v16M1 9h16" />
                </svg>
            </p>
        </div>
        <div class="flex items-center justify-center h-24 rounded bg-gray-50 dark:bg-gray-800">
            <p class="text-2xl text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 1v16M1 9h16" />
                </svg>
            </p>
        </div>
    </div> --}}


    <div class="flex items-center justify-center flex-col gap-4 p-4 mb-4 rounded bg-gray-50 dark:bg-gray-800">
        <p class="font-semibold  text-slate-600 text-lg">F.A.Q</p>

        <div id="accordion-color " class="w-full" data-accordion="collapse"
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
                            Buka tab <a class="underline" href="/mahasiswa/pengajuan-surat">Pengajuan Surat</a>
                        </li>
                        <li>
                            Pilih surat yang ingin diajukan. Lalu tekan tombol "Selanjutnya"
                        </li>
                        <li>
                            Isi form yang disediakan beserta persyaratannya seperti lampiran file jika diperlukan. Jika
                            sudah tekan tombol Ajukan Surat. Lalu pilih tujuan kirim ke staff prodi yang tersedia
                            (pastikan sesuai dengan prodi Anda)
                        </li>
                        <li>
                            Surat berhasil diajukan
                        </li>
                        <li>
                            Anda dapat mereview dan melihat surat di halaman <a class="underline"
                                href="{{ '/' . auth()->user()->role->name . '/riwayat-pengajuan-surat' }}">Riwayat
                                Pengajuan</a>
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
                            href="/mahasiswa/riwayat-pengajuan-surat">Riwayat Pengajuan</a>. Anda dapat melihat detail
                        surat yang anda ajukan dengan menekan tombol lihat atau membatalkan surat dengan tombol batal
                        jika terjadi kesalahan. Surat yang dibatalkan tidak akan diteruskan lagi.
                    </p>

                </div>
            </div>
            <h2 id="accordion-color-heading-3">
                <button type="button"
                    class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800"
                    data-accordion-target="#accordion-color-body-3" aria-expanded="false"
                    aria-controls="accordion-color-body-3">
                    <span>Apa maksud status diproses, dikirim, ditolak, selesai, dan expired (kadaluarsa) pada
                        surat?</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-color-body-3" class="hidden" aria-labelledby="accordion-color-heading-3">
                <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700">
                    <ul class="max-w-md space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400">
                        <li>
                            Status diproses berarti surat sedang dalam proses menunggu untuk disetujui oleh semua
                            pihak
                        </li>
                        <li>
                            Status dikirim biasanya muncul saat mengajukan legalisir yang berarti berkas dokumen sedang
                            dalam proses pengiriman oleh ekspedisi ke tempat Anda

                        </li>
                        <li>
                            Status ditolak berarti surat ditolak oleh salah satu pihak yang artinya surat tidak dapat
                            diteruskan lagi. Anda dapat melihat alasan penolakan dan siapa yang menolak di detail lihat
                            surat.
                        </li>
                        <li>
                            Status selesai berarti surat sudah disetujui oleh semua pihak. Surat yang anda ajukan akan
                            dikirim ke email Anda. Anda juga dapat mencetaknya di detail lihat surat.
                        </li>
                        <li>
                            Status expired berarti surat sudah melewati masa aktif pengajuan. Artinya surat tidak dapat
                            diteruskan lagi
                        </li>

                    </ul>

                </div>
            </div>
            <h2 id="accordion-color-heading-4">
                <button type="button"
                    class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800"
                    data-accordion-target="#accordion-color-body-4" aria-expanded="false"
                    aria-controls="accordion-color-body-4">
                    <span>Apa yang harus saya lakukan jika surat ditolak?</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-color-body-4" class="hidden" aria-labelledby="accordion-color-heading-4">
                <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700">
                    <p class="max-w-md space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400">
                        Anda dapat melihat alasan mengapa surat anda ditolak di detail lihat riwayat pengajuan surat.
                        Anda dapat mengajukan surat kembali mengikuti saran dari alasan penolakan tersebut
                    </p>

                </div>
            </div>
            <h2 id="accordion-color-heading-5">
                <button type="button"
                    class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800"
                    data-accordion-target="#accordion-color-body-5" aria-expanded="false"
                    aria-controls="accordion-color-body-5">
                    <span>Bagaimana cara mengetahui surat saya sudah disetujui dan di mana saya dapat
                        mencetaknya?</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-color-body-5" class="hidden" aria-labelledby="accordion-color-heading-5">
                <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700">
                    <p class="max-w-md space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400">
                        Kami akan mengirimkan pesan bahwa surat Anda telah disetujui melalui email yang Anda daftarkan.
                        Anda juga dapat melihat surat yang disetujui dengan melihat status "selesai" pada surat. File
                        surat dapat dicetak manual di lihat riwayat pengajuan.
                        Surat yang telah resmi diterbitkan akan mempunyai tanda tangan digital berupa QR Code
                    </p>

                </div>
            </div>
            <h2 id="accordion-color-heading-6">
                <button type="button"
                    class="flex items-center justify-between w-full p-5 font-medium text-left text-gray-500 border border-b-0 border-gray-200 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 dark:border-gray-700 dark:text-gray-400 hover:bg-blue-100 dark:hover:bg-gray-800"
                    data-accordion-target="#accordion-color-body-6" aria-expanded="false"
                    aria-controls="accordion-color-body-6">
                    <span>Saya tidak menerima email, apa yang harus dilakukan?</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-color-body-6" class="hidden" aria-labelledby="accordion-color-heading-6">
                <div class="p-5 border border-b-0 border-gray-200 dark:border-gray-700">
                    <p class="max-w-md space-y-1 text-gray-500 list-decimal list-inside dark:text-gray-400">
                        Silahkan cek bagian spam pada email yang anda gunakan. Beri tanda juga pada email bahwa email
                        yang kami berikan bukan sebagai spam, agar kedepannya email yang kami kirimkan tidak menjadi
                        spam.
                    </p>

                </div>
            </div>

        </div>


    </div>
    {{-- <div class="grid grid-cols-2 gap-4">
        <div class="flex items-center justify-center rounded bg-gray-50 h-28 dark:bg-gray-800">
            <p class="text-2xl text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 1v16M1 9h16" />
                </svg>
            </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28 dark:bg-gray-800">
            <p class="text-2xl text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 1v16M1 9h16" />
                </svg>
            </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28 dark:bg-gray-800">
            <p class="text-2xl text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 1v16M1 9h16" />
                </svg>
            </p>
        </div>
        <div class="flex items-center justify-center rounded bg-gray-50 h-28 dark:bg-gray-800">
            <p class="text-2xl text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 1v16M1 9h16" />
                </svg>
            </p>
        </div>
    </div> --}}
</x-layout>
