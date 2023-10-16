<?php

return [
    'admin' => [
        [
            'link' => 'admin',
            'title' => 'Dashboard',
            'icon' => 'svg/piechart.svg',
            'dropdown' => [],
        ],
        [
            'link' => '',
            'title' => 'Daftar Akun',
            'icon' => 'svg/user.svg',
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
            'icon' => 'svg/letter.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'logout',
            'title' => 'Logout',
            'icon' => 'svg/signout.svg',
            'dropdown' => [],
        ],
    ],
    'mahasiswa' =>  [
        [
            'link' => 'mahasiswa',
            'title' => 'Dashboard',
            'icon' => 'svg/piechart.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'letters',
            'title' => 'Daftar Surat',
            'icon' => 'svg/user.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'logout',
            'title' => 'Logout',
            'icon' => 'svg/signout.svg',
            'dropdown' => [],
        ],
    ],
    'staff' => [
        [
            'link' => 'staff',
            'title' => 'Dashboard',
            'icon' => 'svg/piechart.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'letters',
            'title' => 'Daftar Surat',
            'icon' => 'svg/user.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'logout',
            'title' => 'Logout',
            'icon' => 'svg/signout.svg',
            'dropdown' => [],
        ],
    ],
    'kaprodi' => [
        [
            'link' => 'kaprodi',
            'title' => 'Dashboard',
            'icon' => 'svg/piechart.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'letters',
            'title' => 'Daftar Surat',
            'icon' => 'svg/user.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'logout',
            'title' => 'Logout',
            'icon' => 'svg/signout.svg',
            'dropdown' => [],
        ],
    ],
    'wd' => [
        [
            'link' => 'wd',
            'title' => 'Dashboard',
            'icon' => 'svg/piechart.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'letters',
            'title' => 'Daftar Surat',
            'icon' => 'svg/user.svg',
            'dropdown' => [],
        ],
        [
            'link' => 'logout',
            'title' => 'Logout',
            'icon' => 'svg/signout.svg',
            'dropdown' => [],
        ],
    ],

];




