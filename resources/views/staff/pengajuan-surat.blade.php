@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Pengajuan
    </x-slot:title>
    {{-- {{ Breadcrumbs::render('pengajuan-surat') }} --}}
    <h1 class="mx-auto text-center font-bold">Pengajuan Surat</h1>

    {{-- <x-breadcumb /> --}}
    <form action="{{ route('staff-redirect-form-surat') }}" class="mt-4" method="POST">
        @csrf
        @method('post')
        <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih opsi
            surat</label>
        <select id="jenisSurat" name="jenisSurat" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="">Pilih surat yang akan diajukan</option>
            @foreach ($daftarJenisSurat as $jenisSurat)
                <option value="{{ $jenisSurat->slug }}">{{ $jenisSurat->name }}</option>
            @endforeach

        </select>
        <button type="submit"
            class="text-white mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Selanjutnya</button>
    </form>


</x-layout>
