@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Akademik | Dashboard
    </x-slot:title>
</x-layout>