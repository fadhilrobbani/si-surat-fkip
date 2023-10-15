@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        WD FKIP | Dashboard
    </x-slot:title>
</x-layout>
