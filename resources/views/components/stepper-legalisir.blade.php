@props(['surat'])

@php
    if ($surat->data['pengiriman'] == 'dikirim') {
        $steps = [
            ['status' => 'menunggu_pembayaran', 'label' => 'Menunggu Pembayaran', 'description' => 'Pembayaran telah diterima'],
            ['status' => 'diproses', 'label' => 'Diproses Akademik', 'description' => 'Pengajuan Legalisir telah diproses'],
            ['status' => 'dikirim', 'label' => 'Dikirim ke Alamat Tujuan', 'description' => 'Dokumen Legalisir telah dikirim'],
            ['status' => 'selesai', 'label' => 'Dokumen Diterima', 'description' => 'Dokumen Legalisir telah diterima']
        ];
    } else {
        $steps = [
            ['status' => 'menunggu_pembayaran', 'label' => 'Menunggu Pembayaran', 'description' => 'Pembayaran telah diterima'],
            ['status' => 'diproses', 'label' => 'Diproses Akademik', 'description' => 'Pengajuan Legalisir telah diproses'],
            ['status' => 'selesai', 'label' => 'Dokumen Diterima', 'description' => 'Dokumen Legalisir telah siap. Silakan datang ke Akademik']
        ];
    }

    $currentStatus = $surat->status;
    $currentStepIndex = array_search($currentStatus, array_column($steps, 'status'));
    $isRejected = $currentStatus === 'ditolak';
    $isExpired = $surat->expired_at < now() && $currentStatus === 'menunggu_pembayaran';

    if($isRejected) {
        $currentStepIndex = 1; // Step Akademik sebagai penolakan
    } elseif($isExpired) {
        $currentStepIndex = 0; // Kadaluarsa di step pembayaran
    }
@endphp

<div>
    <ol class="relative mx-8 text-gray-500 border-l border-gray-200 dark:border-gray-700 dark:text-gray-400">
        @foreach ($steps as $index => $step)
            <li class="{{ $index < count($steps) - 1 ? 'mb-10' : '' }} ml-6">
                @php
                    $isCompleted = $index < $currentStepIndex || ($currentStatus === 'selesai' && $index === $currentStepIndex);
                    $isCurrent = $index === $currentStepIndex && !$isExpired && !$isRejected;
                    $isError = ($isRejected && $index >= $currentStepIndex) ||
                              ($isExpired && $index >= $currentStepIndex);
                @endphp

                {{-- Icon --}}
                <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white dark:ring-gray-900
                    {{ $isError ? 'bg-pink-300 dark:bg-pink-900' :
                    ($isCompleted ? 'bg-green-200 dark:bg-green-900' :
                    'bg-gray-100 dark:bg-gray-700') }}">

                    @if ($isError)
                        <svg class="w-3.5 h-3.5 text-rose-700 dark:text-rose-300" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    @elseif($isCompleted)
                        <svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 5.917 5.724 10.5 15 1.5"/>
                        </svg>
                    @else
                        <svg class="w-3.5 h-3.5 {{ $isCurrent ? 'text-blue-700 dark:text-white' : 'text-gray-500' }} dark:text-gray-400"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 18 20">
                            <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
                        </svg>
                    @endif
                </span>

                {{-- Content --}}
                <h3 class="font-medium leading-tight {{ $isCurrent ? 'text-blue-700 dark:text-white' : '' }}">
                    {{ $step['label'] }}
                </h3>
                <p class="text-sm {{ $isCurrent ? 'text-blue-700 dark:text-white' : '' }}">
                    @if ($isError)
                        {{ $isExpired && $index === 0 ? 'Pembayaran kadaluarsa' : ($isRejected ? 'Ditolak' : 'Gagal diproses') }}
                    @elseif($isCompleted)
                        {{ $step['description'] }}
                    @else
                        {{ $isCurrent ? 'Sedang diproses' : 'Menunggu' }}
                    @endif
                </p>
            </li>
        @endforeach
    </ol>
</div>
