@php
    $authUser = auth()->user();
    $edit = request()->edit == true;
    // dd($edit);
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Dekan | Atur Ulang Kata Sandi
    </x-slot:title>

    <h1 class="mx-auto text-center font-bold mb-2">Atur Ulang Kata Sandi</h1>

    <form action="{{ route('reset-password-dekan', $authUser->id) }}" method="POST"
        class=" flex  gap-2 justify-center flex-col  w-full">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label for="old-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kata sandi
            </label>
            <input type="password" id="old-password" name="old-password"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Masukkan kata sandi akun saat ini" required>
        </div>
        <div class="mb-6">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kata sandi baru
            </label>
            <input type="password" id="password" name="password"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Masukkan kata sandi baru anda" required>
        </div>
        <div class="mb-6">
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi kata
                sandi baru
            </label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Masukkan konfirmasi kata sandi baru anda" required>
        </div>
        <a href="{{ route('password.request') }}">
            <p class="text-blue-700  font-semibold underline">Lupa kata sandi?</p>
        </a>



        <div class="col-span-2 flex w-full justify-center gap-2 items-center mx-auto mt-0">
            <a class="bg-pink-700 p-2 text-white font-medium hover:bg-pink-800  rounded-lg flex "
                href="/kaprodi/profile">

                <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m13 7-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p>&nbsp;Batal</p>

            </a>

            <button class="bg-blue-700 p-2 text-white font-medium hover:bg-blue-800  rounded-lg flex " type="submit">


                <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m7 10 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p>&nbsp;Perbarui</p>

            </button>
            {{-- <button type="submit"
                    class="text-white  rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button> --}}

        </div>


    </form>

</x-layout>
