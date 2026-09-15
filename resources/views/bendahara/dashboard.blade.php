@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Bendahara | Dashboard
    </x-slot:title>
    <div class="flex sm:flex-row flex-col-reverse items-center justify-evenly min-h-fit p-4 mb-4 rounded bg-gray-50 dark:bg-gray-800">
        <div>
            <p class="font-bold text-2xl text-indigo-400">Selamat Datang Bendahara FKIP!</p>
            <br>
            <p class="text-slate-500 font-semibold">Kelola dan verifikasi usulan pencairan dana kegiatan prodi, unit kerja, dan kegiatan mahasiswa di lingkungan FKIP Universitas Bengkulu.</p>
        </div>
        <img class="h-48 m-2 p-2" src="{{ asset('images/man-laptop.png') }}" alt="">
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        <a href="/bendahara/surat-masuk">
            <div class="flex items-center gap-2 flex-col hover:bg-slate-300 justify-center rounded p-4 bg-gray-50 h-28 dark:bg-gray-800">
                <p class="font-semibold text-slate-600 text-lg">Surat Masuk Menunggu Pencairan</p>
                <div class="text-white text-sm font-semibold flex gap-2">
                    <div class="bg-blue-600 p-2 rounded-lg">Menunggu: {{ $totalSuratMasuk }}</div>
                </div>
            </div>
        </a>
        <a href="/bendahara/riwayat-persetujuan">
            <div class="flex items-center gap-2 flex-col hover:bg-slate-300 justify-center rounded p-4 bg-gray-50 h-28 dark:bg-gray-800">
                <p class="font-semibold text-slate-600 text-lg">Riwayat Pencairan Dana</p>
                <div class="text-white text-sm font-semibold flex gap-2">
                    <div class="bg-green-500 p-2 rounded-lg">Dicairkan: {{ $totalDisetujui }}</div>
                    <div class="bg-rose-500 p-2 rounded-lg">Ditolak: {{ $totalDitolak }}</div>
                </div>
            </div>
        </a>
        <a href="/bendahara/profile">
            <div class="flex hover:bg-slate-300 items-center justify-center gap-4 rounded bg-gray-50 h-28 dark:bg-gray-800">
                <p class="font-semibold cursor-pointer hover:text-slate-900 text-slate-600 text-lg">Pengaturan Akun</p>
            </div>
        </a>
    </div>
</x-layout>
