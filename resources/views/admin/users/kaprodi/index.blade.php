@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Admin | Data Kaprodi
    </x-slot:title>
</x-layout>
