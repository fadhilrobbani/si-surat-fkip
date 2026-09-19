@props(['key', 'value'])

@if (is_array($value))
    @if ($key === 'items' || $key === 'rincian_biaya' || (isset($value[0]) && is_array($value[0]) && (isset($value[0]['uraian']) || isset($value[0]['nominal']))))
        <div class="overflow-x-auto my-2 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
            <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-100 dark:bg-gray-700 uppercase text-gray-700 dark:text-gray-300 text-[11px]">
                    <tr>
                        <th class="px-3 py-2 text-center w-12">No</th>
                        <th class="px-3 py-2">Uraian / Kebutuhan</th>
                        @if (isset($value[0]['volume']))
                            <th class="px-3 py-2 text-center">Vol</th>
                            <th class="px-3 py-2 text-center">Satuan</th>
                            <th class="px-3 py-2 text-right">Harga Satuan</th>
                        @endif
                        <th class="px-3 py-2 text-right w-40">Jumlah Anggaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @foreach ($value as $idx => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-3 py-2 text-center font-medium text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $item['uraian'] ?? '-' }}</td>
                            @if (isset($value[0]['volume']))
                                <td class="px-3 py-2 text-center">{{ $item['volume'] ?? 1 }}</td>
                                <td class="px-3 py-2 text-center">{{ $item['satuan'] ?? '-' }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format((float)($item['harga_satuan'] ?? 0), 0, ',', '.') }}</td>
                            @endif
                            <td class="px-3 py-2 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format((float)($item['nominal'] ?? ($item['subtotal'] ?? 0)), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <ul class="list-disc pl-5 space-y-1 text-sm">
            @foreach ($value as $subKey => $subVal)
                <li>
                    @if (is_array($subVal))
                        <span class="font-medium">{{ is_numeric($subKey) ? '' : ucwords(str_replace('_', ' ', $subKey)) . ': ' }}</span>
                        {{ json_encode($subVal) }}
                    @else
                        @if (!is_numeric($subKey))
                            <span class="font-medium">{{ ucwords(str_replace('_', ' ', $subKey)) }}:</span>
                        @endif
                        {{ $subVal }}
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@elseif ($key === 'totalAnggaran' || $key === 'total_anggaran' || $key === 'nominal')
    <span class="font-bold text-emerald-600 dark:text-emerald-400 text-base">
        Rp {{ number_format((float)$value, 0, ',', '.') }}
    </span>
@else
    {{ $value }}
@endif
