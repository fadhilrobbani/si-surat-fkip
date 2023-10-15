@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Kaprodi | Dashboard
    </x-slot:title>
</x-layout>
