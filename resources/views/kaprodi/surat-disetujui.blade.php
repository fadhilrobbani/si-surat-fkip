@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        Kaprodi | Surat Disetujui
    </x-slot:title>
</x-layout>
