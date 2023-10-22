@props(['authUser'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'E-surat FKIP' }}</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <x-notification />
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start z-30">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>
                    <a href="/dashboard" class="flex ml-2 md:mr-24">
                        <img src="{{ asset('images/logounib.png') }}" class="h-8 mr-3" alt="FlowBite Logo" />
                        <span
                            class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">E-Surat
                            FKIP UNIB</span>
                    </a>
                </div>
                <div class="flex justify-center items-center">
                    <x-bell :authUser='$authUser' />
                    <div class="flex items-center">
                        <div class="flex items-center ml-3">

                            <button type="button"
                                class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                @php
                                    $avatar = 'https://ui-avatars.com/api/?name=' . $authUser->name . '&background=random';
                                @endphp
                                <img class="w-8 h-8 rounded-full"
                                    src="{{ $authUser->profile_image ? asset('/storage' . $authUser->profile_image) : $avatar }}"
                                    alt="user photo">
                            </button>

                            <div class="z-30 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                                id="dropdown-user">
                                <div class="px-4 py-3" role="none">
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300"
                                        role="none">
                                        {{ $authUser->name }}
                                    </p>
                                    <p class="text-sm text-gray-900 dark:text-white" role="none">
                                        {{ $authUser->username }}
                                    </p>

                                </div>
                                <ul class="py-1" role="none">
                                    <li>
                                        <a href="#"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                            role="menuitem">Dashboard</a>
                                    </li>
                                    <li>
                                        <a href="#"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                            role="menuitem">Settings</a>
                                    </li>

                                    <li>
                                        <a href="{{ route('logout') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                            role="menuitem">Sign out</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <p class="hidden sm:block ml-1 text-slate-600 font-semibold">{{ $authUser->name }}</p>

                </div>

            </div>
        </div>
    </nav>
    @php
        $listsData = [
            'admin' => [
                [
                    'link' => 'admin',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => '',
                    'title' => 'Daftar Akun',
                    'icon' => asset('svg/user.svg'),
                    'dropdown' => [
                        [
                            'title' => 'Mahasiswa',
                            'link' => route('admin-mahasiswa'),
                        ],
                        [
                            'title' => 'Staff',
                            'link' => route('admin-staff'),
                        ],
                        [
                            'title' => 'Kaprodi',
                            'link' => route('admin-kaprodi'),
                        ],
                        [
                            'title' => 'Wakil Dekan',
                            'link' => route('admin-wd'),
                        ],
                        [
                            'title' => 'Akademik',
                            'link' => route('admin-akademik'),
                        ],
                    ],
                ],
                [
                    'link' => 'admin/surat',
                    'title' => 'Daftar Surat',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Logout',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'mahasiswa' => [
                [
                    'link' => 'mahasiswa',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'mahasiswa/pengajuan-surat',
                    'title' => 'Pengajuan Surat',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'mahasiswa/riwayat-pengajuan-surat',
                    'title' => 'Riwayat Pengajuan',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Logout',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'staff' => [
                [
                    'link' => 'staff',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Logout',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'kaprodi' => [
                [
                    'link' => 'kaprodi',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'kaprodi/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'kaprodi/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Logout',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'wd' => [
                [
                    'link' => 'wd',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'wd/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'wd/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Logout',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'akademik' => [
                [
                    'link' => 'akademik',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'akademik/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'akademik/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Logout',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
        ];
    @endphp
    @if ($authUser->role_id == 1)
        <x-sidebar :listsData="$listsData['admin']" />
    @elseif ($authUser->role_id == 2)
        <x-sidebar :listsData="$listsData['mahasiswa']" />
    @elseif ($authUser->role_id == 3)
        <x-sidebar :listsData="$listsData['staff']" />
    @elseif ($authUser->role_id == 4)
        <x-sidebar :listsData="$listsData['kaprodi']" />
    @elseif ($authUser->role_id == 5)
        <x-sidebar :listsData="$listsData['wd']" />
    @elseif ($authUser->role_id == 6)
        <x-sidebar :listsData="$listsData['akademik']" />
    @endif



    <div class="p-4 sm:ml-64">
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
            {{ $slot }}
        </div>
    </div>

</body>

</html>
