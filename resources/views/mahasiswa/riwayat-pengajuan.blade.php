@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Dashboard
    </x-slot:title>
    @foreach ($daftarPengajuan as $surat)
        <div class="m-4 bg-slate-300">

            <p>{{ $surat->data['name'] }}</p>
            <p>{{ $surat->data['programStudi'] }}</p>
            <p>{{ $surat->jenis_surat_id }}</p>
            <p>{{ $surat->status }}</p>
            @php
                $currentUser = App\Models\User::find($surat->current_user_id);
            @endphp
            <p>Menunggu: {{ $currentUser->name }}</p>
        </div>
    @endforeach
</x-layout>
