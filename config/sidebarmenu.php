<?php

return [
    'admin' => [
        [
            'link' => 'admin',
            'title' => 'Dashboard',
            'icon' => asset('svg/piechart.svg'),
            'dropdown' => [],
        ],
        [
            'link' => 'admin/users',
            'title' => 'Daftar Akun',
            'icon' => asset('svg/user.svg'),
            'dropdown' => [
                [
                    'title' => 'Mahasiswa',
                    'link' => 'admin/mahasiswa',
                ],
                [
                    'title' => 'Staff',
                    'link' => 'admin/staff',
                ],
                [
                    'title' => 'Kaprodi',
                    'link' => 'admin/kaprodi',
                ],
                [
                    'title' => 'Wakil Dekan',
                    'link' => 'admin/wd',
                ],
            ],
        ],
        [
            'link' => 'admin/letters',
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
    'mahasiswa' =>  [
        [
            'link' => 'mahasiswa',
            'title' => 'Dashboard',
            'icon' => asset('svg/piechart.svg'),
            'dropdown' => [],
        ],
        [
            'link' => 'letters',
            'title' => 'Daftar Surat',
            'icon' => asset('svg/user.svg'),
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
            'link' => 'letters',
            'title' => 'Daftar Surat',
            'icon' => asset('svg/user.svg'),
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
            'link' => 'letters',
            'title' => 'Daftar Surat',
            'icon' => asset('svg/user.svg'),
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
            'link' => 'letters',
            'title' => 'Daftar Surat',
            'icon' => asset('svg/user.svg'),
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




