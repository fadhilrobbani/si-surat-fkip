@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Staff | Konfirmasi Setuju
    </x-slot:title>
</x-layout>
