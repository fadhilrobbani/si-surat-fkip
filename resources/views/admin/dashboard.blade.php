@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Admin | Dashboard
    </x-slot:title>
</x-layout>
