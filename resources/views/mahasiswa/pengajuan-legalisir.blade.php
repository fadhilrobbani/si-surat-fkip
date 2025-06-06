@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Pengajuan Legalisir
    </x-slot:title>

    <div class="container mx-auto p-2">
        <h1 class="text-lg font-bold text-center text-gray-900 mb-8">Pengajuan Legalisir</h1>

        {{-- Uncomment if you want to use breadcrumbs --}}
        {{-- {{ Breadcrumbs::render('pengajuan-surat') }} --}}

        <form action="{{ route('redirect-form-legalisir') }}" method="POST" class="bg-slate-50 shadow-md rounded p-8 mb-4">
            @csrf
            @method('post')

            <label for="jenisSurat" class="block text-sm font-medium text-gray-700 mb-2">Pilih opsi legalisir</label>
            <select id="jenisSurat" name="jenisSurat" required
                class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent block w-full p-3 mb-4">
                <option value="">Pilih legalisir yang akan diajukan</option>
                @foreach ($daftarJenisSurat as $jenisSurat)
                    <option value="{{ $jenisSurat->slug }}">{{ $jenisSurat->name }}</option>
                @endforeach
            </select>

            <button type="submit"
                class="w-fit  bg-blue-600 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Selanjutnya
            </button>
        </form>
    </div>
</x-layout>
