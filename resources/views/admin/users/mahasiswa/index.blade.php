@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Admin | Data Mahasiswa
    </x-slot:title>
</x-layout>
