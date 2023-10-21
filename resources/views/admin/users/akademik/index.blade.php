@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Data Akademik
    </x-slot:title>
</x-layout>
