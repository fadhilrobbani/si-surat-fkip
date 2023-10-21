@php
    $authUser = auth()->user();
@endphp
<x-layout :authUser='$authUser'>
    <x-slot:title>
        WD1 | Surat Masuk
    </x-slot:title>
    show {{ $surat->id }}

</x-layout>
