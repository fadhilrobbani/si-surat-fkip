@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Dashboard
    </x-slot:title>
</x-layout>
