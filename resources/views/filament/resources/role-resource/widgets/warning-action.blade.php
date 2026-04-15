<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-4">
            <h1 class="text-red-600 text-center font-semibold text-xl uppercase tracking-wider">⚠️ PERINGATAN PENTING (DEVELOPER ONLY)</h1>
            <p class="text-center text-gray-600 dark:text-gray-400">
                Halaman ini digunakan untuk mengelola Role sistem.<br>
                Mengubah ID atau menghapus role secara sembarangan dapat menyebabkan <strong>kerusakan logis (Fatal Error)</strong> karena banyak bagian kode yang menggunakan <em>hardcoded ID</em>.
            </p>
            
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                <p class="text-center font-medium mb-3 text-primary-600 dark:text-primary-400">Katalog ID Role Terdaftar (Otomatis Terupdate):</p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 text-sm">
                    @foreach (\App\Models\Role::orderBy('id')->get() as $role)
                        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm">
                            <span class="flex items-center justify-center w-6 h-6 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded font-bold text-xs ring-1 ring-red-200 dark:ring-red-700">
                                {{ $role->id }}
                            </span>
                            <div class="flex flex-col overflow-hidden">
                                <span class="truncate font-mono font-bold text-gray-800 dark:text-gray-200">{{ $role->name }}</span>
                                <span class="truncate text-[10px] text-gray-500 uppercase">{{ $role->description ? Str::limit($role->description, 20) : 'No Desc' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-amber-50 dark:bg-amber-900/20 p-3 rounded-lg border border-amber-200 dark:border-amber-800">
                <p class="text-center text-xs text-amber-700 dark:text-amber-400 italic">
                    * Dokumentasi ini bersifat dinamis. Jika Anda menambahkan Role baru melalui Seeder atau UI, data di atas akan otomatis muncul.
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
