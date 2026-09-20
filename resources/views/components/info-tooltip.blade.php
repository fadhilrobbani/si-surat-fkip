@props([
    'id' => 'tooltip-info-' . Str::random(8),
    'text' => 'Surat dianggap sah jika status surat adalah selesai dan sudah terdapat tanda tangan berupa QR Code.',
    'size' => 'w-4 h-4'
])

<span class="relative inline-flex items-center">
    <button data-tooltip-target="{{ $id }}" type="button"
        title="{{ $text }}"
        class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none p-0.5 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-help">
        <svg class="{{ $size }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <span class="sr-only">Informasi</span>
    </button>
    <div id="{{ $id }}" role="tooltip"
        class="absolute z-50 invisible inline-block px-3 py-2 text-xs font-normal normal-case text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-lg opacity-0 tooltip max-w-xs dark:bg-gray-700 text-left">
        {{ $text }}
        <div class="tooltip-arrow" data-popper-arrow></div>
    </div>
</span>
