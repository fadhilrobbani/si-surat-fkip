@php
    $authUser = auth()->user();

@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Dashboard
    </x-slot:title>
    @foreach ($daftarPengajuan as $surat)
        <div class="m-4 bg-slate-300">
            @php

                // Tanggal kadaluarsa dari surat (contoh)
                $expiredAt = Illuminate\Support\Carbon::parse($surat->expired_at); // Gantilah dengan tanggal kadaluarsa yang sesuai

                // Waktu saat ini
                $now = Illuminate\Support\Carbon::now();

                // Hitung sisa waktu kadaluarsa dalam hari
                $sisaHari = $now->diffInDays($expiredAt);
            @endphp

            <p>{{ $surat->data['name'] }}</p>
            <p>{{ $surat->data['programStudi'] }}</p>
            <p>{{ $surat->jenis_surat_id }}</p>
            <p>{{ $surat->status }}</p>
            <p>Kadaluarsa dalam {{ $sisaHari }}</p>
            {{-- <p>Catatan: {{ $surat->data['note'] ? $surat->data['note'] : '' }}</p> --}}
            @php
                $currentUser = App\Models\User::find($surat->current_user_id);
            @endphp
            <p>Menunggu: {{ $currentUser->name }}</p>
        </div>
    @endforeach
</x-layout>
