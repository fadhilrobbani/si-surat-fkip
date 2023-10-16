@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Admin | Data Wakil Dekan
    </x-slot:title>
</x-layout>
