@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Surat Disetujui
    </x-slot:title>
    surat setuju
    {{-- @foreach ($daftarSuratMasuk as $surat)
        <div>
            <p>{{ $surat->pengaju_id }}</p>
            <p>{{ $surat->data['name'] }}</p>
        </div>
    @endforeach --}}
</x-layout>
