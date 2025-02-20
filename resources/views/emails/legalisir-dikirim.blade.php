<x-mail::message>
# Berkas legalisir ijazah UNIB Anda telah dikirim!

Kepada Yth. {{ $surat->pengaju->name }}

Dengan hormat,

Kami ingin menyampaikan bahwa {{ $surat->jenisSurat->name }} yang anda ajukan di website E-Surat FKIP UNIB telah
disetujui dan kini sedang dalam pengiriman. Kini Anda dapat mengecek statusnya di website E-Surat FKIP
UNIB di bagian riwayat pengajuan
surat. Adapun informasi pengiriman legalisir ijazah UNIB Anda adalah sebagai berikut:

{!! 'Ekspedisi: JNE' !!}
{!! '<br>Nomor Resi: ' !!} {{ $surat->data['noResi'] }}

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
