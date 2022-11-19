<?php
if (isset($data['data_operasi'])) {
    $title = $data['data_operasi']['title'];
} else {
    $title = "";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="icon" href="<?= $this->ASSETS_URL ?>icon/logo.png">
    <title><?= $title ?> | MDL</title>
    <meta name="viewport" content="width=410, user-scalable=no">
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/ionicons.min.css">
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/fontawesome-free-5.15.4-web/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/adminLTE-3.1.0/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/style.css" rel="stylesheet" />

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">
    <!-- FONT -->

    <?php $fontStyle = "'Titillium Web', sans-serif;" ?>

    <style>
        html .table {
            font-family: <?= $fontStyle ?>;
        }

        html .content {
            font-family: <?= $fontStyle ?>;
        }

        html body {
            font-family: <?= $fontStyle ?>;
        }

        @media print {
            p div {
                font-family: <?= $fontStyle ?>;
                font-size: 14px;
            }
        }
    </style>
</head>

<?php

$hideAdmin = "";
$hideKasir = "";
$classAdmin = "btn-danger";
$classKasir = "btn-success";

if ($this->id_privilege >= 100) {
    $hideAdmin = "d-none";
} else {
    $hideAdmin = "";
}

if (isset($_SESSION['log_mode'])) {
    $log_mode = $_SESSION['log_mode'];
} else {
    $log_mode = 0;
}
if ($log_mode == 1) {
    $hideAdmin = "";
    $hideKasir = "d-none";
    $classKasir = "btn-secondary";
} else {
    $hideAdmin = "d-none";
    $hideKasir = "";
    $classAdmin = "btn-secondary";
}

?>

<body class="hold-transition sidebar-mini small">
    <div class="loaderDiv" style="display: none;">
        <div class="loader"></div>
    </div>

    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light sticky-top pb-0">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link p-0 pl-2 pr-2" data-widget="pushmenu" href="#" role="button"> <span class="btn btn-sm"><i class="fas fa-bars"></i> Menu</span></a>
                </li>
            </ul>

            <?php if ($this->id_privilege == 100 or $this->id_privilege == 101 or $this->id_privilege == 12) { ?>
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item waitReady d-none">

                        <?php if (isset($data['data_operasi']['vLaundry'])) {
                            if ($data['data_operasi']['vLaundry'] == true) { ?>
                                <select id="selectCabang" disabled class="form-control form-control-sm bg-primary mb-2">
                                    <option class="font-weight-bold" selected><?= $this->dLaundry['nama_laundry'] ?></option>
                                </select>
                            <?php }
                        } else { ?>
                            <select id="selectCabang" class="form-control form-control-sm bg-primary mb-2">
                                <?php foreach ($this->listCabang as $lcb) { ?>
                                    <option class="font-weight-bold" value="<?= $lcb['id_cabang'] ?>" <?php
                                                                                                        if ($this->id_cabang == $lcb['id_cabang']) {
                                                                                                            echo "selected";
                                                                                                        } ?>><?= "" . $lcb['id_cabang'] . "-" . $lcb['kode_cabang']; ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </li>
                </ul>
            <?php } ?>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link refresh p-0" href="#">
                        <span id="spinner"></span>
                        <span class="btn btn-sm btn-outline-success">Sync</span>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link p-0 pr-2 pl-2" href="<?= $this->BASE_URL ?>Login/logout" role="button">
                        <span class="btn btn-sm btn-outline-dark">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-cyan">
            <div class="sidebar">
                <div class="user-panel mt-2 pb-2 mb-2 d-flex">
                    <div class="info">
                        <span class="btn btn-sm btn-light"> <i class="fas fa-user-circle"></i> <?= $this->nama_user . " #" . $this->id_laundry . "-" . $this->id_cabang ?></span>
                    </div>
                </div>

                <?php if ($this->id_privilege >= 100) { ?>
                    <div class="user-panel pb-2 mb-2 d-flex">
                        <div class="info mr-auto">
                            <span id="btnKasir" class="btn btn-sm <?= $classKasir ?> pr-3 pl-3"><i class="fas fa-user-alt"></i> Kasir</span>
                        </div>
                        <div class="info">
                            <span id="btnAdmin" class="btn btn-sm <?= $classAdmin ?> pr-3 pl-3"><i class="fas fa-user-shield"></i> Admin</span>
                        </div>
                    </div>
                <?php } ?>

                <!-- MENU KASIR --------------------------------->
                <nav>
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <?php if ($this->id_laundry > 0 && $this->id_cabang > 0) {
                        ?>
                            <ul id="nav_kasir" class="nav nav-pills nav-sidebar flex-column <?= $hideKasir ?>">
                                <li class="nav-item ">
                                    <a href="<?= $this->BASE_URL ?>Penjualan/i" class="nav-link 
                <?php if (strpos($title, 'Buka Order') !== FALSE) : echo 'active';
                            endif ?>">
                                        <i class="nav-icon fas fa-cash-register"></i>
                                        <p>
                                            Buka Order [ <b><?= $this->dCabang['kode_cabang'] ?></b> ]
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item 
                <?php if (strpos($title, 'Data Order') !== FALSE) {
                                echo 'menu-is-opening menu-open';
                            } ?>">
                                    <a href="#" class="nav-link 
                <?php if (strpos($title, 'Data Order') !== FALSE) {
                                echo 'active';
                            } ?>">
                                        <i class="nav-icon far fa-clock"></i>
                                        <p>
                                            Order Proses
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Data Order') !== FALSE) {
                                echo 'block;';
                            } else {
                                echo 'none;';
                            } ?>;">
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Antrian/i/1" class="nav-link 
                    <?php if ($title == 'Data Order Proses H7-') {
                                echo 'active';
                            } ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    <b>Terkini</b>
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Antrian/i/6" class="nav-link 
                    <?php if ($title == 'Data Order Proses H7+') {
                                echo 'active';
                            } ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    <b>> 7 Hari</b>
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Antrian/i/7" class="nav-link 
                    <?php if ($title == 'Data Order Proses H30+') {
                                echo 'active';
                            } ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    <b>> 30 Hari</b>
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Antrian/i/8" class="nav-link 
                    <?php if ($title == 'Data Order Proses H365+') {
                                echo 'active';
                            } ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    <b>> 1 Tahun</b>
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item 
                <?php if (strpos($title, 'Operasi Order') !== FALSE) {
                                echo 'menu-is-opening menu-open';
                            } ?>">
                                    <a href="#" class="nav-link 
                <?php if (strpos($title, 'Operasi Order') !== FALSE) {
                                echo 'active';
                            } ?>">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>
                                            Order Operasi
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Operasi Order') !== FALSE) {
                                echo 'block;';
                            } else {
                                echo 'none;';
                            } ?>;">
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Operasi/i/1/0/0" class="nav-link 
                    <?php if ($title == 'Operasi Order Proses') {
                                echo 'active';
                            } ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    Proses
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Operasi/i/2/0/0" class="nav-link 
                    <?php if ($title == 'Operasi Order Tuntas') {
                                echo 'active';
                            } ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    Tuntas
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item 
                <?php if (strpos($title, 'Member') !== FALSE) {
                                echo 'menu-is-opening menu-open';
                            } ?>">
                                    <a href="#" class="nav-link 
                <?php if (strpos($title, 'Member') !== FALSE) {
                                echo 'active';
                            } ?>">
                                        <i class="nav-icon fas fa-book"></i>
                                        <p>
                                            Deposit Member
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Member') !== FALSE) {
                                echo 'block;';
                            } else {
                                echo 'none;';
                            } ?>;">
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Member/tampil_rekap" class="nav-link 
                <?php if (strpos($title, 'List Member') !== FALSE) : echo 'active';
                            endif ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    List Member
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Member/tambah_paket/0" class="nav-link 
                <?php if (strpos($title, '(+) Paket Member') !== FALSE) : echo 'active';
                            endif ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    (+) Paket Member
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item ">
                                    <a href="<?= $this->BASE_URL ?>Data_List/i/pelanggan" class="nav-link 
                <?php if (strpos($title, 'Pelanggan') !== FALSE) : echo 'active';
                            endif ?>">
                                        <i class="nav-icon fas fa-address-book"></i>
                                        <p>
                                            Pelanggan
                                        </p>
                                    </a>
                                </li>

                                <?php if (count($this->listCabang) > 1) { ?>
                                    <li class="nav-item ">
                                        <a href="<?= $this->BASE_URL ?>Operan" class="nav-link 
                  <?php if (strpos($title, 'Operan') !== FALSE) : echo 'active';
                                    endif ?>">
                                            <i class="nav-icon fas fa-random"></i>
                                            <p>
                                                Operan
                                            </p>
                                        </a>
                                    </li>
                                <?php } ?>

                                <li class="nav-item ">
                                    <a href="<?= $this->BASE_URL ?>Kinerja" class="nav-link 
                <?php if (strpos($title, 'Kinerja') !== FALSE) : echo 'active';
                            endif ?>">
                                        <i class="nav-icon fas fa-id-card-alt"></i>
                                        <p>
                                            Data Kinerja
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a href="<?= $this->BASE_URL ?>Kas" class="nav-link 
                <?php if (strpos($title, 'Kas') !== FALSE) : echo 'active';
                            endif ?>">
                                        <i class="nav-icon fas fa-wallet"></i>
                                        <p>
                                            Kas
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item ">
                                    <a href="<?= $this->BASE_URL ?>PackLabel" class="nav-link 
                <?php if (strpos($title, 'PackLabel') !== FALSE) : echo 'active';
                            endif ?>">
                                        <i class="nav-icon fas fa-tag"></i>
                                        <p>
                                            Pack Label
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item 
                <?php if (strpos($title, 'MDL Menu') !== FALSE) {
                                echo 'menu-is-opening menu-open';
                            } ?>">
                                    <a href="#" class="nav-link 
                <?php if (strpos($title, 'MDL Menu') !== FALSE) {
                                echo 'active';
                            } ?>">
                                        <i class="nav-icon fas fa-server"></i>
                                        <p>
                                            MDL Menu
                                            <i class="fas fa-angle-left right"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'MDL Menu') !== FALSE) {
                                echo 'block;';
                            } else {
                                echo 'none;';
                            } ?>;">
                                        <li class="nav-item">
                                            <a href="<?= $this->BASE_URL ?>Download" class="nav-link 
                    <?php if ($title == 'MDL Menu | Download') {
                                echo 'active';
                            } ?>">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>
                                                    MDL Download Center
                                                </p>
                                            </a>
                                        </li>
                                        <?php if ($this->id_cabang > 0) { ?>
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Subscription" class="nav-link 
                    <?php if ($title == 'MDL Menu | Subscription') {
                                                echo 'active';
                                            } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        MDL Subscription
                                                    </p>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            </ul>
                        <?php
                        } ?>


                        <!-- BATAS MENU KASIR -->

                        <!-- INI MENU ADMIN ----------------------------------------->
                        <?php if ($this->id_privilege >= 100) { ?>
                            <ul id="nav_admin" class="nav nav-pills nav-sidebar flex-column <?= $hideAdmin ?>">
                                <li class="nav-header">PANEL ADMIN</li>
                                <!-- JIKA SUDAH PUNYA LAUNDRY DAN CABANG ------------------------------->
                                <?php if ($this->id_laundry > 0 && $this->id_cabang > 0) { ?>
                                    <li class="nav-item ">
                                        <a href="<?= $this->BASE_URL ?>AdminApproval/index/Setoran" class="nav-link 
                <?php if (strpos($title, 'Approval') !== FALSE) : echo 'active';
                                    endif ?>">
                                            <i class="nav-icon fas fa-tasks"></i>
                                            <p>
                                                Admin Approval
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item 
                <?php if (strpos($title, 'Rekap') !== FALSE) {
                                        echo 'menu-is-opening menu-open';
                                    } ?>">
                                        <a href="#" class="nav-link 
                <?php if (strpos($title, 'Rekap') !== FALSE) {
                                        echo 'active';
                                    } ?>">
                                            <i class="nav-icon fas fa-chart-line"></i>
                                            <p>
                                                Rekap
                                                <i class="fas fa-angle-left right"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Rekap') !== FALSE) {
                                        echo 'block;';
                                    } else {
                                        echo 'none;';
                                    } ?>;">
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Rekap/i/1" class="nav-link 
                    <?php if ($title == 'Harian Cabang - Rekap') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Laba/Rugi Cabang Harian
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Rekap/i/2" class="nav-link 
                    <?php if ($title == 'Bulanan Cabang - Rekap') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Laba/Rugi Cabang Bulanan
                                                    </p>
                                                </a>
                                            </li>

                                            <?php if (count($this->listCabang) > 1) { ?>
                                                <li class="nav-item">
                                                    <a href="<?= $this->BASE_URL ?>Rekap/i/3" class="nav-link 
                    <?php if ($title == 'Bulanan Laundry - Rekap') {
                                                    echo 'active';
                                                } ?>">
                                                        <i class="far fa-circle nav-icon"></i>
                                                        <p>
                                                            Laba/Rugi Laundry Bulanan
                                                        </p>
                                                    </a>
                                                </li>

                                            <?php } ?>

                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Gaji" class="nav-link 
                    <?php if ($title == 'Gaji Bulanan - Rekap') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Gaji Bulanan
                                                    </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php }
                                // ===============================================================================

                                //ADMIN SETTING DATA USAHA
                                if ($this->id_privilege == 100) { ?>
                                    <li class="nav-item ">
                                        <a href="<?= $this->BASE_URL ?>Laundry_List" class="nav-link 
  <?php if ($title == 'Data Laundry') : echo 'active';
                                    endif ?>">
                                            <i class="nav-icon fas fa-store-alt"></i>
                                            <p>
                                                Laundry
                                            </p>
                                        </a>
                                    </li>
                                <?php }

                                // JIKA SUDAH PUNYA LAUNDRY =========================
                                if ($this->id_laundry > 0) { ?>


                                    <li class="nav-item 
                <?php if (strpos($title, 'Poin') !== FALSE) {
                                        echo 'menu-is-opening menu-open';
                                    } ?>">
                                        <a href="#" class="nav-link 
                <?php if (strpos($title, 'Poin') !== FALSE) {
                                        echo 'active';
                                    } ?>">
                                            <i class="nav-icon fas fa-coins"></i>
                                            <p>
                                                Poin
                                                <i class="fas fa-angle-left right"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Poin') !== FALSE) {
                                        echo 'block;';
                                    } else {
                                        echo 'none;';
                                    } ?>;">
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Poin/menu" class="nav-link 
                    <?php if ($title == 'Poin') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Poin Pelanggan
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>SetPoin/i" class="nav-link 
                    <?php if ($title == 'Poin Set') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Poin Set
                                                    </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="nav-item ">
                                        <a href="<?= $this->BASE_URL ?>Cabang_List" class="nav-link 
                  <?php if ($title == 'Data Cabang') : echo 'active';
                                    endif ?>">
                                            <i class="nav-icon fas fa-store"></i>
                                            <p>
                                                Cabang
                                            </p>
                                        </a>
                                    </li>

                                    <li class="nav-item 
                <?php if (strpos($title, 'Item') !== FALSE) {
                                        echo 'menu-is-opening menu-open';
                                    } ?>">
                                        <a href="#" class="nav-link 
                <?php if (strpos($title, 'Item') !== FALSE) {
                                        echo 'active';
                                    } ?>">
                                            <i class="nav-icon fas fa-list"></i>
                                            <p>
                                                Item List
                                                <i class="fas fa-angle-left right"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Item') !== FALSE) {
                                        echo 'block;';
                                    } else {
                                        echo 'none;';
                                    } ?>;">
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Data_List/i/item" class="nav-link 
              <?php if ($title == 'Item Laundry') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Item Laundry
                                                    </p>
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Data_List/i/item_pengeluaran" class="nav-link 
              <?php if ($title == 'Item Pengeluaran') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Pengeluaran
                                                    </p>
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Data_List/i/surcas" class="nav-link 
              <?php if ($title == 'Surcharge') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Surcharge
                                                    </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="nav-item 
                <?php if (strpos($title, 'Kelompok') !== FALSE) {
                                        echo 'menu-is-opening menu-open';
                                    } ?>">
                                        <a href="#" class="nav-link 
                <?php if (strpos($title, 'Kelompok') !== FALSE) {
                                        echo 'active';
                                    } ?>">
                                            <i class="nav-icon fas fa-layer-group"></i>
                                            <p>
                                                Kelompok Item
                                                <i class="fas fa-angle-left right"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Kelompok') !== FALSE) {
                                        echo 'block;';
                                    } else {
                                        echo 'none;';
                                    } ?>;">
                                            <?php foreach ($this->dPenjualan as $a) {
                                                if ($a['id_penjualan_jenis'] < 5) { ?>
                                                    <li class="nav-item">
                                                        <a href="<?= $this->BASE_URL ?>SetGroup/i/<?= $a['id_penjualan_jenis'] ?>" class="nav-link 
                    <?php if ($title == 'Kelompok ' . $a['penjualan_jenis']) {
                                                        echo 'active';
                                                    } ?>">
                                                            <i class="far fa-circle nav-icon"></i>
                                                            <p>
                                                                <?= $a['penjualan_jenis'] ?>
                                                            </p>
                                                        </a>
                                                    </li>
                                            <?php }
                                            } ?>
                                        </ul>
                                    </li>

                                    <li class="nav-item 
                <?php if (strpos($title, 'Harga') !== FALSE) {
                                        echo 'menu-is-opening menu-open';
                                    } ?>">
                                        <a href="#" class="nav-link 
                <?php if (strpos($title, 'Harga') !== FALSE) {
                                        echo 'active';
                                    } ?>">
                                            <i class="nav-icon fas fa-tags"></i>
                                            <p>
                                                Harga
                                                <i class="fas fa-angle-left right"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Harga') !== FALSE) {
                                        echo 'block;';
                                    } else {
                                        echo 'none;';
                                    } ?>;">
                                            <?php foreach ($this->dPenjualan as $a) {
                                                if ($a['id_penjualan_jenis'] < 5) { ?>
                                                    <li class="nav-item">
                                                        <a href="<?= $this->BASE_URL ?>SetHarga/i/<?= $a['id_penjualan_jenis'] ?>" class="nav-link 
                    <?php if ($title == 'Harga ' . $a['penjualan_jenis']) {
                                                        echo 'active';
                                                    } ?>">
                                                            <i class="far fa-circle nav-icon"></i>
                                                            <p>
                                                                <?= $a['penjualan_jenis'] ?>
                                                            </p>
                                                        </a>
                                                    </li>
                                            <?php }
                                            } ?>
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>SetHargaPaket" class="nav-link 
                    <?php if ($title == 'Harga Paket') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Paket Member
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>SetDiskon/i" class="nav-link 
                    <?php if ($title == 'Harga Diskon Kuantitas') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Diskon Kuantitas
                                                    </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                <?php }

                                // JIKA SUDAH PUNYA CABANG
                                if ($this->id_cabang > 0) { ?>
                                    <li class="nav-item 
                <?php if (strpos($title, 'Karyawan') !== FALSE) {
                                        echo 'menu-is-opening menu-open';
                                    } ?>">
                                        <a href="#" class="nav-link 
                <?php if (strpos($title, 'Karyawan') !== FALSE) {
                                        echo 'active';
                                    } ?>">
                                            <i class="nav-icon fas fa-user-friends"></i>
                                            <p>
                                                Karyawan
                                                <i class="fas fa-angle-left right"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview" style="display: 
                <?php if (strpos($title, 'Karyawan') !== FALSE) {
                                        echo 'block;';
                                    } else {
                                        echo 'none;';
                                    } ?>;">
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Data_List/i/user" class="nav-link 
                    <?php if ($title == 'Karyawan Aktif') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Aktif
                                                    </p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="<?= $this->BASE_URL ?>Data_List/i/userDisable" class="nav-link 
                    <?php if ($title == 'Karyawan Non Aktif') {
                                        echo 'active';
                                    } ?>">
                                                    <i class="far fa-circle nav-icon"></i>
                                                    <p>
                                                        Non Aktif
                                                    </p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                            </ul>
                    <?php
                                }
                            } ?>
                    </ul>
                </nav>
            </div>
        </aside>

        <style>
            .content-wrapper {
                max-height: 100px;
                overflow: auto;
                display: inline-block;
            }
        </style>

        <div class="content-wrapper pt-2" style="min-width: 410px;">
            <?php
            if ($_SESSION['masa'] <> 1 && $this->id_cabang > 0) {
                $today = strtotime(date('Y-m-d'));
                if (isset($this->langganan['toDate'])) {
                    $aktifTo = ($this->langganan['toDate']);
                    $aktifTo = strtotime($aktifTo);
                    $jatem = strtotime("+20 day", $aktifTo);
                    $jatemShow = date("d-m-Y", $jatem);
                } else {
                    $registered = strtotime($this->cabang_registerd);
                    $aktifTo =  strtotime("+30 day", $registered);
                    $jatem = strtotime("+20 day", $aktifTo);
                    $jatemShow = date("d-m-Y", $jatem);
                }

                if ($aktifTo >= $today) {
                    $_SESSION['masa'] = 1;
                } else {
                    if ($today <= $jatem) {
                        $_SESSION['masa'] = 2;
                    }
                }
            }

            if ($this->id_cabang > 0) {
                switch ($_SESSION['masa']) {
                    case 2: ?>
                        <div class="content sticky-top">
                            <div class="container-fluid">
                                <div class="alert alert-warning" role="alert">
                                    Aplikasi dalam masa TENGGANG, lakukan pembayaran sebelum Tanggal <b><?= $jatemShow ?></b>, demi menghindari pembekuan transaksi.
                                </div>
                            </div>
                        </div>
                    <?php
                        break;
                    case 0: ?>
                        <div class="content sticky-top">
                            <div class="container-fluid">
                                <div class="alert alert-danger" role="alert">
                                    Aplikasi EXPIRED, lakukan pembayaran agar dapat melakukan transaksi kembali.
                                </div>
                            </div>
                        </div>
            <?php break;
                }
            } ?>

            <script src="<?= $this->ASSETS_URL ?>plugins/adminLTE-3.1.0/jquery/jquery.min.js"></script>
            <script src="<?= $this->ASSETS_URL ?>plugins/adminLTE-3.1.0/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="<?= $this->ASSETS_URL ?>plugins/adminLTE-3.1.0/js/adminlte.js"></script>

            <script>
                $(document).ready(function() {
                    $(".waitReady").removeClass("d-none");
                });

                $("a.refresh").on('click', function() {
                    $.ajax('<?= $this->BASE_URL ?>Data_List/synchrone', {
                        beforeSend: function() {
                            $('span#spinner').addClass('spinner-border spinner-border-sm');
                        },
                        success: function(data, status, xhr) {
                            location.reload(true);
                        }
                    });
                });

                $("span#btnKasir").click(function() {
                    $.ajax({
                        url: "<?= $this->BASE_URL ?>Login/log_mode",
                        data: {
                            mode: 0
                        },
                        type: "POST",
                        dataType: 'html',
                        success: function(res) {
                            $("#nav_kasir").removeClass('d-none');
                            $("#nav_admin").addClass('d-none');

                            $("span#btnKasir").removeClass("btn-secondary").addClass("btn-success");
                            $("span#btnAdmin").removeClass("btn-danger").addClass("btn-secondary");
                        },
                    });
                });

                $("span#btnAdmin").click(function() {
                    $.ajax({
                        url: '<?= $this->BASE_URL ?>Login/log_mode',
                        data: {
                            mode: 1
                        },
                        type: "POST",
                        dataType: 'html',
                        success: function(response) {
                            $("#nav_kasir").addClass('d-none');
                            $("#nav_admin").removeClass('d-none');

                            $("span#btnKasir").removeClass("btn-success").addClass("btn-secondary");
                            $("span#btnAdmin").removeClass("btn-secondary").addClass("btn-danger");
                        },
                    });
                })

                $("select#selectCabang").on("change", function() {
                    var idCabang = $(this).val();
                    $.ajax({
                        url: '<?= $this->BASE_URL ?>Cabang_List/selectCabang',
                        data: {
                            id: idCabang
                        },
                        beforeSend: function() {
                            $('span#spinner').addClass('spinner-border spinner-border-sm');
                        },
                        type: "POST",
                        success: function(response) {
                            location.reload(true);
                        },
                    });
                });

                var time = new Date().getTime();
                $(document.body).bind("mousemove keypress", function(e) {
                    time = new Date().getTime();
                });

                function refresh() {
                    if (new Date().getTime() - time >= 420000)
                        window.location.reload(true);
                    else
                        setTimeout(refresh, 10000);
                }
                setTimeout(refresh, 10000);
            </script>