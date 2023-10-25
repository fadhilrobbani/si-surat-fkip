<x-mail::message>
    # {{ $surat->jenisSurat->name }} yang anda ajukan sudah disetujui!

    Halo, {{ $surat->pengaju->name }}
    <br>
    Sekarang anda bisa mengunduh surat via email ini ataupun di website e-surat FKIP UNIB

    @php
        $url = 'http://www.google.com';
    @endphp
    <x-mail::button :url="$url">
        Unduh
    </x-mail::button>

    Thanks,<br>
    {{ 'Fakultas FKIP Universitas Bengkulu' }}
</x-mail::message>
