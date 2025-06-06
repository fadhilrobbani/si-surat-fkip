@props(['authUser'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>{{ $title ?? 'E-surat FKIP' }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="overflow-x-hidden bg-slate-100">
    <x-notification />
    <nav class="fixed top-0 z-50 w-full bg-slate-100   border-slate-100  dark:bg-gray-800 dark:border-gray-700">
        <div class="px-4 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <!-- Left Section -->
                <div class="flex items-center justify-start">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-gray-900 rounded-lg sm:hidden hover:bg-white focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>

                    </button>
                    <a href="/" class="flex items-center ml-2 md:mr-24">
                        <img src="{{ asset('images/logounib.png') }}" class="h-8 mr-3" alt="UNIB Logo" />
                        <span
                            class="text-xl font-semibold text-slate-900 pt-[1px] dark:text-white whitespace-nowrap">E-Surat
                            FKIP
                            UNIB</span>
                    </a>
                </div>

                <!-- Right Section -->
                <div class="flex items-center space-x-4">
                    <p class="hidden sm:block text-slate-900 font-semibold">{{ $authUser->name }}</p>
                    <button type="button"
                        class="flex items-center text-sm rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                        aria-expanded="false" data-dropdown-toggle="dropdown-user">
                        <span class="sr-only">Open user menu</span>
                        @php
                            $avatar = 'https://ui-avatars.com/api/?name=' . $authUser->name . '&background=random';
                        @endphp
                        <img class="w-8 h-8 rounded-full  dark:border-gray-700"
                            src="{{ $authUser->profile_image ? asset('/storage' . $authUser->profile_image) : $avatar }}"
                            alt="user photo">
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="z-30 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                        id="dropdown-user">
                        <ul class="py-1">
                            <li>
                                <a href="/"
                                    class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-6 h-6 mr-3 text-gray-800 dark:text-white" fill="currentColor"
                                        viewBox="0 0 22 21">
                                        <path
                                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                        <path
                                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ '/' . $authUser->role->name . '/profile' }}"
                                    class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-6 h-6 mr-3 text-gray-800 dark:text-white" fill="none"
                                        viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M10 19a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 11 14H9a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 10 19Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    Profil
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                    class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400" fill="none"
                                        viewBox="0 0 16 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 8h11m0 0-4-4m4 4-4 4m-5 3H3a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h3" />
                                    </svg>
                                    Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    @php
        $listsData = [
            // 'admin' => [
            //     [
            //         'link' => 'admin',
            //         'title' => 'Dashboard',
            //         'icon' => asset('svg/piechart.svg'),
            //         'dropdown' => [],
            //     ],
            //     [
            //         'link' => '',
            //         'title' => 'Daftar Akun',
            //         'icon' => asset('svg/user.svg'),
            //         'dropdown' => [
            //             [
            //                 'title' => 'Mahasiswa',
            //                 'link' => route('admin-mahasiswa'),
            //             ],
            //             [
            //                 'title' => 'Staff',
            //                 'link' => route('admin-staff'),
            //             ],
            //             [
            //                 'title' => 'Kaprodi',
            //                 'link' => route('admin-kaprodi'),
            //             ],
            //             [
            //                 'title' => 'Wakil Dekan',
            //                 'link' => route('admin-wd'),
            //             ],
            //             [
            //                 'title' => 'Akademik',
            //                 'link' => route('admin-akademik'),
            //             ],
            //         ],
            //     ],
            //     [
            //         'link' => 'admin/surat',
            //         'title' => 'Daftar Surat',
            //         'icon' => asset('svg/letter.svg'),
            //         'dropdown' => [],
            //     ],
            //     [
            //         'link' => 'logout',
            //         'title' => 'Logout',
            //         'icon' => asset('svg/signout.svg'),
            //         'dropdown' => [],
            //     ],
            // ],
            'mahasiswa' => [
                [
                    'link' => 'mahasiswa',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'title' => 'Pengajuan',
                    'icon' => asset('svg/letterpencil.svg'),
                    'link' => '/mahasiswa/pengajuan-surat',

                    'dropdown' => [
                        [
                            'title' => 'Surat',
                            'icon' => asset('svg/letterpencil.svg'),
                            'link' => '/mahasiswa/pengajuan-surat',
                        ],
                        [
                            'title' => 'Legalisir',
                            'icon' => asset('svg/letterpencil.svg'),
                            'link' => '/mahasiswa/pengajuan-legalisir',
                        ],
                    ],
                ],
                [
                    'link' => 'mahasiswa/riwayat-pengajuan-surat',
                    'title' => 'Riwayat Pengajuan',
                    'icon' => asset('svg/letterline.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
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
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff/pengajuan-surat',
                    'title' => 'Pengajuan Surat',
                    'icon' => asset('svg/letterpencil.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff/riwayat-pengajuan-surat',
                    'title' => 'Riwayat Pengajuan ',
                    'icon' => asset('svg/letterline.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
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
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'dekan' => [
                [
                    'link' => 'dekan',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'dekan/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'dekan/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
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
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'wd2' => [
                [
                    'link' => 'wd2',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'wd2/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'wd2/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'wd3' => [
                [
                    'link' => 'wd3',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'wd3/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'wd3/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
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
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'staffNilai' => [
                [
                    'link' => 'staff-nilai',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-nilai/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-nilai/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'staffWD1' => [
                [
                    'link' => 'staff-wd1',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-wd1/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-wd1/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'staffWD2' => [
                [
                    'link' => 'staff-wd2',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-wd2/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-wd2/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'staffWD3' => [
                [
                    'link' => 'staff-wd3',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-wd3/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-wd3/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'staffDekan' => [
                [
                    'link' => 'staff-dekan',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-dekan/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-dekan/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-dekan/pengajuan-surat',
                    'title' => 'Pengajuan Surat',
                    'icon' => asset('svg/letterpencil.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'staff-dekan/riwayat-pengajuan-surat',
                    'title' => 'Riwayat Pengajuan ',
                    'icon' => asset('svg/letterline.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'pengirimLegalisir' => [
                [
                    'link' => 'pengirim-legalisir',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'pengirim-legalisir/pengajuan-terbaru',
                    'title' => 'Pengajuan Terbaru',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'pengirim-legalisir/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
                    'icon' => asset('svg/signout.svg'),
                    'dropdown' => [],
                ],
            ],
            'akademikFakultas' => [
                [
                    'link' => 'akademik-fakultas',
                    'title' => 'Dashboard',
                    'icon' => asset('svg/piechart.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'akademik-fakultas/surat-masuk',
                    'title' => 'Surat Masuk',
                    'icon' => asset('svg/letter.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'akademik-fakultas/riwayat-persetujuan',
                    'title' => 'Riwayat Persetujuan',
                    'icon' => asset('svg/lettercheck.svg'),
                    'dropdown' => [],
                ],
                [
                    'link' => 'logout',
                    'title' => 'Keluar',
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
    @elseif ($authUser->role_id == 7)
        <x-sidebar :listsData="$listsData['staffNilai']" />
    @elseif ($authUser->role_id == 8)
        <x-sidebar :listsData="$listsData['dekan']" />
    @elseif ($authUser->role_id == 9)
        <x-sidebar :listsData="$listsData['wd2']" />
    @elseif ($authUser->role_id == 10)
        <x-sidebar :listsData="$listsData['wd3']" />
    @elseif ($authUser->role_id == 11)
        <x-sidebar :listsData="$listsData['staffWD1']" />
    @elseif ($authUser->role_id == 12)
        <x-sidebar :listsData="$listsData['staffWD2']" />
    @elseif ($authUser->role_id == 13)
        <x-sidebar :listsData="$listsData['staffWD3']" />
    @elseif ($authUser->role_id == 14)
        <x-sidebar :listsData="$listsData['staffDekan']" />
    @elseif ($authUser->role_id == 15)
        <x-sidebar :listsData="$listsData['pengirimLegalisir']" />
    @elseif ($authUser->role_id == 16)
        <x-sidebar :listsData="$listsData['akademikFakultas']" />
    @endif



    <div class="px-0 sm:px-4 pt-2 sm:ml-60">
        <div class="p-4 relative bg-white  border-dashed shadow-sm border-slate-300 overflow-x-hidden rounded-xl dark:border-gray-700 mt-14 "
            style="height: calc(100vh - 5rem);">
            {{ $slot }}
        </div>

    </div>
    @isset($script)
        {{ $script }} <!-- Hanya ditampilkan jika $script ada -->
    @endisset

</body>

</html>
