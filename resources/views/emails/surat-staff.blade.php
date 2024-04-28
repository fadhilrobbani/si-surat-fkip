<x-mail::message>
# E-Surat {{ $surat->jenisSurat->name }} Anda Sudah Terbit!

Kepada Yth. {{ $surat->pengaju->name }}

Dengan hormat,

Kami ingin menyampaikan bahwa {{ $surat->jenisSurat->name }} yang anda ajukan di website E-Surat FKIP UNIB telah disetujui. Kini Anda dapat mengeceknya atau mengunduhnya di website E-Surat FKIP UNIB di bagian riwayat pengajuan surat.

Terima kasih atas perhatiannya.

Hormat kami,
{!! '<br>Fakultas Keguruan dan Ilmu Pendidikan Universitas Bengkulu'  !!}

@php
    $url = 'https://esurat-fkip.unib.ac.id';
@endphp
<x-mail::button :url="$url">
Visit E-Surat FKIP UNIB
</x-mail::button>
</x-mail::message>
