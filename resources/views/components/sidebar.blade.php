@props(['listsData'])

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-60 h-screen pt-[62px] transition-transform -translate-x-full bg-slate-100 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-slate-100 dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            @foreach ($listsData as $list)
                <x-sidebar-list :list='$list' />
            @endforeach

        </ul>
    </div>
</aside>
