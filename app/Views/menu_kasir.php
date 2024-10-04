<?php
$menu[0] = [
    [
        'c' => 'Penjualan',
        'title' => 'Buka Order',
        'icon' => 'fas fa-cash-register',
        'txt' => 'Buka Order [ <b>' . $_SESSION['data']['cabang']['kode_cabang'] . '</b> ]'
    ],
    [
        'c' => 'Antrian',
        'title' => 'Data Order',
        'icon' => 'far fa-clock',
        'txt' => 'Order Proses',
        'submenu' =>
        [
            [
                'c' => '/i/1',
                'title' => 'Data Order Proses H7-',
                'txt' => 'Proses Terkini',
            ],
            [
                'c' => '/i/6',
                'title' => 'Data Order Proses H7+',
                'txt' => 'Proses >1 Minggu',
            ],
            [
                'c' => '/i/7',
                'title' => 'Data Order Proses H30+',
                'txt' => 'Proses >1 Bulan',
            ],
            [
                'c' => '/i/8',
                'title' => 'Data Order Proses H365+',
                'txt' => 'Proses >1 Tahun',
            ],
        ]
    ],
    [
        'c' => 'Operasi',
        'title' => 'Operasi Order',
        'icon' => 'fas fa-tasks',
        'txt' => 'Order Operasi',
        'submenu' =>
        [
            [
                'c' => '/i/1/0/0',
                'title' => 'Operasi Order Proses',
                'txt' => 'Proses',
            ],
            [
                'c' => '/i/2/0/0',
                'title' => 'Operasi Order Tuntas',
                'txt' => 'Tuntas',
            ],
        ]
    ],
    [
        'c' => 'Filter',
        'title' => 'Order Filter',
        'icon' => 'fas fa-filter',
        'txt' => 'Order Filter',
        'submenu' =>
        [
            [
                'c' => '/i/2',
                'title' => 'Order Filter Pengantaran',
                'txt' => 'Pengantaran',
            ],
            [
                'c' => '/i/1',
                'title' => 'Order Filter Pengambilan',
                'txt' => 'Pengambilan',
            ],
        ]
    ],
    [
        'c' => 'Antrian',
        'title' => 'Data Piutang',
        'icon' => 'fas fa-receipt',
        'txt' => 'Order Piutang',
        'submenu' =>
        [
            [
                'c' => '/p/100',
                'title' => 'Data Piutang H7-',
                'txt' => 'Piutang Terkini',
            ],
            [
                'c' => '/p/101',
                'title' => 'Data Piutang H7+',
                'txt' => 'Piutang >1 Minggu',
            ],
            [
                'c' => '/p/102',
                'title' => 'Data Piutang H30+',
                'txt' => 'Piutang >1 Bulan',
            ],
            [
                'c' => '/p/103',
                'title' => 'Data Piutang H365+',
                'txt' => 'Piutang >1 Tahun',
            ],
        ]
    ],
    [
        'c' => '',
        'title' => 'Deposit',
        'icon' => 'fas fa-book',
        'txt' => 'Saldo Pelanggan',
        'submenu' =>
        [
            [
                'c' => 'Member/tampil_rekap',
                'title' => 'List Deposit Member',
                'txt' => 'List Saldo Paket',
            ],
            [
                'c' => 'Member/tambah_paket/0',
                'title' => '(+) Deposit Member',
                'txt' => 'Topup Saldo Paket',
            ],
            [
                'c' => 'SaldoTunai/tampil_rekap',
                'title' => 'List Deposit Tunai',
                'txt' => 'List Saldo Deposit',
            ],
            [
                'c' => 'SaldoTunai/tambah',
                'title' => '(+) Deposit Tunai',
                'txt' => 'Topup Saldo Deposit',
            ],
        ]
    ],
    [
        'c' => 'Data_List/i/pelanggan',
        'title' => 'Pelanggan',
        'icon' => 'fas fa-address-book',
        'txt' => 'Pelanggan'
    ],
    [
        'c' => '',
        'title' => 'Karyawan',
        'icon' => 'fas fa-users-cog',
        'txt' => 'Karyawan',
        'submenu' =>
        [
            [
                'c' => 'Absen',
                'title' => 'Karyawan Absen',
                'txt' => 'Absen Harian',
            ],
            [
                'c' => 'Kinerja/index/0',
                'title' => 'Karyawan - Kinerja Harian',
                'txt' => 'Kinerja Harian',
            ],
            [
                'c' => 'Kinerja/index/1',
                'title' => 'Karyawan - Kinerja Bulanan',
                'txt' => 'Kinerja Bulanan',
            ],
            [
                'c' => 'Data_List/i/karyawan',
                'title' => 'Karyawan Mac Address',
                'txt' => 'Wifi Mac Address',
            ],
        ]
    ],
];
