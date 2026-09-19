@props(['key', 'value'])

<tr class="border-b border-gray-200 dark:border-gray-700">
    <td class="px-6 py-4 bg-gray-50 dark:bg-gray-800 font-semibold align-top whitespace-nowrap">
        @if ($key === 'items' || $key === 'rincian_biaya')
            Rincian Kebutuhan Anggaran:&nbsp;
        @elseif ($key === 'totalAnggaran' || $key === 'total_anggaran')
            Total Anggaran Diajukan:&nbsp;
        @elseif ($key === 'namaBank')
            Bank Penerima Transfer:&nbsp;
        @elseif ($key === 'nomorRekening')
            Nomor Rekening:&nbsp;
        @elseif ($key === 'atasNamaRekening')
            Atas Nama Rekening:&nbsp;
        @else
            {{ ucwords(implode(' ', preg_split('/(?=[A-Z])/', str_replace('_', ' ', $key)))) }}:&nbsp;
        @endif
    </td>
    <td class="px-6 py-4">
        <x-surat-data-value :key="$key" :value="$value" />
    </td>
</tr>
