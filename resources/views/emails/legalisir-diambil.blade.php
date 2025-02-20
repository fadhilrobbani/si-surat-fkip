<x-mail::message>
# Berkas legalisir ijazah UNIB Anda telah siap diambil

Kepada Yth. {{ $surat->pengaju->name }}

Dengan hormat,

Kami ingin menyampaikan bahwa {{ $surat->jenisSurat->name }} yang anda ajukan di website E-Surat FKIP UNIB telah
disetujui dan kini Anda dapat mengambilnya di bagian FKIP. Kini Anda dapat mengecek statusnya di website E-Surat FKIP
UNIB di bagian riwayat pengajuan
surat.

Terima kasih atas perhatiannya.

Hormat kami,
{!! '<br>Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu' !!}

@php
    $url = 'https://esurat-fkip.unib.ac.id';
@endphp
<x-mail::button :url="$url">
    Visit E-Surat FKIP UNIB
</x-mail::button>
</x-mail::message>
