@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Admin | Data Surat
    </x-slot:title>
</x-layout>
