@props(['daftarPenerima', 'signerName' => null, 'signerNip' => null])

<div id="authentication-modal" tabindex="-1" aria-hidden="true"
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-slate-200 rounded-lg shadow-lg dark:bg-gray-700">
            <button type="button"
                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                data-modal-hide="authentication-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="px-6 py-6 lg:px-8 flex flex-col gap-4">
                {{-- <form class="space-y-6" action="#"> --}}
                <div>
                    <label for="penerima" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan
                        Kirim</label>
                    <select id="penerima" name="penerima"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach ($daftarPenerima as $penerima)
                            <option value="{{ $penerima->id }}">{{ $penerima->username }} - {{ $penerima->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if (!is_null($signerName))
                    <div class="rounded-lg border border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Anda menyetujui sebagai:</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100"><span id="modal-signer-name">{{ $signerName }}</span></p>
                        <p class="text-gray-700 dark:text-gray-300">NIP <span id="modal-signer-nip">{{ $signerNip }}</span></p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pastikan nama &amp; NIP ini benar &mdash; akan tercetak pada surat.</p>
                    </div>
                @endif

                <button type="submit"
                    class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Kirim</button>

                {{-- </form> --}}
            </div>
        </div>
    </div>
</div>
