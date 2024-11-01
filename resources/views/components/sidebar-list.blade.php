@props(['list'])

@if (!$list['dropdown'])
    <li>
        <a href="/{{ $list['link'] }}"
            class="{{ request()->is($list['link']) ? 'bg-slate-300' : '' }} flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 group">
            <img class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                src="{{ $list['icon'] }}" alt="">
            <span class="ml-3">{{ $list['title'] }}</span>

        </a>
    </li>
@else
    <li>
        <button type="button"
            class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700"
            aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
            <img class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                src="{{ $list['icon'] }}" alt="">
            <span class="flex-1 ml-3 text-left whitespace-nowrap">{{ $list['title'] }}</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 4 4 4-4" />
            </svg>
        </button>
        <ul id="dropdown-example" class="py-2 space-y-2">
            @foreach ($list['dropdown'] as $sublist)
                <li>
                    <a href="{{ $sublist['link'] }}"
                        class="{{ request()->url() == $sublist['link'] ? 'bg-slate-300' : '' }} flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">{{ $sublist['title'] }}</a>

                </li>
            @endforeach

        </ul>
    </li>
@endif
