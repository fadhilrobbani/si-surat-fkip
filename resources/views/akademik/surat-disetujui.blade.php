@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        WD FKIP | Surat Disetujui
    </x-slot:title>
</x-layout>
