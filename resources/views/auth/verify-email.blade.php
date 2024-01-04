@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Verifikasi Email
    </x-slot:title>

    <x-notification />

    <div class="flex flex-col justify-center items-center gap-2">
        <img class="w-28" src="{{ asset('images/logounib.png') }}" alt="logounib" />
        <div class="flex flex-col gap-0 m-0 p-0">
            <h1 class="text-xl  text-slate-700 font-semibold text-center mb-4 mx-auto">Verifikasi Email Anda
            </h1>
            <h1 class=" text-md mt-0 text-slate-700  text-center mb-4 mx-auto"> </h1>
            <h1 class=" text-md mt-0 text-slate-700  text-center mb-4 mx-auto">Kami telah mengirimkan email verifikasi ke
                akun yang telah Anda daftarkan: <b>{{ auth()->user()->email }}</b>. <br>Jika belum menerima email,
                silahkan Tekan tombol "Kirim Email Verifikasi" di bawah,lalu buka email yang Anda daftarkan. Setelah itu
                lakukan
                konfirmasi dengan menekan tombol atau link konfirmasi yang dikirimkan! </h1>
            <form action="{{ route('verification.send', $authUser->id) }}"
                class="self-center flex flex-col justify-center items-center" method="POST">
                @csrf
                @method('post')
                <button class="bg-blue-600 mt-2 p-2 rounded-lg text-white font-semibold hover:bg-blue-800"
                    type="submit">
                    Kirim Ulang Email Verifikasi</button>
            </form>
        </div>

    </div>
    <a href="/logout"><button
            class="p-2 absolute top-4 right-4 bg-pink-700 rounded-lg text-white hover:bg-pink-800">Logout</button></a>
</x-layout>
