@php
    $authUser = auth()->user();
    $edit = request()->edit == true;
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Bendahara | Profile
    </x-slot:title>

    <h1 class="mx-auto text-center font-bold mb-2">Pengaturan Akun</h1>

    <form action="{{ route('update-profile-bendahara', $authUser->id) }}" method="POST"
        enctype="multipart/form-data" class="gap-4 justify-center mx-auto flex-col w-full">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
            <input type="text" id="username" name="username" {{ $edit ? '' : 'disabled' }}
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                value="{{ $authUser->username }}" required>
        </div>
        <div class="mb-6">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
            <input type="text" id="name" name="name" {{ $edit ? '' : 'disabled' }}
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                value="{{ $authUser->name }}" required>
        </div>
        <div class="mb-6">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
            <input type="email" id="email" name="email" {{ $edit ? '' : 'disabled' }}
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                value="{{ $authUser->email ?? '' }}" required>
        </div>

        <div class="mb-6">
            <label for="tandatangan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanda Tangan (PNG Transparan)</label>
            @if ($authUser->tandatangan)
                <div class="mb-2">
                    <img class="h-20 border rounded p-1 bg-white" src="{{ asset('storage/' . $authUser->tandatangan) }}" alt="TTD">
                </div>
            @endif
            <input type="file" id="tandatangan" name="tandatangan" accept=".png" {{ $edit ? '' : 'disabled' }}
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
        </div>

        <div class="col-span-2 flex w-full justify-center gap-2 items-center mx-auto mt-0">
            <a class="bg-yellow-400 p-2 text-white font-medium hover:bg-yellow-500 rounded-lg flex {{ $edit ? 'hidden' : '' }}"
                href="{{ url()->current() . '?edit=true' }}">
                <p>&nbsp;Edit Profil</p>
            </a>
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center {{ $edit ? '' : 'hidden' }}">Simpan Perubahan</button>
        </div>
    </form>
</x-layout>
