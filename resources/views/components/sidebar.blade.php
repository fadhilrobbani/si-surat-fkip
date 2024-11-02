@props(['listsData'])

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-[72px] transition-transform -translate-x-full bg-white sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            @foreach ($listsData as $list)
                <x-sidebar-list :list='$list' />
            @endforeach

        </ul>
    </div>
</aside>
