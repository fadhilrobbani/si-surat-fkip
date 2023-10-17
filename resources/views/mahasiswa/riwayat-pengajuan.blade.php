@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Mahasiswa | Dashboard
    </x-slot:title>
    riwayat
</x-layout>
