<x-mail::message>
# E-Surat {{ $surat->jenisSurat->name }} Anda Sudah Terbit!

Kepada Yth. {{ $surat->pengaju->name }}

Dengan hormat,

Kami ingin menyampaikan bahwa {{ $surat->jenisSurat->name }} yang anda ajukan di website E-Surat FKIP UNIB telah disetujui. Kini Anda dapat mengunduh surat melalui lampiran email ini atau dapat mencetaknya di website E-Surat FKIP UNIB di bagian riwayat pengajuan surat.

Terima kasih atas perhatiannya.

Hormat kami,
{!! '<br>Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu'  !!}

@php
    $url = 'http://www.google.com';
@endphp
<x-mail::button :url="$url">
Visit E-Surat FKIP UNIB
</x-mail::button>
</x-mail::message>
