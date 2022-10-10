<?php
$notifSenderPath = "https://www.mdl.my.id/notifSender.zip";
?>

<div class="content">
    <div class="container-fluid">
        <div>
            <div class="d-flex pb-0 mb-1 mt-1">
                <div class="mr-auto">
                    <h5>Admin Approval</h5>
                </div>
            </div>

            <div id="accordion">
                <div class="card mb-1">
                    <?php $countSetoran = count($data['setoran']) ?>
                    <div style="background-color: #FFFFF0;" class="card-header p-0 pr-1 collapsed" data-toggle="collapse" data-target="#collapseSetoran" aria-expanded="false" aria-controls="collapseSetoran" id="headingSetoran">
                        <h5 class="mb-0">
                            <span class="btn text-primary">
                                Setoran Kas
                            </span>
                            <span class="float-right mt-2 mr-2 badge badge-danger">
                                <small><b><?= $countSetoran ?></b></small>
                            </span>
                        </h5>
                    </div>
                    <div id="collapseSetoran" class="collapse" aria-labelledby="headingSetoran" data-parent="#accordion">
                        <div class="card-body pl-1 pr-1 pb-0 pt-0">
                            <?php foreach ($data['setoran'] as $a) {
                                $id = $a['id_kas'];
                                $f1 = $a['insertTime'];
                                $f2 = $a['note'];
                                $f3 = $a['id_user'];
                                $f4 = $a['jumlah'];
                                $f17 = $a['id_client'];
                                $karyawan = '';
                                foreach ($this->userMerge as $c) {
                                    if ($c['id_user'] == $f3) {
                                        $karyawan = $c['nama_user'];
                                    }
                                }
                            ?>
                                <div class="row p-0">
                                    <div class="col p-0 pl-2 pr-3 border-bottom">
                                        <table class="m-0 mr-1 ml-1 table-sm w-100">
                                            <tr>
                                                <?php
                                                echo "<td colspan='2' class='w-100'>" . $karyawan . " #" . $id . " <small>" . $f1 . "</small></span><br>
                <span data-mode='4' data-id_value='" . $id . "' data-value='" . $f4 . "'></span><span>" . strtoupper($f2) . ", </span> 
                <b><span class='text-success'>Rp" . number_format($f4) . "</span></b></td>";
                                                ?>
                                                <td class="text-right">
                                                    <button class="rounded btn-outline-secondary badge-secondary nTunai" data-id="<?= $id ?>" data-target="<?= $this->BASE_URL; ?>Setoran/operasi/4"><i class="fas fa-times-circle"></i></button>
                                                </td>
                                                <td class='text-right'>
                                                    <button class="rounded btn-outline-success badge-success nTunai" data-id="<?= $id ?>" data-target="<?= $this->BASE_URL; ?>Setoran/operasi/3"><i class="fas fa-check-circle"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- NONT TUNAI -->
                <div class="card mb-1">
                    <?php $countNonTunai = count($data['nonTunai']) ?>
                    <div style="background-color: #FFFFF0;" class="card-header p-0 pr-1 collapsed" data-toggle="collapse" data-target="#collapseNonTunai" aria-expanded="false" aria-controls="collapseNonTunai" id="headingNonTunai">
                        <h5 class="mb-0">
                            <span class="btn text-primary">
                                Transaksi Non Tunai
                            </span>
                            <span class="float-right mt-2 mr-2 badge badge-danger">
                                <small><b><?= $countNonTunai ?></b></small>
                            </span>
                        </h5>
                    </div>
                    <div id="collapseNonTunai" class="collapse" aria-labelledby="headingNonTunai" data-parent="#accordion">
                        <div class="card-body pl-1 pr-1 pb-0 pt-0">
                            <?php foreach ($data['nonTunai'] as $a) {
                                $sts = $a['status_mutasi'];
                                if ($sts == 2) {
                                    foreach ($this->dStatusMutasi as $st) {
                                        if ($sts == $st['id_status_mutasi']) {
                                            $stBayar = $st['status_mutasi'];
                                        }
                                    }

                                    $id = $a['id_kas'];
                                    $f1 = $a['insertTime'];
                                    $f2 = $a['note'];
                                    $f3 = $a['id_user'];
                                    $f4 = $a['jumlah'];
                                    $f17 = $a['id_client'];
                                    $jenisT = $a['jenis_transaksi'];

                                    $karyawan = '';
                                    foreach ($this->userMerge as $c) {
                                        if ($c['id_user'] == $f3) {
                                            $karyawan = $c['nama_user'];
                                        }
                                    }

                                    $pelanggan = '';

                                    foreach ($this->pelanggan as $c) {
                                        if ($c['id_pelanggan'] == $f17) {
                                            $pelanggan = "<span class='text-info'>" . strtoupper($c['nama_pelanggan']) . "</span>";
                                            switch ($jenisT) {
                                                case 1:
                                                    $pelanggan = "Laundry, " . $pelanggan;
                                                    break;
                                                case 3:
                                                    $pelanggan = "Member, " . $pelanggan;
                                                    break;
                                                case 5:
                                                    $pelanggan = "Kasbon, " . $pelanggan;
                                                    break;
                                            }
                                        }
                                    }
                                }
                            ?>
                                <div class="row p-0">
                                    <div class="col p-0 pl-2 pr-3 border-bottom">
                                        <table class="m-0 mr-1 ml-1 table-sm w-100">
                                            <tr>
                                                <?php
                                                echo "<td colspan='2' class='w-100'><b>" . $pelanggan . "</b><br>" . $karyawan . " #" . $id . " <small>" . $f1 . "</small></span><br>
                <span data-mode='4' data-id_value='" . $id . "' data-value='" . $f4 . "'></span><span>" . strtoupper($f2) . ", </span> 
                <b><span class='text-success'>Rp" . number_format($f4) . "</span></b></td>";
                                                ?>
                                                <td class="text-right">
                                                    <button class="rounded btn-outline-secondary badge-secondary nTunai" data-id="<?= $id ?>" data-target="<?= $this->BASE_URL; ?>Setoran/operasi/4"><i class="fas fa-times-circle"></i></button>
                                                </td>
                                                <td class='text-right'>
                                                    <button class="rounded btn-outline-success badge-success nTunai" data-id="<?= $id ?>" data-target="<?= $this->BASE_URL; ?>Setoran/operasi/3"><i class="fas fa-check-circle"></i></button>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- HAPUS ORDER -->
                <div class="card mb-1">
                    <?php $countNonTunai = count($data['hapusOrder']) ?>
                    <div style="background-color: #FFFFF0;" class="card-header p-0 pr-1 collapsed" data-toggle="collapse" data-target="#collapseDelOrder" aria-expanded="false" aria-controls="collapseDelOrder" id="headingDelOrder">
                        <h5 class="mb-0">
                            <span class="btn text-primary">
                                Hapus Order
                            </span>
                            <span class="float-right mt-2 mr-2 badge badge-danger">
                                <small><b><?= $countNonTunai ?></b></small>
                            </span>
                        </h5>
                    </div>
                    <div id="collapseDelOrder" class="collapse" aria-labelledby="headingDelOrder" data-parent="#accordion">
                        <div class="card-body p-1">
                            <div class="row p-0">
                                <div class="col pt-1">
                                    <div class="card p-2 mb-2">
                                        <div class="row">
                                            <div class="col">
                                                Related : <span id="forbidden"></span>
                                            </div>
                                            <div class="col">
                                                <button class="badge-danger rounded btn-outline-danger clearHapus float-right">Hapus Semua</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card p-2">
                                        <table class="table table-sm w-100 p-0 m-0">
                                            <?php
                                            $arrRef = array();
                                            $prevRef = '';
                                            $countRef = 0;
                                            foreach ($data['hapusOrder'] as $a) {
                                                $ref = $a['no_ref'];
                                                if ($prevRef <> $a['no_ref']) {
                                                    $countRef = 0;
                                                    $countRef++;
                                                    $arrRef[$ref] = $countRef;
                                                } else {
                                                    $countRef++;
                                                    $arrRef[$ref] = $countRef;
                                                }
                                                $prevRef = $ref;
                                            }
                                            $no = 0;
                                            $urutRef = 0;
                                            $arrCount = 0;
                                            $enHapus = true;
                                            $arrNoref = array();
                                            $arrID = array();

                                            $forbiddenCount = 0;

                                            foreach ($data['hapusOrder'] as $a) {
                                                $no++;
                                                $id = $a['id_penjualan'];
                                                array_push($arrID, $id);

                                                $f10 = $a['id_penjualan_jenis'];
                                                $f3 = $a['id_item_group'];
                                                $f4 = $a['list_item'];
                                                $f5 = $a['list_layanan'];
                                                $f11 = $a['id_durasi'];
                                                $f6 = $a['qty'];
                                                $f7 = $a['harga'];
                                                $f8 = $a['note'];
                                                $f9 = $a['id_user'];
                                                $f1 = $a['insertTime'];
                                                $f12 = $a['hari'];
                                                $f13 = $a['jam'];
                                                $f14 = $a['diskon_qty'];
                                                $f15 = $a['diskon_partner'];
                                                $f16 = $a['min_order'];
                                                $f17 = $a['id_pelanggan'];
                                                $f18 = $a['id_user'];
                                                $noref = $a['no_ref'];
                                                $letak = $a['letak'];

                                                $pelanggan = '';
                                                $no_pelanggan = '';
                                                foreach ($this->pelanggan as $c) {
                                                    if ($c['id_pelanggan'] == $f17) {
                                                        $pelanggan = $c['nama_pelanggan'];
                                                        $no_pelanggan = $c['nomor_pelanggan'];
                                                        $modeNotif = $c['id_notif_mode'];
                                                    }
                                                }

                                                $karyawan = '';
                                                foreach ($this->user as $c) {
                                                    if ($c['id_user'] == $f18) {
                                                        $karyawan = $c['nama_user'];
                                                    }
                                                }

                                                $durasi = "";
                                                foreach ($this->dDurasi as $b) {
                                                    if ($b['id_durasi'] == $f11) {
                                                        $durasi = $b['durasi'];
                                                    }
                                                }

                                                if ($no == 1) {
                                                    $enHapus = true;
                                                    $urutRef++;
                                                    echo "<tr class='table-success' id='tr" . $id . "'>";
                                                    echo "<td colspan='2'> 
                    <b>" . strtoupper($pelanggan) . "</b></td>";
                                                    echo "<td nowrap colspan='2'>" . substr($f1, 5, 11) . "<br><small>" . $f8 . "</small></td>";
                                                    echo "<td class='text-right'><small>CS: " . $karyawan . "</small></td>";
                                                    echo "</tr>";
                                                    $subTotal = 0;
                                                }

                                                $penjualan = "";
                                                $satuan = "";
                                                foreach ($this->dPenjualan as $l) {
                                                    if ($l['id_penjualan_jenis'] == $f10) {
                                                        $penjualan = $l['penjualan_jenis'];
                                                        foreach ($this->dSatuan as $sa) {
                                                            if ($sa['id_satuan'] == $l['id_satuan']) {
                                                                $satuan = $sa['nama_satuan'];
                                                            }
                                                        }
                                                    }
                                                }

                                                $show_qty = 0;
                                                $qty_real = 0;
                                                if ($f6 < $f16) {
                                                    $qty_real = $f16;
                                                    $show_qty = $f6 . $satuan . " (Min. " . $f16 . $satuan . ")";
                                                } else {
                                                    $qty_real = $f6;
                                                    $show_qty = $f6 . $satuan;
                                                }

                                                $kategori = "";
                                                foreach ($this->itemGroup as $b) {
                                                    if ($b['id_item_group'] == $f3) {
                                                        $kategori = $b['item_kategori'];
                                                    }
                                                }


                                                $list_layanan = "";
                                                $arrList_layanan = unserialize($f5);
                                                $doneLayanan = 0;
                                                $countLayanan = count($arrList_layanan);
                                                foreach ($arrList_layanan as $b) {
                                                    $check = 0;
                                                    foreach ($this->dLayanan as $c) {
                                                        if ($c['id_layanan'] == $b) {
                                                            foreach ($data['operasi_order'] as $o) {
                                                                if ($o['id_penjualan'] == $id && $o['jenis_operasi'] == $b) {
                                                                    $user = "";
                                                                    $check++;
                                                                    foreach ($this->user as $p) {
                                                                        if ($p['id_user'] == $o['id_user_operasi']) {
                                                                            $user = $p['nama_user'];
                                                                        }
                                                                    }
                                                                    $list_layanan = $list_layanan . '<b><i class="fas fa-check-circle text-success"></i> ' . $c['layanan'] . "</b> " . $user . " <span style='white-space: pre;'>(" . substr($o['insertTime'], 5, 11) . ")</span><br>";
                                                                    $doneLayanan++;
                                                                    $forbiddenCount++;
                                                                    $enHapus = false;
                                                                }
                                                            }
                                                            if ($check == 0) {
                                                                $list_layanan = $list_layanan . "<span class='addOperasi mb-1 rounded'>" . $c['layanan'] . "</span><br>";
                                                            }
                                                        }
                                                    }
                                                }

                                                $diskon_qty = $f14;
                                                $diskon_partner = $f15;

                                                $show_diskon_qty = "";
                                                if ($diskon_qty > 0) {
                                                    $show_diskon_qty = $diskon_qty . "%";
                                                }
                                                $show_diskon_partner = "";
                                                if ($diskon_partner > 0) {
                                                    $show_diskon_partner = $diskon_partner . "%";
                                                }
                                                $plus = "";
                                                if ($diskon_qty > 0 && $diskon_partner > 0) {
                                                    $plus = " + ";
                                                }
                                                $show_diskon = $show_diskon_qty . $plus . $show_diskon_partner;

                                                $itemList = "";
                                                $itemListPrint = "";
                                                if (strlen($f4) > 0) {
                                                    $arrItemList = unserialize($f4);
                                                    $arrCount = count($arrItemList);
                                                    if ($arrCount > 0) {
                                                        foreach ($arrItemList as $key => $k) {
                                                            foreach ($this->dItem as $b) {
                                                                if ($b['id_item'] == $key) {
                                                                    $itemList = $itemList . "<span class='badge badge-light text-dark'>" . $b['item'] . "[" . $k . "]</span> ";
                                                                    $itemListPrint = $itemListPrint . $b['item'] . "[" . $k . "]";
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                                $total = ($f7 * $qty_real) - (($f7 * $qty_real) * ($f14 / 100));
                                                $subTotal = $subTotal + $total;

                                                foreach ($arrRef as $key => $m) {
                                                    if ($key == $noref) {
                                                        $arrCount = $m;
                                                    }
                                                }

                                                $show_total = "";
                                                $show_total_print = "";

                                                if (strlen($show_diskon) > 0) {
                                                    $show_total = "<del>" . number_format($f7 * $qty_real) . "</del><br>" . number_format($total);
                                                    $show_total_print = "-" . $show_diskon . " <del>" . number_format($f7 * $qty_real) . "</del> " . number_format($total);
                                                } else {
                                                    $show_total = number_format($total);
                                                    $show_total_print = number_format($total);
                                                }

                                                $showNote = "";
                                                if (strlen($f8) > 0) {
                                                    $showNote = $f8;
                                                }

                                                echo "<tr id='tr" . $id . "'>";
                                                echo "</td>";
                                                echo "<td>" . $id . " | " . $penjualan . "<br>" . $kategori . "</td>";
                                                echo "<td nowrap>" . $list_layanan . "</td>";
                                                echo "<td class='text-right'>" . $show_qty . "<br>" . $show_diskon . "</td>";
                                                echo "<td>" . $durasi . "</td>";
                                                echo "<td class='text-right'>Rp" . $show_total . "</td>";
                                                echo "</tr>";

                                                $showMutasi = "";
                                                $userKas = "";
                                                $totalBayar = 0;
                                                foreach ($data['kas_order'] as $ka) {
                                                    if ($ka['ref_transaksi'] == $noref) {
                                                        foreach ($this->user as $usKas) {
                                                            if ($usKas['id_user'] == $ka['id_user']) {
                                                                $userKas = $usKas['nama_user'];
                                                            }
                                                        }
                                                        $showMutasi = $showMutasi . number_format($ka['jumlah']) . " | " . $userKas . " (" . substr($ka['insertTime'], 5, 11) . ")<br>";
                                                        $totalBayar = $totalBayar + $ka['jumlah'];
                                                    }
                                                }

                                                if ($totalBayar > 0) {
                                                    $enHapus = false;
                                                }

                                                $sisaTagihan = $subTotal - $totalBayar;
                                                $showSisa = "";
                                                if ($sisaTagihan < $subTotal && $sisaTagihan > 0) {
                                                    $showSisa = "(Sisa Rp" . $sisaTagihan . ")";
                                                }

                                                if ($arrCount == $no) {

                                                    //SURCAS
                                                    foreach ($data['surcas_order'] as $sca) {
                                                        if ($sca['no_ref'] == $noref) {
                                                            $forbiddenCount++;
                                                            array_push($arrNoref, $noref);
                                                        }
                                                    }

                                                    $buttonRestore = "<button data-ref='" . $noref . "' class='restoreRef badge-success mb-1 rounded btn-outline-success'><i class='fas fa-recycle'></i></button> ";
                                                    if ($totalBayar > 0) {
                                                        $forbiddenCount++;
                                                        array_push($arrNoref, $noref);
                                                    }

                                                    echo "<tr>";
                                                    echo "<td>" . $buttonRestore . "</td>";
                                                    echo "<td class='text-right' colspan='2'></td>";
                                                    echo "<td>" . $showSisa . "</td>";

                                                    echo "<td nowrap class='text-right'>";
                                                    if ($sisaTagihan <= 0) {
                                                    } else {
                                                        echo "<i class='fas fa-check-circle text-success'></i> ";
                                                    }
                                                    echo "<b>Rp" . number_format($subTotal) . "</b>";
                                                    echo "</td></tr>";
                                                    if ($totalBayar > 0) {
                                            ?>
                                                        <tr>
                                                            <td colspan="5" class="text-right"><?= $showMutasi ?></td>
                                                        </tr>
                                            <?php
                                                    }

                                                    $totalBayar = 0;
                                                    $sisaTagihan = 0;
                                                    $no = 0;
                                                    $subTotal = 0;
                                                    $listPrint = "";
                                                    $listNotif = "";
                                                    $enHapus = false;
                                                }
                                            }
                                            ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-1">
                    <?php $countNonTunai = count($data['depositHapus']) ?>
                    <div style="background-color: #FFFFF0;" class="card-header p-0 pr-1 collapsed" data-toggle="collapse" data-target="#collapseDelDeposit" aria-expanded="false" aria-controls="collapseDelDeposit" id="headingDelDeposit">
                        <h5 class="mb-0">
                            <span class="btn text-primary">
                                Hapus Deposit Member
                            </span>
                            <span class="float-right mt-2 mr-2 badge badge-danger">
                                <small><b><?= $countNonTunai ?></b></small>
                            </span>
                        </h5>
                    </div>
                    <div id="collapseDelDeposit" class="collapse" aria-labelledby="headingDelDeposit" data-parent="#accordion">
                        <div class="card-body p-1">
                            <div class="row p-0">
                                <div class="col pt-1">
                                    <div class="card p-2 mb-2">
                                        <div class="row">
                                            <div class="col">
                                                Related : <span id="forbidden"></span>
                                            </div>
                                            <div class="col">
                                                <button class="badge-danger rounded btn-outline-danger clearHapus float-right">Hapus Semua</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-0 p-0">
                                        <div class="card-body p-0 m-0">
                                            <?php
                                            $forbiddenCount  = 0;
                                            $arrID = array();
                                            $arrNoref = array();
                                            foreach ($data['depositHapus'] as $z) { ?>
                                                <div class="col p-0 m-0 rounded" style='max-width:470px;'>
                                                    <table class="table border-right table-sm w-100 m-0">
                                                        <?php
                                                        $id = $z['id_member'];
                                                        array_push($arrID, $id);
                                                        $harga = $z['harga'];
                                                        $id_user = $z['id_user'];
                                                        $kategori = "";
                                                        $layanan = "";
                                                        $durasi = "";
                                                        $unit = "";
                                                        $idPoin = $z['id_poin'];
                                                        $perPoin = $z['per_poin'];

                                                        $pelanggan = $z['id_pelanggan'];
                                                        $nama_pelanggan = "";
                                                        foreach ($this->pelanggan as $dp) {
                                                            if ($dp['id_pelanggan'] == $pelanggan) {
                                                                $nama_pelanggan = $dp['nama_pelanggan'];
                                                            }
                                                        }


                                                        $gPoin = 0;
                                                        $gPoinShow = "";
                                                        if ($idPoin > 0) {
                                                            $gPoin = floor($harga / $perPoin);
                                                            $gPoinShow = "<small class='text-success'>(+" . $gPoin . ")</small>";
                                                        }

                                                        $showMutasi = "";
                                                        $userKas = "";
                                                        foreach ($data['kas_hapus'] as $ka) {
                                                            if ($ka['ref_transaksi'] == $id) {
                                                                foreach ($this->userMerge as $usKas) {
                                                                    if ($usKas['id_user'] == $ka['id_user']) {
                                                                        $userKas = $usKas['nama_user'];
                                                                    }
                                                                }
                                                                $showMutasi = $showMutasi . "<br><small><b>#" . $ka['id_kas'] . " " . $userKas . "</b> " . substr($ka['insertTime'], 5, 11) . "</small> -Rp" . number_format($ka['jumlah']);
                                                            }
                                                        }

                                                        foreach ($this->harga as $a) {
                                                            if ($a['id_harga'] == $z['id_harga']) {
                                                                foreach ($this->dPenjualan as $dp) {
                                                                    if ($dp['id_penjualan_jenis'] == $a['id_penjualan_jenis']) {
                                                                        foreach ($this->dSatuan as $ds) {
                                                                            if ($ds['id_satuan'] == $dp['id_satuan']) {
                                                                                $unit = $ds['nama_satuan'];
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                foreach (unserialize($a['list_layanan']) as $b) {
                                                                    foreach ($this->dLayanan as $c) {
                                                                        if ($b == $c['id_layanan']) {
                                                                            $layanan = $layanan . " " . $c['layanan'];
                                                                        }
                                                                    }
                                                                }
                                                                foreach ($this->dDurasi as $c) {
                                                                    if ($a['id_durasi'] == $c['id_durasi']) {
                                                                        $durasi = $durasi . " " . $c['durasi'];
                                                                    }
                                                                }

                                                                foreach ($this->itemGroup as $c) {
                                                                    if ($a['id_item_group'] == $c['id_item_group']) {
                                                                        $kategori = $kategori . " " . $c['item_kategori'];
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        $historyBayar = array();
                                                        foreach ($data['kas_hapus'] as $k) {
                                                            if ($k['ref_transaksi'] == $id) {
                                                                array_push($historyBayar, $k['jumlah']);
                                                                array_push($arrNoref, $id);
                                                            }
                                                        }

                                                        $statusBayar = "";
                                                        $totalBayar = array_sum($historyBayar);
                                                        $showSisa = "";
                                                        $sisa = $harga;
                                                        $lunas = false;
                                                        $enHapus = true;
                                                        if ($totalBayar > 0) {
                                                            $forbiddenCount += 1;
                                                            $enHapus = false;
                                                            if ($totalBayar >= $harga) {
                                                                $lunas = true;
                                                                $statusBayar = "<b><i class='fas fa-check-circle text-success'></i></b>";
                                                            } else {
                                                                $sisa = $harga - $totalBayar;
                                                                $showSisa = "<b><i class='fas fa-exclamation-circle'></i> Sisa Rp" . number_format($sisa) . "</b>";
                                                                $lunas = false;
                                                            }
                                                        } else {
                                                            $lunas = false;
                                                        }
                                                        $buttonBayar = "<button data-ref='" . $id . "' data-harga='" . $sisa . "' class='btn badge badge-danger bayar' data-bs-toggle='modal' data-bs-target='#exampleModal2'>Bayar</button>";
                                                        if ($lunas == true) {
                                                            $buttonBayar = "";
                                                        }

                                                        $cs = "";
                                                        foreach ($this->userMerge as $uM) {
                                                            if ($uM['id_user'] == $id_user) {
                                                                $cs = $uM['nama_user'];
                                                            }
                                                        }

                                                        if ($this->id_privilege >= 100) {
                                                            $buttonHapus = "<button data-id='" . $id . "' class='restoreRef badge-success mb-1 rounded btn-outline-success'><i class='fas fa-recycle'></i></button> ";
                                                        } else {
                                                            $buttonHapus = "";
                                                        }

                                                        ?>
                                                        <tr>
                                                            <td class="p-1" nowrap><b><?= strtoupper($nama_pelanggan) ?></b><br><?= $z['insertTime'] ?><br><?= $kategori ?> * <?= $layanan ?> * <?= $durasi ?></td>
                                                            <td class="p-1 text-right" nowrap>CS: <?= $cs ?><br><b><?= $z['qty'] . $unit ?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="text-right p-1">
                                                                <span class="float-left"><?= $buttonHapus ?></span>
                                                                <span id="statusBayar<?= $id ?>"><?= $statusBayar ?></span>&nbsp;
                                                                <span class="float-right"><?= $gPoinShow ?> <b>Rp<?= number_format($harga) ?></b></span>
                                                                <span id="historyBayar<?= $id ?>"><?= $showMutasi ?></span>
                                                                </span><br><span id="sisa<?= $id ?>" class="text-danger"><?= $showSisa ?></span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>