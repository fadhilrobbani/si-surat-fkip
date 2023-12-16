@php
    $authUser = auth()->user();
    $edit = request()->edit == true;
    // dd($edit);
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Kaprodi| Profile
    </x-slot:title>

    <h1 class="mx-auto text-center font-bold mb-2">Pengaturan Akun</h1>

    <form action="{{ route('update-profile-kaprodi', $authUser->id) }}" enctype="multipart/form-data" method="POST"
        class="md:grid flex  md:grid-cols-2 gap-4 justify-center mx-auto flex-col w-full">
        @csrf
        @method('PUT')
        <div class="mb-6">
            <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username
            </label>
            <input type="text" id="username" name="username" {{ $edit ? '' : 'disabled' }}
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Masukkan NPM atau username" value="{{ $authUser->username }}" required>
        </div>
        <div class="mb-6">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap
            </label>
            <input type="text" id="name" name="name" {{ $edit ? '' : 'disabled' }}
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Masukkan Nama Lengkap Anda" value="{{ $authUser->name }}" required>
        </div>
        <div class="mb-6">
            <label for="nip" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIP
            </label>
            <input type="text" id="nip" name="nip" {{ $edit ? '' : 'disabled' }}
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Masukkan NIP Anda" value="{{ $authUser->nip }}" required>
        </div>
        <div class="mb-6">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email (Anda wajib
                konfirmasi email jika ingin mengubah email)
            </label>
            <input type="email" id="email" name="email" {{ $edit ? '' : 'disabled' }}
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Masukkan Akun Email Anda yang Aktif" value="{{ $authUser->email ?? '' }}" required>
            @if ($authUser->email_verified_at == null)
                <a href="{{ route('verification.notice') }}">
                    <span
                        class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                        {{ 'Email Anda belum terverifikasi. Klik di sini' }}
                    </span>
                </a>
            @endif

        </div>

        <div class="mb-6">
            <label for="program-studi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Program
                Studi (Hanya bisa diubah admin)
            </label>
            <select type="text" disabled
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                required>
                <option value="">Pilih Program Studi</option>
                @foreach ($daftarProgramStudi as $programStudi)
                    <option {{ $authUser->program_studi_id == $programStudi->id ? 'selected' : '' }}
                        value="{{ $programStudi->id }}">{{ $programStudi->name }}</option>
                @endforeach
            </select>
            <input type="text" hidden name="program-studi" value="{{ $authUser->program_studi_id }}">
        </div>

        {{-- <div class="mb-6">
            <label for="ttd" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanda Tangan
            </label>
            @if ($edit)
                <input
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                    aria-describedby="file_input_help" id="ttd" type="file" name="ttd" accept=".png"
                    max-size="2048">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">File .PNG (Background
                    Transparan MAX.
                    2 MB).</p>
            @else
                <img class="w-20" src="{{ asset('storage/' . $authUser->tandatangan) }}" alt="">
            @endif
        </div> --}}

        <div class="col-span-2 flex w-full justify-center gap-2 items-center mx-auto mt-0">
            <a class="bg-yellow-400 p-2 text-white font-medium hover:bg-yellow-500  rounded-lg flex {{ $edit ? 'hidden' : '' }}"
                href="{{ url()->current() . '?edit=true' }}">

                <svg class="w-6 h-6  text-white dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.109 17H1v-2a4 4 0 0 1 4-4h.87M10 4.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm7.95 2.55a2 2 0 0 1 0 2.829l-6.364 6.364-3.536.707.707-3.536 6.364-6.364a2 2 0 0 1 2.829 0Z" />
                </svg>
                <p>&nbsp;Edit</p>

            </a>
            <a class="bg-rose-600 p-2 text-white font-medium hover:bg-rose-700  rounded-lg flex {{ $edit ? 'hidden' : '' }}"
                href="{{ route('password.request') }}">

                <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p>&nbsp;Atur Ulang Kata Sandi</p>

            </a>
            <a class="bg-pink-700 p-2 text-white font-medium hover:bg-pink-800  rounded-lg flex {{ $edit ? '' : 'hidden' }}"
                href="{{ url()->current() }}">

                <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m13 7-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p>&nbsp;Batal</p>

            </a>
            @if ($edit)
                <button
                    class="bg-blue-700 p-2 text-white font-medium hover:bg-blue-800  rounded-lg flex {{ $edit ? '' : 'hidden' }}"
                    type="submit">

                    <svg class="w-6 h-6 text-white dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m7 10 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p>&nbsp;Perbarui</p>

                </button>
                {{-- <button type="submit"
                    class="text-white  rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Update</button> --}}
            @endif
        </div>


    </form>

</x-layout>
