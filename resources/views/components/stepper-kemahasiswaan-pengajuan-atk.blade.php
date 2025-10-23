@props(['surat'])
<div>
    <ol class="relative mx-8 text-gray-500 border-l border-gray-200 dark:border-gray-700 dark:text-gray-400">
        <li class="mb-10 ml-6">
            <span
                class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path
                        d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                </svg>
            </span>
            <span
                class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-green-900">
                <svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 5.917 5.724 10.5 15 1.5" />
                </svg>
            </span>
            <h3 class="font-medium leading-tight">Kemahasiswaan</h3>
            <p class="text-sm">Berhasil Mengajukan</p>
        </li>
        <li class="mb-10 ml-6">

            @if ($surat->status == 'ditolak' || ($surat->expired_at < Carbon\Carbon::now() && $surat->status === 'diproses'))
                <span
                    class="absolute flex items-center justify-center w-8 h-8 bg-pink-300 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-green-900">

                    <svg class="w-3.5 h-3.5 text-rose-700 dark:text-rose-700" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>

                </span>
                <h3 class="font-medium leading-tight">Kabag</h3>
                <p class="text-sm">Ditolak</p>
            @elseif ($surat->status == 'selesai')
                <span
                    class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-green-900">
                    <svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 5.917 5.724 10.5 15 1.5" />
                    </svg>
                </span>
                <h3 class="font-medium leading-tight">Kabag</h3>
                <p class="text-sm">Disetujui</p>
            @else
                <span
                    class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700">
                    <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                        <path
                            d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                    </svg>
                </span>
                <h3 class="font-medium leading-tight">Kabag</h3>
                <p class="text-sm">Menunggu</p>
            @endif

        </li>


    </ol>
</div>