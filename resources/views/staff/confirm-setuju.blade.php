@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Konfirmasi Setuju
    </x-slot:title>
    <form class="space-y-6" action="#">
        <div>
            <label for="penerima"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan Kirim</label>
            <select id="penerima" name="penerima"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach ($daftarPenerima as $penerima)
                    <option
                        value="{{ $penerima->id }}">{{ $penerima->username }} - {{ $penerima->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Kirim</button>

    </form>
</div>
</x-layout>
