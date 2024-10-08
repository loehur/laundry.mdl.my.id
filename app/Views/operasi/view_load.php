<?php

$kodeCabang = $this->dCabang['kode_cabang'];
$modeView = $data['modeView'];
$loadRekap = [];
$id_pelanggan = $data['pelanggan']['id_pelanggan'];
$nama_pelanggan = $data['pelanggan']['nama_pelanggan'];
$no_pelanggan = $data['pelanggan']['nomor_pelanggan'];
$labeled = false;
?>

<div id="colAntri" class="container-fluid">
  <div class="row p-1">
    <?php
    $prevPoin = 0;
    $arrRef = [];
    $arrPoin = [];
    $jumlahRef = 0;
    $r_bayar = [];

    foreach ($data['data_main'] as $a) {
      $ref = $a['no_ref'];
      if (isset($arrRef[$ref])) {
        $arrRef[$ref] += 1;
      } else {
        $arrRef[$ref] = 1;
      }

      //Riwayat Bayar
      foreach ($data['kas'] as $ks) {
        if ($ks['ref_transaksi'] == $ref) {
          if ($ks['ref_finance'] <> "") {
            if (!isset($r_bayar[$ks['ref_finance']])) {
              $r_bayar[$ks['ref_finance']]['tanggal'] = $ks['insertTime'];
              $r_bayar[$ks['ref_finance']]['note'] = ($ks['note'] == '') ? "Tunai" : $ks['note'];
              $r_bayar[$ks['ref_finance']]['jumlah'] = $ks['jumlah'];
              $r_bayar[$ks['ref_finance']]['penerima'] = $ks['id_user'];
              $r_bayar[$ks['ref_finance']]['status'] = $ks['status_mutasi'];
            } else {
              $r_bayar[$ks['ref_finance']]['jumlah'] += $ks['jumlah'];
            }
          }
        }
      }
    }

    $no_urut = 0;
    $urutRef = 0;
    $listPrint = "";
    $arrGetPoin = [];
    $arrTotalPoin = [];
    $arrBayar = [];
    $arrBayarAll = [];

    $enHapus = true;
    $arrTuntas = [];

    $cols = 0;
    $countMember = 0;

    $rekapAntrian = "";
    $arrRekapAntrian = [];

    $countEndLayananDone = [];
    $countAmbil = [];
    ?>

    <?php

    foreach ($data['data_main'] as $a) {
      $no_urut += 1;

      $id = $a['id_penjualan'];
      $id_cabang = $a['id_cabang'];
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
      $f18 = $a['id_user'];
      $noref = $a['no_ref'];
      $letak = $a['letak'];
      $pack = $a['pack'];
      $hanger = $a['hanger'];
      $id_ambil = $a['id_user_ambil'];
      $tgl_ambil = $a['tgl_ambil'];
      $idPoin = $a['id_poin'];
      $perPoin = $a['per_poin'];
      $timeRef = $f1;
      $member = $a['member'];
      $showMember = "";
      $id_harga = $a['id_harga'];
      $countMember = $countMember + $member;

      if ($f12 <> 0) {
        $tgl_selesai = date('d-m-Y', strtotime($f1 . ' +' . $f12 . ' days +' . $f13 . ' hours'));
      } else {
        $tgl_selesai = date('d-m-Y H:i', strtotime($f1 . ' +' . $f12 . ' days +' . $f13 . ' hours'));
      }

      $karyawan = '';
      foreach ($this->userMerge as $c) {
        if ($c['id_user'] == $f18) {
          $karyawan = $c['nama_user'];
          $karyawan_id = $c['id_user'];
        }
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

      $show_qty = "";
      $qty_real = 0;

      if ($f6 < $f16) {
        $qty_real = $f16;
        $show_qty = $f6 . $satuan . " <small>(Min. " . $f16 . $satuan . ")</small>";
      } else {
        $qty_real = $f6;
        $show_qty = $f6 . $satuan;
      }

      if ($idPoin > 0) {
        if (isset($arrPoin[$noref][$idPoin]) ==  TRUE) {
          $arrPoin[$noref][$idPoin] = $arrPoin[$noref][$idPoin] + ($qty_real * $f7);
        } else {
          $arrPoin[$noref][$idPoin] = ($qty_real * $f7);
        }
        $arrGetPoin[$noref][$idPoin] = $arrPoin[$noref][$idPoin] / $perPoin;

        $gPoin = 0;
        if (isset($arrGetPoin[$noref][$idPoin]) == TRUE) {
          foreach ($arrGetPoin[$noref] as $gpp) {
            $gPoin = $gPoin + $gpp;
          }
          $arrTotalPoin[$noref] = floor($gPoin);
        }
      }

      $pelanggan_show = $nama_pelanggan;
      if (strlen($nama_pelanggan) > 20) {
        $pelanggan_show = substr($nama_pelanggan, 0, 20) . "...";
      }

      $tgl_terima = date('d/m H:i', strtotime($f1));

      if ($no_urut == 1) {
        $adaBayar = false;
        $cols += 1;
        $listNotif = ""; ?>

        <div class='col p-0 m-1' style='max-width:400px;'>
          <table class='table table-sm m-0 w-100 bg-white shadow-sm'>

            <?php
            $lunas = false;
            $totalBayar = 0;
            $dibayar = 0;
            $subTotal = 0;
            $enHapus = true;
            $urutRef++;
            $buttonNotif_londri = "<a href='#' data-idPelanggan = '" . $id_pelanggan . "' data-urutRef='" . $urutRef . "' data-hp='" . $no_pelanggan . "' data-ref='" . $noref . "' data-time='" . $timeRef . "' class='text-dark sendNotif bg-white rounded col px-1'> <i class='fab fa-whatsapp'></i><span id='notif" . $urutRef . "'></span></a>";

            foreach ($data['notif_bon'] as $notif) {
              if ($notif['no_ref'] == $noref) {
                $statusWA = $notif['proses'];
                if ($statusWA == '') {
                  $statusWA = 'Pending';
                }
                $stNotif = "<b>" . ucwords(strtolower($statusWA)) . "</b> " . ucwords($notif['state']);
                $buttonNotif_londri = "<span class='bg-white rounded px-1'><i class='fab fa-whatsapp'></i> " . $stNotif . "</span>";
              }
            }

            $dateToday = date("Y-m-d");
            if (strpos($f1, $dateToday) !== FALSE) {
              $classHead = 'table-primary';
            } else {
              $classHead = 'table-success';
            } ?>

            <tr class='<?= $classHead ?> row<?= $noref ?>' id='tr<?= $id ?>'>
              <td class='text-center border-bottom-0 pb-0'><a href='#' class='text-dark' onclick='PrintContentRef("<?= $urutRef ?>","<?= $id_pelanggan ?>")'><i class='fas fa-print'></i></a></td>
              <td colspan='3' class="border-bottom-0 pb-0">
                <span style='cursor:pointer' title='<?= $nama_pelanggan ?>'><b><?= strtoupper($pelanggan_show) ?></b></span>
                <small><span class="float-end"><b><i class='far fa-check-circle text-secondary'></i> <?= $karyawan ?></b> <span style='white-space: pre;'><?= $tgl_terima ?></span></span></small>
              </td>
            </tr>
            <tr class="<?= $classHead ?>">
              <td class="border-top-0 pt-0"></td>
              <td colspan="3" class="border-top-0 pt-0">
                <small>
                  <span class="shadow-sm me-1"><?= $buttonNotif_londri ?></span><a href='#'><span onclick='Print("Label")' class='bg-white rounded px-1 shadow-sm me-1'><i class='fa fa-tag'></i></span></a><a href='#' class='tambahCas bg-white rounded px-1 shadow-sm me-1' data-ref="<?= $noref ?>" data-tr='id_transaksi'><span data-bs-toggle='modal' data-bs-target='#exampleModalSurcas'><i class='fa fa-plus'></i></span></a><span class='bg-white rounded shadow-sm px-1 me-1'><a class='text-dark' href='<?= URL::BASE_URL . "I/i/" . $id_pelanggan ?>' target='_blank'><i class='fas fa-file-invoice'></i></a></span><a class='text-dark bg-white rounded px-1 shadow-sm me-1' href='#' onclick='bonJPG("<?= $urutRef ?>","<?= $noref ?>", "<?= $id_pelanggan ?>")'><i class='far fa-arrow-alt-circle-down'></i> JPG</a>
                </small>
                <small class="float-end"><span>#<?= date('Y', strtotime($f1)) ?></span></small>
              </td>
            </tr>
          <?php
        }

        $idKas = "";

        foreach ($data['kas'] as $byr) {
          if ($byr['ref_transaksi'] ==  $noref && $byr['status_mutasi'] == 3) {
            $idKas = $byr['id_kas'];
            $arrBayar[$noref][$idKas] = $byr['jumlah'];
          }
          if ($byr['ref_transaksi'] ==  $noref && $byr['status_mutasi'] <> 4) {
            $idKas = $byr['id_kas'];
            $arrBayarAll[$noref][$idKas] = $byr['jumlah'];
          }
          if ($byr['ref_transaksi'] == $noref) {
            $adaBayar = true;
          }
        }

        if (isset($arrBayar[$noref][$idKas])) {
          $totalBayar = array_sum($arrBayar[$noref]);
        }
        if (isset($arrBayarAll[$noref][$idKas])) {
          $dibayar = array_sum($arrBayarAll[$noref]);
        }

        $kategori = "";
        foreach ($this->itemGroup as $b) {
          if ($b['id_item_group'] == $f3) {
            $kategori = $b['item_kategori'];
          }
        }

        $durasi = "";
        foreach ($this->dDurasi as $b) {
          if ($b['id_durasi'] == $f11) {
            $durasi = strtoupper($b['durasi']);
          }
        }

        $userAmbil = "";
        $endLayananDone = false;
        $list_layanan = "";
        $list_layanan_print = "";
        $arrList_layanan = unserialize($f5);
        $endLayanan = end($arrList_layanan);
        $doneLayanan = 0;
        $countLayanan = count($arrList_layanan);
        foreach ($arrList_layanan as $b) {
          $check = 0;
          foreach ($this->dLayanan as $c) {
            if ($c['id_layanan'] == $b) {
              foreach ($data['operasi'] as $o) {
                if ($o['id_penjualan'] == $id && $o['jenis_operasi'] == $b) {
                  $user = "";
                  $check++;
                  if ($b == $endLayanan) {
                    $endLayananDone = true;
                    if (isset($countEndLayananDone[$noref])) {
                      $countEndLayananDone[$noref] += 1;
                    } else {
                      $countEndLayananDone[$noref] = 1;
                    }
                  }
                  foreach ($this->userMerge as $p) {
                    if ($p['id_user'] == $o['id_user_operasi']) {
                      $user = $p['nama_user'];
                    }
                    if ($p['id_user'] == $id_ambil) {
                      $userAmbil = $p['nama_user'];
                    }
                  }

                  $buttonNotifSelesai = "";
                  if ($b == $endLayanan && $endLayananDone == true) {
                    foreach ($data['notif_selesai'] as $notif) {
                      if ($notif['no_ref'] == $id) {
                        $stNotif = "<b>" . ucwords(strtolower($notif['proses'])) . "</b> " . ucwords($notif['state']);
                        $buttonNotifSelesai = "<span class='text-secondary'><i class='far fa-check-circle'></i> " . ucwords($stNotif) . "</span><br>";
                      }
                    }
                  }

                  if ($this->id_privilege >= 100) {
                    $list_layanan =
                      $list_layanan .
                      "<span style='cursor:pointer' data-awal='" . $user . "' data-id='" . $o['id_operasi'] . "' class='gantiOperasi' data-bs-toggle='modal' data-bs-target='#modalGanti'>
                  <b><i class='far fa-check-circle text-success'></i> " . $user . "</b> " . $c['layanan'] . " <span style='white-space: pre;'>" . date('d/m H:i', strtotime($o['insertTime'])) . "</span>
                  </span><br>" . $buttonNotifSelesai;
                  } else {
                    $list_layanan =
                      $list_layanan .
                      "<b><i class='far fa-check-circle text-success'></i> " . $user . "</b> " . $c['layanan'] . " <span style='white-space: pre;'>" . date('d/m H:i', strtotime($o['insertTime'])) . "</span><br>" . $buttonNotifSelesai;
                  }

                  $doneLayanan++;
                  $enHapus = false;
                }
              }
              if ($check == 0) {
                if ($b == $endLayanan) {
                  $list_layanan =
                    $list_layanan .
                    "<span style='cursor:pointer' id='" . $id . $b . "' data-layanan='" . $c['layanan'] . "' data-value='" . $c['id_layanan'] . "' data-id='" . $id . "' data-ref='" . $noref . "' data-bs-toggle='modal' data-bs-target='#exampleModal' class='endLayanan'><i class='far fa-circle text-info'></i> " . $c['layanan'] . "</span><br>
                  <span class='d-none ambilAfterSelesai" . $id . $b . "'><a href='#' data-id='" . $id . "' data-ref='" . $noref . "' data-bs-toggle='modal' data-bs-target='#exampleModal4' class='ambil text-dark ambil" . $id . "'><i class='far fa-circle'></i> Ambil</a></span>";
                } else {
                  $list_layanan =
                    $list_layanan .
                    "<span style='cursor:pointer' id='" . $id . $b . "' data-layanan='" . $c['layanan'] . "' data-value='" . $c['id_layanan'] . "' data-id='" . $id . "' data-ref='" . $noref . "' data-bs-toggle='modal' data-bs-target='#exampleModal' class='addOperasi'><i class='far fa-circle text-info'></i> " . $c['layanan'] . "</span><br>";
                }

                $layananNow = $c['layanan'];
                if (isset($arrRekapAntrian[$layananNow])) {
                  $arrRekapAntrian[$layananNow] += $f6;
                } else {
                  $arrRekapAntrian[$layananNow] = $f6;
                }
              }
              $list_layanan_print = $list_layanan_print . $c['layanan'] . " ";
            }
          }
        }

        $ambilDone = false;
        if ($id_ambil > 0) {
          $list_layanan = $list_layanan . "<b><i class='far fa-check-circle text-success'></i> " . $userAmbil . "</b> Ambil <span style='white-space: pre;'>" . date('d/m H:i', strtotime($tgl_ambil))  . "</span><br>";
          $ambilDone = true;
          if (isset($countAmbil[$noref])) {
            $countAmbil[$noref] += 1;
          } else {
            $countAmbil[$noref] = 1;
          }
        }

        $buttonAmbil = "";
        if ($id_ambil == 0 && $endLayananDone == true) {
          $buttonAmbil = "<a href='#' data-id='" . $id . "' data-ref='" . $noref . "' data-bs-toggle='modal' data-bs-target='#exampleModal4' class='ambil text-dark ambil" . $id . "'><i class='far fa-circle'></i> Ambil</a>";
        }


        $list_layanan = $list_layanan . "<span class='operasiAmbil" . $id . "'></span>";

        $adaDiskon = false;

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

        $total = $f7 * $qty_real;

        if ($member == 0) {
          if ($diskon_qty > 0 && $diskon_partner == 0) {
            $total = $total - ($total * ($diskon_qty / 100));
          } else if ($diskon_qty == 0 && $diskon_partner > 0) {
            $total = $total - ($total * ($diskon_partner / 100));
          } else if ($diskon_qty > 0 && $diskon_partner > 0) {
            $total = $total - ($total * ($diskon_qty / 100));
            $total = $total - ($total * ($diskon_partner / 100));
          } else {
            $total = ($f7 * $qty_real);
          }
        } else {
          $total = 0;
        }

        $subTotal = $subTotal + $total;
        $show_total = "";
        $show_total_print = "";
        $show_total_notif = "";

        if ($member == 0) {
          if (strlen($show_diskon) > 0) {
            $tampilDiskon = "(Disc. " . $show_diskon . ")";
            $show_total = "<del>Rp" . number_format($f7 * $qty_real) . "</del><br>Rp" . number_format($total);
            $show_total_print = "<del>Rp" . number_format($f7 * $qty_real) . "</del> Rp" . number_format($total);
            $show_total_notif = "~Rp" . number_format($f7 * $qty_real) . "~" . " Rp" . number_format($total) . " ";
          } else {
            $tampilDiskon = "";
            $show_total = "Rp" . number_format($total);
            $show_total_print = "Rp" . number_format($total);
            $show_total_notif = "Rp" . number_format($total);
          }
        } else {
          $show_total = "<span class='badge badge-success'>Member</span>";
          $show_total_print = "MEMBER";
          $show_total_notif = "MEMBER";
          $tampilDiskon = "";
        }

        $showNote = "";
        if (strlen($f8) > 0) {
          $showNote = $f8;
        }

        $classDurasi = "";
        if (strpos($durasi, "EKSPRES") !== false || strpos($durasi, "KILAT") !== false || strpos($durasi, "PREMIUM") !== false) {
          $classDurasi = "fw-bold text-danger";
        }

        $classTRDurasi = "";
        if (strpos($durasi, "-D") !== false) {
          $classTRDurasi = "table-warning";
        } ?>

          <tr id='tr<?= $id ?>' class='row<?= $noref ?> <?= $classTRDurasi ?> table-borderless'>

            <?php
            if ($ambilDone == false) {
              $classs_rak = "text-success editRak";
              $classs_pack = "text-info editPack";
              $classs_hanger = "text-info editHanger";
            } else {
              $classs_rak = "text-secondary";
              $classs_pack = "text-secondary";
              $classs_hanger = "text-secondary";
            }
            ?>
            <td nowrap class='text-center'>
              <a href='#' class='mb-1 text-secondary' onclick='Print(<?= $id ?>)'><i class='fas fa-print'></i></a><br>
              <?php
              if (strlen($letak) > 0) {
                $statusRak = "<h6 class='m-0 p-0'><small><span data-id='" . $id . "' data-value='" . strtoupper($letak) . "' class='m-0 p-0 fw-bold " . $classs_rak . " " . $id . "'>" . strtoupper($letak) . "</span></small></h6>";
              } else {
                $statusRak = "<h6 class='m-0 p-0'></small><span data-id='" . $id . "' data-value='" . strtoupper($letak) . "' class='m-0 p-0 fw-bold " . $classs_rak . " " . $id . "'>[ ]</span><small></h6>";
              }

              if ($endLayananDone == false) {
                $statusRak = "<span class='" . $classs_rak . " " . $id . "'></span>";
              }

              if ($doneLayanan == true) {
              }

              if ($endLayananDone == true) {
                $statusPack = "<h6 class='m-0 p-0'><small><b class='" . $classs_pack . "'>P</b><span data-id='" . $id . "' data-value='" . strtoupper($pack) . "' class='m-0 p-0 fw-bold " . $classs_pack . " " . $id . "'>" . strtoupper($pack) . "</span></small></h6>";
                $statusHanger = "<h6 class='m-0 p-0'><small><b class='" . $classs_hanger . "'>H</b><span data-id='" . $id . "' data-value='" . strtoupper($hanger) . "' class='m-0 p-0 fw-bold " . $classs_hanger . " " . $id . "'>" . strtoupper($hanger) . "</span></small></h6>";
              } else {
                $statusPack = "";
                $statusHanger = "";
              }

              echo "<small>";
              echo $statusRak;
              echo $statusPack;
              echo $statusHanger;
              echo "</small>";
              ?>
            </td>

            <td class='pb-0'>
              <small><?= $id ?></small><br><b><span style='white-space: nowrap;'><?= $kategori ?></span></b><span class='badge badge-light'></span>
              <br><span class='<?= $classDurasi ?>' style='white-space: pre;'><?= $durasi ?> <?= $f12 ?>h <?= $f13 ?>j</span><br>
              <b><?= $show_qty ?></b> <?= $tampilDiskon ?><br><?= $itemList ?>
            </td>
            <td nowrap><?= $list_layanan . $buttonAmbil ?></td>
            <td class='text-right'><?= $show_total ?></td>
          </tr>
          <tr class='<?= $classTRDurasi ?>'>
            <?php if (strlen($f8) > 0) { ?>
              <td style='border-top:0' colspan='5' class='m-0 pt-0'><span class='badge badge-warning'><?= $f8 ?></span></td>
            <?php } else { ?>
              <td style='border-top:0' colspan='5' class='m-0 pt-0'><span class='badge badge-warning'></span></td>
            <?php } ?>
          </tr>

          <?php
          $showMutasi = "";
          $userKas = "";
          foreach ($data['kas'] as $ka) {
            if ($ka['ref_transaksi'] == $noref) {
              foreach ($this->userMerge as $usKas) {
                if ($usKas['id_user'] == $ka['id_user']) {
                  $userKas = $usKas['nama_user'];
                }
              }

              $stBayar = "";
              foreach ($this->dStatusMutasi as $st) {
                if ($ka['status_mutasi'] == $st['id_status_mutasi']) {
                  $stBayar = $st['status_mutasi'];
                }
              }

              $notenya = strtoupper($ka['note']);

              switch ($ka['status_mutasi']) {
                case '2':
                  $statusM = "<span class='text-info'>" . $stBayar . " <b>(" . $notenya . ")</b></span> - ";
                  break;
                case '3':
                  $statusM = "<b><i class='far fa-check-circle text-success'></i></b> " . $notenya . " ";
                  break;
                case '4':
                  $statusM = "<span class='text-danger text-bold'><i class='fas fa-times-circle'></i> " . $stBayar . " <b>(" . $notenya . ")</b></span> - ";
                  break;
                default:
                  $statusM = "Non Status - ";
                  break;
              }

              if ($ka['status_mutasi'] == 4) {
                $nominal = "<s>-Rp" . number_format($ka['jumlah']) . "</s>";
              } else {
                $nominal = "-Rp" . number_format($ka['jumlah']);
              }

              $showMutasi = $showMutasi . "<small>" . $statusM . "#" . $ka['id_kas'] . "</small> <b>" . $userKas . "</b> " . date('d/m H:i', strtotime($ka['insertTime'])) . " " . $nominal . "<br>";
            }
          }

          $spkPrint = "";
          $firstid = substr($id, 0, strlen($id) - 3);
          $lastid = substr($id, -3);
          $spkPrint = "<tr><td colspan='2'>ID" . $firstid . "-<b>" . $lastid . "</b> <br>Selesai <b>" . $tgl_selesai . "</b></td>
          </tr>
          <tr>
            <td>" . $penjualan . "</td>
            <td>" . $kategori . "</td>
          </tr>
          <tr>
            <td><b>" . strtoupper($durasi) . "</b></td>
            <td><b>" . strtoupper($list_layanan_print) . "</b></td>
          </tr>
          <tr>
            <td style='vertical-align:top'><b>" . $show_qty . "</b></td>
            <td style='text-align: right;'><b>" . $show_total_print . "</b></td>
          </tr>
          <tr>
            <td colspan='2'>" . $itemListPrint . "</td>
          </tr>
          <tr>
            <td colspan='2'>" . $showNote . "</td>
          </tr>
          <tr>
            <td colspan='2' style='border-bottom:1px dashed black;'></td>
          </tr>";
          $listPrint = $listPrint . $spkPrint;

          // LIST ITEM LAUNDRY
          $listNotif = $listNotif . "\n" . $kategori . " " . $show_qty . "\n" .  rtrim($list_layanan_print, " ") . " " . ucwords(strtolower($durasi)) . "\n#" . $id . " " . $show_total_notif . "\n";
          echo "<span class='d-none selesai" . $id . "' data-hp='" . $no_pelanggan . "'>" . strtoupper($nama_pelanggan) . " _#" . $kodeCabang . "_ \n#" . $id . " Selesai. " . $show_total_notif . "\n" . $this->HOST_URL . "/I/i/" . $id_pelanggan . "</span>";

          ?>
          <tr class="d-none">
            <td>
              <div class="d-none" id="print<?= $id ?>" style="width:50mm;background-color:white; border:1px solid grey">
                <style>
                  @font-face {
                    font-family: "fontku";
                    src: url("<?= $this->ASSETS_URL ?>font/Titillium-Regular.otf");
                  }

                  html .table {
                    font-family: 'fontku', sans-serif;
                  }

                  html .content {
                    font-family: 'fontku', sans-serif;
                  }

                  html body {
                    font-family: 'fontku', sans-serif;
                  }

                  @media print {
                    p div {
                      font-family: 'fontku', sans-serif;
                      font-size: 14px;
                    }
                  }

                  hr {
                    border-top: 1px dashed black;
                  }
                </style>
                <table style="width:42mm; font-size:x-small; margin-top:<?= URL::MARGIN_TOP_NOTA ?>px; margin-bottom:10px">
                  <tr>
                    <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                      <b><?= $this->dCabang['nama'] ?> - <?= $this->dCabang['kode_cabang'] ?></b><br>
                      <?= $this->dCabang['alamat'] ?>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                      <font size='2'><b><?= strtoupper($nama_pelanggan) ?></b></font><br>
                      REF<b><?= $id_cabang ?></b>#<?= $noref ?><br>
                      <?= $f1 ?>
                    </td>
                  </tr>
                  <?= $spkPrint ?>
                  <tr>
                    <td align="center" colspan="2"><?= URL::PACK_ROWS ?><b>- <?= $this->dCabang['kode_cabang'] ?> -</b>
                      <hr>
                    </td>
                  </tr>
                </table>
              </div>
            </td>
          </tr>
          <?php

          if ($arrRef[$noref] == $no_urut) {

            //SURCAS
            foreach ($data['surcas'] as $sca) {
              if ($sca['no_ref'] == $noref) {
                foreach ($this->surcas as $sc) {
                  if ($sc['id_surcas_jenis'] == $sca['id_jenis_surcas']) {
                    $surcasNya = $sc['surcas_jenis'];
                  }
                }

                foreach ($this->userMerge as $p) {
                  if ($p['id_user'] == $sca['id_user']) {
                    $userCas = $p['nama_user'];
                  }
                }

                $id_surcas = $sca['id_surcas'];
                $jumlahCas = $sca['jumlah'];
                $tglCas = "<b><i class='far fa-check-circle text-success'></i> " . $userCas . "</b> Input <span style='white-space: pre;'>" . date('d/m H:i', strtotime($sca['insertTime'])) . "</span><br>";
                echo "<tr><td></td><td>" . $surcasNya . "</td><td>" . $tglCas . "</td><td align='right'>Rp" . number_format($jumlahCas) . "</td></tr>";
                $subTotal += $jumlahCas;

                $spkPrint = "<tr><td colspan='2'>S" . $id_surcas . " <br><b>" . $surcasNya . "</b></td></tr><tr><td></td><td style='text-align: right;'><b>Rp" . number_format($jumlahCas) . "</b></td></tr><tr><td colspan='2' style='border-bottom:1px dashed black;'></td></tr>";
                $listPrint = $listPrint . $spkPrint;
                // LIST SURCAS
                $listNotif = $listNotif . "\n#S" . $id_surcas . " " . $surcasNya . " Rp" . number_format($jumlahCas) . "\n";
              }
            }

            if ($totalBayar > 0) {
              $enHapus = false;
            }
            $sisaTagihan = intval($subTotal) - $dibayar;
            $sisaTagihanFinal = intval($subTotal) - $totalBayar;
            $textPoin = "";
            if (isset($arrTotalPoin[$noref]) && $arrTotalPoin[$noref] > 0) {
              $textPoin = " (Poin " . $arrTotalPoin[$noref] . ") ";
            }
            echo "<span class='d-none' id='poin" . $urutRef . "'>" . $textPoin . "</span>";
            echo "<span class='d-none' id='member" . $urutRef . "'>" . $countMember . "</span>";

            $buttonHapus = "";
            if ($enHapus == true || $this->id_privilege >= 100) {
              $buttonHapus = "<small><a href='#' data-ref='" . $noref . "' class='hapusRef mb-1'><i class='fas fa-trash-alt text-secondary'></i></a><small> ";
            }
            if ($sisaTagihanFinal < 1) {
              $lunas = true;
            } else {
              if ($sisaTagihan > 0) {
                $loadRekap['U#' . $noref] = $sisaTagihan;
              }
            }
          ?>
            <tr class='row<?= $noref ?>'>
              <td class='text-center'><span class='d-none'><?= $nama_pelanggan ?></span><?= $buttonHapus ?></td>

              <?php
              if (isset($countEndLayananDone[$noref]) && isset($countAmbil[$noref])) {
                if ($lunas == true && $countEndLayananDone[$noref] == $arrRef[$noref] && $countAmbil[$noref] == $arrRef[$noref]) {
                  if ($modeView <> 2) { // 2 SUDAH TUNTAS
                    array_push($arrTuntas, $noref);
                  }
                }
              }

              if ($lunas == false) {
                echo "<td nowrap colspan='3' class='text-right'><small>
                    <font color='green'>" . $textPoin . "</font>
                  </small> <span class='showLunas" . $noref . "'></span><b> Rp" . number_format($subTotal) . "</b><br>";
              } else {
                echo "
                <td nowrap colspan='3' class='text-right'><small>
                    <font color='green'>" . $textPoin . "</font>
                  </small> <b><i class='far fa-check-circle text-success'></i> Rp" . number_format($subTotal) . "</b><br>";
              }
              ?>

              </td>
            </tr>

            <?php
            if ($adaBayar == true) {
              $classMutasi = "";
            } else {
              $classMutasi = "d-none";
            }
            ?>
            <tr class='row<?= $noref ?> sisaTagihan<?= $noref ?> <?= $classMutasi ?>'>
              <td nowrap colspan='4' class='text-right'>
                <?= $showMutasi ?>
                <span class='text-danger sisaTagihan<?= $noref ?>'>
                  <?php if (($sisaTagihan < intval($subTotal)) && (intval($sisaTagihan) > 0)) { ?>
                    <b><i class='fas fa-exclamation-circle'></i> Sisa Rp<?= number_format($sisaTagihan) ?></b>
                  <?php } ?>
                </span>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <?php if ($cols == 2) { ?>
          <div class="w-100"></div>
        <?php $cols = 0;
            } ?>

        <?php
            if ($member > 0) {
              $totalText = "";
            } else {
              if ($lunas == false) {
                $totalText = "\n*Total Rp" . number_format($subTotal) . ". Bayar Rp" . number_format($totalBayar) . $textPoin . "*";
              } else {
                $totalText = "\n*Total Rp" . number_format($subTotal) . ". LUNAS" . $textPoin . "*";
              }
            }
        ?>

        <!-- NOTIF -->
        <div class="d-none">
          <span id="<?= $urutRef ?>"><?= strtoupper($nama_pelanggan) ?> _#<?= $this->dCabang['kode_cabang'] ?>_ <?= "\n" . $listNotif . $totalText . "\n" ?><?= $this->HOST_URL  ?>/I/i/<?= $id_pelanggan ?></span>
        </div>
        <div class="d-none" id="print<?= $urutRef ?>" style="width:50mm;background-color:white; padding-bottom:10px">
          <style>
            @font-face {
              font-family: "fontku";
              src: url("<?= $this->ASSETS_URL ?>font/Titillium-Regular.otf");
            }

            html .table {
              font-family: 'fontku', sans-serif;
            }

            html .content {
              font-family: 'fontku', sans-serif;
            }

            html body {
              font-family: 'fontku', sans-serif;
            }

            @media print {
              p div {
                font-family: 'fontku', sans-serif;
                font-size: 14px;
              }
            }

            hr {
              border-top: 1px dashed black;
            }
          </style>
          <table style="width:42mm; font-size:x-small; margin-top:<?= URL::MARGIN_TOP_NOTA ?>px; margin-bottom:10px">
            <tr>
              <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                <b> <?= $this->dCabang['nama'] ?> - <?= $this->dCabang['kode_cabang'] ?></b><br>
                <?= $this->dCabang['alamat'] ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                <font size='2'><b><?= strtoupper($nama_pelanggan) ?></b></font><br>
                REF<b><?= $id_cabang ?></b>#<?= $noref ?><br>
                <?php
                $tgl_masuk = date('d-m-Y H:i', strtotime($f1));
                echo $tgl_masuk ?>
              </td>
            </tr>
            <?= $listPrint ?>
            <tr>
              <td>
                Total
              </td>
              <td style="text-align: right;">
                <?= "Rp" . number_format($subTotal) ?>
              </td>
            </tr>
            <tr>
              <td>
                Bayar
              </td>
              <td style="text-align: right;">
                Rp<?= number_format($totalBayar) ?>
              </td>
            </tr>
            <tr>
              <td>
                Sisa
              </td>
              <td style="text-align: right;">
                Rp<?= number_format($sisaTagihan) ?>
              </td>
            </tr>
            <?php if (strlen($textPoin) > 0 || strlen($countMember > 0)) { ?>
              <tr>
                <td colspan='2' style='border-bottom:1px dashed black;'></td>
              </tr>
              <?php if (strlen($textPoin) > 0) { ?>
                <tr>
                  <td>
                    Poin
                  </td>
                  <td style="text-align: right;">
                    <?= $textPoin ?> <span class="saldoPoin<?= $urutRef ?>"></span>
                  </td>
                </tr>
              <?php }
              if (strlen($countMember > 0)) { ?>
                <tr>
                  <td class="textMember<?= $urutRef ?>" colspan="2"></td>
                </tr>
            <?php }
            } ?>
            <tr>
              <td colspan="2" style="border-bottom:1px dashed black;"></td>
            </tr>
            <tr>
              <td align="center" colspan="2"><?= URL::PACK_ROWS ?><b>- <?= $this->dCabang['kode_cabang'] ?> -</b>
                <hr>
              </td>
            </tr>
          </table>
        </div>

        <?php if ($labeled == false) { ?>
          <div class="d-none" id="printLabel" style="width:50mm;padding-bottom:10px">
            <style>
              @font-face {
                font-family: "fontku";
                src: url("<?= $this->ASSETS_URL ?>font/Titillium-Regular.otf");
              }

              html .table {
                font-family: 'fontku', sans-serif;
              }

              html .content {
                font-family: 'fontku', sans-serif;
              }

              html body {
                font-family: 'fontku', sans-serif;
              }

              @media print {
                p div {
                  font-family: 'fontku', sans-serif;
                  font-size: 14px;
                }
              }

              hr {
                border-top: 1px dashed black;
              }
            </style>
            <table style="width:42mm; margin-top:<?= URL::MARGIN_TOP_NOTA ?>px; margin-bottom:10px">
              <tr>
                <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                  <br>
                  <font size='1'>
                    <?= $this->dCabang['nama'] ?> - <b><?= $this->dCabang['kode_cabang'] ?></b><br>
                    <?= date("Y-m-d H:i:s") ?>
                  </font>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                  <font size='5'><b><?= strtoupper($nama_pelanggan) ?></b></font>
                </td>
              </tr>
              <tr>
                <td colspan="2" align="center" style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                  <font size='1'>
                    <?= URL::PACK_ROWS ?><b>- <?= $this->dCabang['kode_cabang'] ?> -</b>
                  </font>
                </td>
              </tr>
            </table>
          </div>
        <?php
              $labeled = true;
            } ?>

    <?php
            $totalBayar = 0;
            $sisaTagihan = 0;
            $no_urut = 0;
            $subTotal = 0;
            $listPrint = "";
            $enHapus = true;
          }
        }
    ?>

    <!-- MEMEBR ================================================== -->

    <?php
    foreach ($data['data_member'] as $z) {
      $cols += 1;
      $id = $z['id_member'];
      $id_harga = $z['id_harga'];
      $harga = $z['harga'];
      $id_user = $z['id_user'];
      $kategori = "";
      $layanan = "";
      $durasi = "";
      $unit = "";
      $idPoin = $z['id_poin'];
      $perPoin = $z['per_poin'];
      $timeRef = $z['insertTime'];

      $gPoin = 0;
      $gPoinShow = "";
      if ($idPoin > 0) {
        $gPoin = floor($harga / $perPoin);
        $gPoinShow = "<small class='text-success'>(+" . $gPoin . ")</small>";
      }

      $totalBayar = 0;
      $dibayar_M = 0;
      $showMutasi = "";
      $userKas = "";
      foreach ($data['kas_member'] as $ka) {
        if ($ka['ref_transaksi'] == $id) {
          foreach ($this->userMerge as $usKas) {
            if ($usKas['id_user'] == $ka['id_user']) {
              $userKas = $usKas['nama_user'];
            }
          }

          $stBayar = "";
          foreach ($this->dStatusMutasi as $st) {
            if ($ka['status_mutasi'] == $st['id_status_mutasi']) {
              $stBayar = $st['status_mutasi'];
            }
          }

          $notenya = strtoupper($ka['note']);
          $st_mutasi = $ka['status_mutasi'];

          switch ($st_mutasi) {
            case '2':
              $statusM = "<span class='text-info'>" . $stBayar . " <b>(" . $notenya . ")</b></span> - ";
              break;
            case '3':
              $statusM = "<b><i class='far fa-check-circle text-success'></i></b> " . $notenya . " ";
              break;
            case '4':
              $statusM = "<span class='text-danger text-bold'><i class='fas fa-times-circle'></i> " . $stBayar . " <b>(" . $notenya . ")</b></span> - ";
              break;
            default:
              $statusM = "Non Status - ";
              break;
          }

          if ($st_mutasi == 4) {
            $nominal = "<s>-Rp" . number_format($ka['jumlah']) . "</s>";
          } else {
            $nominal = "-Rp" . number_format($ka['jumlah']);
          }

          $showMutasi = $showMutasi . "<small>" . $statusM . "<b>#" . $ka['id_kas'] . "</small> " . $userKas . "</b> " . date('d/m H:i', strtotime($ka['insertTime'])) . " " . $nominal . "<br>";
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
              $durasi = $c['durasi'];
            }
          }

          foreach ($this->itemGroup as $c) {
            if ($a['id_item_group'] == $c['id_item_group']) {
              $kategori = $c['item_kategori'];
            }
          }
        }
      }
      $adaBayar = false;

      $historyBayar = [];
      $hisDibayar = [];
      foreach ($data['kas_member'] as $k) {
        if ($k['ref_transaksi'] == $id && $k['status_mutasi'] == 3) {
          array_push($historyBayar, $k['jumlah']);
        }
        if ($k['ref_transaksi'] == $id && $k['status_mutasi'] <> 4) {
          array_push($hisDibayar, $k['jumlah']);
        }
        if ($k['ref_transaksi'] == $id) {
          $adaBayar = true;
        }
      }

      $statusBayar = "";
      $totalBayar = array_sum($historyBayar);
      $dibayar_M = array_sum($hisDibayar);
      $showSisa = "";
      $sisa = $harga;
      $lunas = false;
      $enHapus = true;
      $sisa = $harga - $dibayar_M;

      if ($dibayar_M > 0) {
        $enHapus = false;
      }

      if ($totalBayar >= $harga) {
        $lunas = true;
        $statusBayar = "<b><i class='far fa-check-circle text-success'></i></b>";
      } else {
        $lunas = false;
      }

      if ($dibayar_M > 0 && $sisa > 0) {
        $showSisa = "<b><i class='fas fa-exclamation-circle'></i> Sisa Rp" . number_format($sisa) . "</b>";
      }

      $cs = "";
      foreach ($this->userMerge as $uM) {
        if ($uM['id_user'] == $id_user) {
          $cs = $uM['nama_user'];
        }
      }

      if ($enHapus == true || $this->id_privilege >= 100) {
        $buttonHapus = "<small><a href='" . URL::BASE_URL . "Member/bin/" . $id . "' data-ref='" . $id . "' class='hapusRef text-dark'><i class='fas fa-trash-alt'></i></a></small> ";
      } else {
        $buttonHapus = "";
      }

      //BUTTON NOTIF MEMBER
      $buttonNotif_Member = "<a href='#' data-ref='" . $id . "' class='sendNotifMember bg-white rounded px-1 mr-1'><i class='fab fa-whatsapp'></i> <span id='notif" . $id . "'></span></a>";
      foreach ($data['notif_member'] as $notif) {
        if ($notif['no_ref'] == $id) {
          $stNotif = "<b>" . ucwords($notif['proses']) . "</b> " . ucwords($notif['state']);
          $buttonNotif_Member = "<span class='bg-white rounded px-1 mr-1'><i class='fab fa-whatsapp'></i> " . $stNotif . "</span>";
        }
      }

      $cabangKode = $this->dCabang['kode_cabang'];
    ?>

      <?php if ($lunas == false) {
        $loadRekap['M#' . $id] = $sisa;
      ?>
        <div class='col p-0 m-1' style='max-width:400px;'>
          <table class="table bg-white table-sm w-100 pb-0 mb-0">
            <tbody>
              <tr class="table-info">
                <td><a href='#' class='ml-1 text-dark' onclick='Print("<?= $id ?>")'><i class='fas fa-print'></i></a></td>
                <td colspan="2"><b><?= strtoupper($nama_pelanggan) ?></b>
                  <div class="float-right">
                    <?= $buttonNotif_Member ?></span>
                    <span class='bg-white rounded pr-1 pl-1'><a class="text-dark" href="<?= URL::BASE_URL ?>I/i/<?= $id_pelanggan ?>" target='_blank'><i class='fas fa-file-invoice'></i></a></span>
                    <span class='rounded bg-white border pr-1 pl-1'><?= $cs ?></span>

                  </div>
                </td>
              </tr>

              <tr>
                <td class="text-center">
                  <?php if ($adaBayar == false || $this->id_privilege >= 100) { ?>
                    <span><?= $buttonHapus ?></span>
                  <?php } ?>
                </td>
                <td nowrap>
                  <?= "#" . $id . " " ?> <?= $z['insertTime'] ?><br>
                  <b>M<?= $id_harga ?></b> <?= $kategori ?> * <?= $layanan ?> * <?= $durasi ?>
                </td>
                <td nowrap class="text-right"><br><b><?= $z['qty'] . $unit ?></b></td>
              </tr>
              <tr>
                <td></td>
                <td class="text-right"></td>
                <td nowrap class="text-right"><span id="statusBayar<?= $id ?>"><?= $statusBayar ?></span>&nbsp;
                  <span class="float-right"><?= $gPoinShow ?> <b>Rp<?= number_format($harga) ?></b></span>
                </td>
              </tr>
              <?php if ($adaBayar == true) { ?>
                <tr>
                  <td></td>
                  <td colspan="2" align="right"><span id="historyBayar<?= $id ?>"><?= $showMutasi ?></span>
                    </span><span id="sisa<?= $id ?>" class="text-danger"><?= $showSisa ?></span></td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
        <?php if ($cols == 2) { ?>
          <div class="w-100"></div>
        <?php $cols = 0;
        } ?>

        <span class="d-none" id="print<?= $id ?>" style="width:50mm;background-color:white; padding-bottom:10px">
          <style>
            @font-face {
              font-family: "fontku";
              src: url("<?= $this->ASSETS_URL ?>font/Titillium-Regular.otf");
            }

            html .table {
              font-family: 'fontku', sans-serif;
            }

            html .content {
              font-family: 'fontku', sans-serif;
            }

            html body {
              font-family: 'fontku', sans-serif;
            }

            @media print {
              p div {
                font-family: 'fontku', sans-serif;
                font-size: 14px;
              }
            }

            hr {
              border-top: 1px dashed black;
            }
          </style>
          <table style="width:42mm; font-size:x-small; margin-top:<?= URL::MARGIN_TOP_NOTA ?>px; margin-bottom:10px">
            <tr>
              <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                <b> <?= $this->dCabang['nama'] ?> [ <?= $this->dCabang['kode_cabang'] ?></b> ]<br>
                <?= $this->dCabang['alamat'] ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                <font size='2'><b><?= strtoupper($nama_pelanggan) ?></b></font><br>
                #<?= $id ?><br>
                <?= $z['insertTime'] ?>
              </td>
            </tr>
            <td style="margin: 0;">Topup Paket <b>M<?= $id_harga ?></b><br><?= $kategori ?>, <?= $layanan ?>, <?= $durasi ?>, <?= $z['qty'] . $unit ?></td>
            <tr>
              <td colspan="2" style="border-bottom:1px dashed black;"></td>
            </tr>
            <tr>
              <td>
                Total
              </td>
              <td style="text-align: right;">
                <?= "Rp" . number_format($harga) ?>
              </td>
            </tr>
            <tr>
              <td>
                Bayar
              </td>
              <td style="text-align: right;">
                Rp<?= number_format($totalBayar) ?>
              </td>
            </tr>
            <tr>
              <td>
                Sisa
              </td>
              <td style="text-align: right;">
                Rp<?= number_format($sisa) ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-bottom:1px dashed black;"></td>
            </tr>
          </table>
        </span>
      <?php } ?>
    <?php } ?>
  </div>
</div>

<?php
if (count($r_bayar) > 0) { ?>
  <div style="max-width:825px">
    <div class="container-fluid pt-0">
      <div class="row pt-0 px-1 pb-0">
        <div class="col p-1">
          <div class="card p-0 mb-0">
            <div class="py-2 text-center rounded-top border-bottom border-warning" style="background-color: floralwhite;"><b>RIWAYAT PEMBAYARAN</b></div>
            <table class="table table-sm m-0 p-0">
              <?php foreach ($r_bayar as $key => $rb) {
                $reff_id = $key;

                $cl_st = "";
                switch ($rb['status']) {
                  case '0':
                  case '1':
                  case '2':
                    $st_b = "Check ";
                    break;
                  case '3':
                    $st_b = "<i class='far fa-check-circle text-success'></i> ";
                    break;
                  case '4':
                    $cl_st = "bg-light";
                    $st_b = "<i class='fas fa-times-circle text-danger'></i> ";
                    break;
                  default:
                    $st_b = "Error";
                    break;
                }
              ?>
                <tr class="<?= $cl_st ?>">
                  <td class="text-start"><?= $rb['note'] ?> </td>
                  <?php if ($rb['status'] <> 3 && $rb['status'] <> 4) { ?>
                    <!-- target="_blank" href="URL::BASE_URL?>Kas/qris_instant/$reff_id ?>" -->
                    <td class="text-end"><a href="#">QRIS Instant <i class="fas fa-qrcode"></i></a></td>
                    <td class="pe-2 text-end"><span onclick="//cekQris('//$reff_id',$rb['jumlah'])" style="cursor: pointer;" class="text-info shadow-sm px-2 me-2"><?= $st_b ?></span> <?= number_format($rb['jumlah']) ?></td>
                  <?php } else { ?>
                    <td class="text-end"></a></td>
                    <td class="pe-2 text-end"><?= $st_b ?> <?= number_format($rb['jumlah']) ?></td>
                  <?php } ?>
                </tr>
              <?php } ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php }
?>

<div id="loadRekap" style="max-width:825px" class="pb-3">
  <div class="container-fluid pb-0">
    <div class="row px-1 pt-1 pb-0">
      <div class="col p-1">
        <div class="card p-0 mb-0">
          <form method="POST" class="ajax_json">
            <div class="text-center rounded-top border-bottom border-danger py-2" style="background-color:lavenderblush;"><b>PEMBAYARAN</b></div>
            <div class="p-2">
              <table class="w-100">
                <tr>
                  <td class="pb-1">Penerima</td>
                  <td class="pt-2"><select name="karyawanBill" id="karyawanBill" class="form-control form-control-sm tize" style="width: 100%;" required>
                      <option value="" selected disabled></option>
                      <optgroup label="<?= $this->dCabang['nama'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
                        <?php foreach ($this->user as $a) { ?>
                          <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                        <?php } ?>
                      </optgroup>
                      <?php if (count($this->userCabang) > 0) { ?>
                        <optgroup label="----- Cabang Lain -----">
                          <?php foreach ($this->userCabang as $a) { ?>
                            <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                          <?php } ?>
                        </optgroup>
                      <?php } ?>
                    </select></td>
                  <td></td>
                </tr>
                <tr>
                  <td>Metode</td>
                  <td class="pb-2"><select name="metodeBill" id="metodeBill" class="form-control form-control-sm metodeBayarBill" style="width: 100%;" required>
                      <?php foreach ($this->dMetodeMutasi as $a) {
                        if ($data['saldoTunai'] <= 0 && $a['id_metode_mutasi'] == 3) {
                          continue;
                        } ?>
                        <option value="<?= $a['id_metode_mutasi'] ?>"><?= $a['metode_mutasi'] ?> <?= ($a['id_metode_mutasi'] == 3) ? "[ Rp" . number_format($data['saldoTunai']) . " ]" : "" ?></option>
                      <?php } ?>
                    </select></td>
                  <td></td>
                </tr>
                <tr id="nTunaiBill" class="border-top">
                  <td style="vertical-align: bottom;" class="pr-2 pb-2" nowrap>Catatan<br>[ Non Tunai ]</td>
                  <td class="pb-2 pt-2">
                    <label class="text-success">
                      <?php foreach (URL::NON_TUNAI as $ntm) { ?>
                        <span class="nonTunaiMetod rounded px-1" style="cursor: pointer"><?= $ntm ?></span>
                      <?php } ?>
                    </label>
                    <input type="text" name="noteBill" id="noteBill" maxlength="10" class="form-control border-danger" placeholder="" style="text-transform:uppercase">
                  </td>
                  <td></td>
                </tr>
                <tr class="border-top">
                  <td colspan="3" class="pb-1"></td>
                </tr>
                <?php
                $totalTagihan = 0;
                foreach ($loadRekap as $key => $value) {
                  echo "<tr class='hoverBill'>
                  <td colspan='2'><span class='text-dark'>" . $key . "<input class='cek float-right' type='checkbox' data-jumlah='" . $value . "' data-ref='" . $key . "' checked></td>
                  <td class='text-right pl-2'>Rp" . number_format($value) . "</td>
                  </tr>";
                  $totalTagihan += $value;
                } ?>
                <tr>
                  <td class="pb-2 pr-2 text-danger" nowrap>
                    <b>TOTAL TAGIHAN</b>
                  </td>
                  <td></td>
                  <td class="text-right text-danger">
                    <span data-total=''><b>Rp<span id="totalBill" data-total="<?= $totalTagihan ?>"><?= number_format($totalTagihan) ?></span></b></span>
                  </td>
                </tr>
                <tr class="border-top">
                  <td></td>
                  <td class="pt-2 pb-1"><a class="btn badge badge-info bayarPasMulti">Bayar Pas (Click)</a></td>
                  <td></td>
                </tr>
                <tr>
                  <td>Jumlah Bayar</td>
                  <td class="pb-1"><input id="bayarBill" name="dibayarBill" class="text-right form form-control form-control-sm" type="number" min="1" value="" required /></td>
                  <td class="text-right pl-2" rowspan="2" nowrap>
                    <button type="submit" id="btnBayarBill" class='btn btn-sm btn-outline-danger w-100 py-4'>Bayar</button>
                  </td>
                </tr>
                <tr>
                  <td>Kembalian</td>
                  <td><input id='kembalianBill' name="kembalianBill" class="text-right form form-control form-control-sm" type="number" readonly /></td>
                </tr>
              </table>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<form class="ajax" data-operasi="" action="<?= URL::BASE_URL; ?>Antrian/ambil" method="POST">
  <div class="modal" id="exampleModal4">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ambil Laundry</b></h5>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group">
              <label>Pengembali</label>
              <select name="f1" class="ambil form-control form-control-sm tize userChange" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <optgroup label="<?= $this->dCabang['nama'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
                  <?php foreach ($this->user as $a) { ?>
                    <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                  <?php } ?>
                </optgroup>
                <?php if (count($this->userCabang) > 0) { ?>
                  <optgroup label="----- Cabang Lain -----">
                    <?php foreach ($this->userCabang as $a) { ?>
                      <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                    <?php } ?>
                  </optgroup>
                <?php } ?>
              </select>
              <input type="hidden" class="idItem" name="f2" value="" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Ambil</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form data-operasi="" class="operasi ajax" action="<?= URL::BASE_URL; ?>Antrian/operasi" method="POST">
  <div class="modal" id="exampleModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Selesai <b class="operasi"></b>!</h5>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group">
              <div class="row">
                <div class="col">
                  <label>Karyawan</label>
                  <select name="f1" class="operasi form-control tize form-control-sm userChange" style="width: 100%;" required>
                    <option value="" selected disabled></option>
                    <optgroup label="<?= $this->dCabang['nama'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
                      <?php foreach ($this->user as $a) { ?>
                        <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                      <?php } ?>
                    </optgroup>
                    <?php if (count($this->userCabang) > 0) { ?>
                      <optgroup label="----- Cabang Lain -----">
                        <?php foreach ($this->userCabang as $a) { ?>
                          <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                        <?php } ?>
                      </optgroup>
                    <?php } ?>
                  </select>
                </div>
                <div class="col">
                  <label>Letak / Rak</label>
                  <input id='letakRAK' type="text" maxlength="2" name="rak" style="text-transform: uppercase" class="form-control">
                </div>
              </div>
              <input type="hidden" class="idItem" name="f2" value="" required>
              <input type="hidden" class="valueItem" name="f3" value="" required>
              <input type="hidden" class="textNotif" name="text" value="" required>
              <input type="hidden" class="hpNotif" name="hp" value="" required>
            </div>
            <div class="form-group letakRAK">
              <div class="row">
                <div class="col">
                  <label>Pack</label>
                  <input type="number" min="0" value="1" name="pack" style="text-transform: uppercase" class="form-control" required>
                </div>
                <div class="col">
                  <label>Hanger</label>
                  <input type="number" min="0" value="0" name="hanger" style="text-transform: uppercase" class="form-control" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Selesai</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form class="operasi ajax" action="<?= URL::BASE_URL; ?>Operasi/ganti_operasi" method="POST">
  <div class="modal" id="modalGanti">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title">Ubah Penyelesai</h5>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group">
              <label>Ubah dari <span class="text-danger" id="awalOP"></span> menjadi:</label>
              <select name="f1" class="operasi form-control tize form-control-sm userChange" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <optgroup label="<?= $this->dCabang['nama'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
                  <?php foreach ($this->user as $a) { ?>
                    <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                  <?php } ?>
                </optgroup>
                <?php if (count($this->userCabang) > 0) { ?>
                  <optgroup label="----- Cabang Lain -----">
                    <?php foreach ($this->userCabang as $a) { ?>
                      <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                    <?php } ?>
                  </optgroup>
                <?php } ?>
              </select>
              <input type="hidden" id="id_ganti" name="id" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-danger">Simpan</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form class="ajax" action="<?= URL::BASE_URL; ?>Antrian/surcas" method="POST">
  <div class="modal" id="exampleModalSurcas">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Surcharge/Biaya Tambahan</h5>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group">
              <label>Jenis Surcharge</label>
              <select name="surcas" class="form-control form-control-sm" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <?php foreach ($this->surcas as $sc) { ?>
                  <option value="<?= $sc['id_surcas_jenis'] ?>"><?= $sc['surcas_jenis'] ?></option>
                <?php } ?>
              </select>
            </div>
            <input type="hidden" name="no_ref" id="id_transaksi">
            <div class="form-group">
              <label>Jumlah Biaya</label>
              <input type="number" name="jumlah" class="form-control">
            </div>
            <div class="form-group">
              <label>Di input Oleh</label>
              <select name="user" class="form-control tize form-control-sm userSurcas" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <optgroup label="<?= $this->dCabang['nama'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
                  <?php foreach ($this->user as $a) { ?>
                    <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                  <?php } ?>
                </optgroup>
                <?php if (count($this->userCabang) > 0) { ?>
                  <optgroup label="---- Cabang Lain ----">
                    <?php foreach ($this->userCabang as $a) { ?>
                      <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                    <?php } ?>
                  </optgroup>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Tambah</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/dom-to-image.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/FileSaver.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>

<script>
  var noref;
  var json_rekap;
  var totalBill;
  $(document).ready(function() {
    clearTuntas();
    var diBayar = 0;
    var noref = '';
    var idRow = '';
    var idtargetOperasi = '';

    $("tr#nTunaiBill").hide();

    $('select.tize').selectize();

    totalBill = $("span#totalBill").attr("data-total");
    if (totalBill == 0) {
      $("div#loadRekap").fadeOut('slow');
    }
    json_rekap = [<?= json_encode($loadRekap) ?>];
  });

  $(".hoverBill").hover(function() {
    $(this).addClass("bg-light");
  }, function() {
    $(this).removeClass("bg-light");
  })

  $("span.nonTunaiMetod").click(function() {
    $("input[name=noteBayar]").val($(this).html());
    $("input[name=noteBill]").val($(this).html());
  })

  function clearTuntas() {
    var dataNya = '<?= serialize($arrTuntas) ?>';
    var countArr = <?= count($arrTuntas) ?>;
    var arrTuntas = <?= json_encode($arrTuntas) ?>;

    if (countArr > 0) {
      $.ajax({
        url: '<?= URL::BASE_URL ?>Antrian/clearTuntas',
        data: {
          'data': dataNya,
        },
        type: 'POST',
        success: function(response) {
          loadDiv();
        },
      });
    }
  }

  $("form.ajax").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      data: $(this).serialize(),
      type: $(this).attr("method"),
      beforeSend: function() {
        $('.modal').click();
        $(".loaderDiv").fadeIn("fast");
      },
      success: function(res) {
        if (res == 0) {
          loadDiv();
        } else {
          alert(res);
        }
      },
      complete: function() {
        $(".loaderDiv").fadeOut("slow");
      }
    });
  });

  $("form.ajax_json").on("submit", function(e) {
    e.preventDefault();

    var karyawanBill = $("#karyawanBill").val();
    var metodeBill = $("#metodeBill").val();
    var noteBill = $("#noteBill").val();

    noteBill = noteBill.replace(" ", "_SPACE_")
    var idPelanggan = "<?= $id_pelanggan ?>";

    $.ajax({
      url: "<?= URL::BASE_URL ?>Operasi/bayarMulti/" + karyawanBill + "/" + idPelanggan + "/" + metodeBill + "/" + noteBill,
      data: {
        rekap: json_rekap,
        dibayar: $("input#bayarBill").val()
      },
      type: $(this).attr("method"),
      beforeSend: function() {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function(res) {
        if (res == 0) {
          loadDiv();
        } else {
          alert(res);
        }
      },
      complete: function() {
        $(".loaderDiv").fadeOut("slow");
      }
    });
  });

  $("span.addOperasi").on('click', function(e) {
    e.preventDefault();
    $('div.letakRAK').hide();
    $('input#letakRAK').prop('required', false);

    var idNya = $(this).attr('data-id');
    var valueNya = $(this).attr('data-value');
    var layanan = $(this).attr('data-layanan');
    $("input.idItem").val(idNya);
    $("input.valueItem").val(valueNya);
    $('b.operasi').html(layanan);
    idtargetOperasi = $(this).attr('id');
    var textNya = $('span.selesai' + idNya).html();
    var hpNya = $('span.selesai' + idNya).attr('data-hp');
    $("input.textNotif").val(textNya);
    $("input.hpNotif").val(hpNya);
    idRow = idNya;
  });

  $("span.gantiOperasi").on('click', function(e) {
    e.preventDefault();
    var idNya = $(this).attr('data-id');
    var awal = $(this).attr('data-awal');
    $("input#id_ganti").val(idNya);
    $("span#awalOP").html(awal);
  });

  $("span.endLayanan").on('click', function(e) {
    e.preventDefault();
    $('div.letakRAK').show();
    $('input#letakRAK').prop('required', true);
    $('form.operasi').attr("data-operasi", "operasiSelesai");
    var idNya = $(this).attr('data-id');
    var valueNya = $(this).attr('data-value');
    var layanan = $(this).attr('data-layanan');
    noref = $(this).attr('data-ref');
    $("input.idItem").val(idNya);
    $("input.valueItem").val(valueNya);
    $('b.operasi').html(layanan);
    idtargetOperasi = $(this).attr('id');

    var textNya = $('span.selesai' + idNya).html();
    var hpNya = $('span.selesai' + idNya).attr('data-hp');
    $("input.textNotif").val(textNya);
    $("input.hpNotif").val(hpNya);
    idRow = idNya;
  });

  $("a.directWA_selesai").on('click', function(e) {
    e.preventDefault();
    var idNya = $(this).attr('data-id');
    var hpNya = $('span.selesai' + idNya).attr('data-hp');
    var textNya = $('span.selesai' + idNya).html();
    var number = '62' + hpNya.substring(1);
    window.open("https://wa.me/" + number + "?text=" + textNya);
  });

  var totalTagihan = 0;

  $('.tambahCas').click(function() {
    noref = $(this).attr('data-ref');
    idNya = $(this).attr('data-tr');
    $("#" + idNya).val(noref);
  });

  $("a.hapusRef").on('dblclick', function(e) {
    e.preventDefault();
    var refNya = $(this).attr('data-ref');
    var note = prompt("Alasan Hapus:", "");
    if (note === null || note.length == 0) {
      return;
    }
    $.ajax({
      url: '<?= URL::BASE_URL ?>Antrian/hapusRef',
      data: {
        ref: refNya,
        note: note
      },
      type: "POST",
      beforeSend: function() {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function(response) {
        loadDiv();
      },
      complete: function() {
        $(".loaderDiv").fadeOut("slow");
      }
    });
  });

  $("a.hapusRef").on('click', function(e) {
    e.preventDefault();
  });

  $("a.ambil").on('click', function(e) {
    e.preventDefault();
    var idNya = $(this).attr('data-id');
    $("input.idItem").val(idNya);
  });

  var klikNotif = 0;

  $("a.sendNotif").on('click', function(e) {
    klikNotif += 1;
    if (klikNotif > 1) {
      return;
    }
    $(this).fadeOut("slow");
    e.preventDefault();
    var urutRef = $(this).attr('data-urutRef');
    var id_pelanggan = $(this).attr('data-idPelanggan');
    var hpNya = $(this).attr('data-hp');
    var refNya = $(this).attr('data-ref');
    var timeNya = $(this).attr('data-time');
    var textNya = $("span#" + urutRef).html();
    var countMember = $("span#member" + urutRef).html();
    $.ajax({
      url: '<?= URL::BASE_URL ?>Antrian/sendNotif/' + countMember + "/1",
      data: {
        hp: hpNya,
        text: textNya,
        ref: refNya,
        time: timeNya,
        idPelanggan: id_pelanggan
      },
      type: "POST",
      beforeSend: function() {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function(res) {
        if (res == 0) {
          loadDiv();
        } else {
          alert(res);
        }
      },
      complete: function() {
        $(".loaderDiv").fadeOut("slow");
      }
    });
  });

  $("a.sendNotifMember").on('click', function(e) {
    klikNotif += 1;
    if (klikNotif > 1) {
      return;
    }
    $(this).fadeOut("slow");
    e.preventDefault();
    var refNya = $(this).attr('data-ref');
    $.ajax({
      url: '<?= URL::BASE_URL ?>Member/sendNotifDeposit/' + refNya,
      data: {},
      type: "POST",
      beforeSend: function() {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function() {
        loadDiv();
      },
      complete: function() {
        $(".loaderDiv").fadeOut("slow");
      }
    });
  });

  $("a.bayarPasMulti").on('click', function(e) {
    $("input#bayarBill").val(totalBill);
    bayarBill();
  });

  $("select.metodeBayarBill").on("keyup change", function() {
    if ($(this).val() == 2) {
      $("tr#nTunaiBill").show();
    } else {
      $("tr#nTunaiBill").hide();
    }
  });

  var userClick = "";
  $("select.userChange").change(function() {
    userClick = $('select.userChange option:selected').text();
  });

  var click = 0;

  $("span.editRak").on('click', function() {
    click = click + 1;
    if (click != 1) {
      return;
    }

    var id_value = $(this).attr('data-id');
    var value = $(this).attr('data-value');
    var value_before = value;
    var span = $(this);
    var valHtml = $(this).html();
    span.html("<input type='text' maxLength='2' id='value_' style='text-align:center;width:30px' value='" + value.toUpperCase() + "'>");

    $("#value_").focus();
    $("#value_").focusout(function() {
      var value_after = $(this).val();
      if (value_after === value_before) {
        span.html(valHtml);
        click = 0;
      } else {
        $.ajax({
          url: '<?= URL::BASE_URL ?>Antrian/updateRak/',
          data: {
            'id': id_value,
            'value': value_after,
          },
          type: 'POST',
          beforeSend: function() {
            $(".loaderDiv").fadeIn("fast");
          },
          success: function() {
            span.html(value_after.toUpperCase());
            span.attr('data-value', value_after.toUpperCase());
            click = 0;
          },
          complete: function() {
            $(".loaderDiv").fadeOut("slow");
          }
        });
      }
    });
  });

  $("span.editPack").on('click', function() {
    click = click + 1;
    if (click != 1) {
      return;
    }

    var id_value = $(this).attr('data-id');
    var value = $(this).attr('data-value');
    var value_before = value;
    var span = $(this);
    var valHtml = $(this).html();
    span.html("<input type='number' min='0' id='value_' style='text-align:center;width:45px' value='" + value + "'>");

    $("#value_").focus();
    $("#value_").focusout(function() {
      var value_after = $(this).val();
      if (value_after === value_before) {
        span.html(valHtml);
        click = 0;
      } else {
        $.ajax({
          url: '<?= URL::BASE_URL ?>Antrian/updateRak/1',
          data: {
            'id': id_value,
            'value': value_after,
          },
          type: 'POST',
          beforeSend: function() {
            $(".loaderDiv").fadeIn("fast");
          },
          success: function() {
            loadDiv();
          },
          complete: function() {
            $(".loaderDiv").fadeOut("slow");
          }
        });
      }
    });
  });

  $("span.editHanger").on('click', function() {
    click = click + 1;
    if (click != 1) {
      return;
    }

    var id_value = $(this).attr('data-id');
    var value = $(this).attr('data-value');
    var value_before = value;
    var span = $(this);
    var valHtml = $(this).html();
    span.html("<input type='number' min='0' id='value_' style='text-align:center;width:45px' value='" + value + "'>");

    $("#value_").focus();
    $("#value_").focusout(function() {
      var value_after = $(this).val();
      if (value_after === value_before) {
        span.html(valHtml);
        click = 0;
      } else {
        $.ajax({
          url: '<?= URL::BASE_URL ?>Antrian/updateRak/2',
          data: {
            'id': id_value,
            'value': value_after,
          },
          type: 'POST',
          beforeSend: function() {
            $(".loaderDiv").fadeIn("fast");
          },
          success: function() {
            loadDiv();
          },
          complete: function() {
            $(".loaderDiv").fadeOut("slow");
          }
        });
      }
    });
  });

  function downloadJPG(id, noref) {
    var idDIV = "print" + id;
    var scale = 2;
    $("#" + idDIV).removeClass("d-none");
    var domNode = document.getElementById(idDIV);
    domtoimage.toPng(domNode, {
      width: domNode.clientWidth * scale,
      height: domNode.clientHeight * scale,
      style: {
        transform: "scale(" + scale + ")",
        transformOrigin: "top left"
      }
    }).then(function(dataUrl) {
      var link = document.createElement('a');
      link.download = noref + ".png";
      link.href = dataUrl;
      link.click();
      $("#" + idDIV).addClass("d-none");
    });
  }

  function PrintContentRef(id, idPelanggan) {
    var txtPoin = $('span#poin' + id).html();
    var countMember = $('span#member' + id).html();

    if (txtPoin.length > 0) {
      $.ajax({
        url: '<?= URL::BASE_URL ?>Antrian/getPoin/' + idPelanggan,
        data: {
          'id': idPelanggan,
        },
        type: 'POST',
        success: function(response) {
          $('span.saldoPoin' + id).html(response);
          if (countMember > 0) {
            $.ajax({
              url: '<?= URL::BASE_URL ?>Member/textSaldo',
              data: {
                'id': idPelanggan,
              },
              type: 'POST',
              success: function(result) {
                $('td.textMember' + id).html(result);
                Print(id);
              },
            });
          } else {
            Print(id);
          }
        },
      });
    } else {
      if (countMember > 0) {
        $.ajax({
          url: '<?= URL::BASE_URL ?>Member/textSaldo',
          data: {
            'id': idPelanggan,
          },
          type: 'POST',
          success: function(result) {
            $('td.textMember' + id).html(result);
            Print(id);
          },
        });
      } else {
        Print(id);
      }
    }
  }

  $("input#bayarBill").on("keyup change", function() {
    bayarBill();
  });

  function bayarBill() {
    var dibayar = parseInt($('input#bayarBill').val());
    var kembalian = parseInt(dibayar) - parseInt(totalBill);
    if (kembalian > 0) {
      $('input#kembalianBill').val(kembalian);
    } else {
      $('input#kembalianBill').val(0);
    }
  }

  var totalBill = $("span#totalBill").attr("data-total");

  $("input.cek").change(function() {
    var jumlah = $(this).attr("data-jumlah");
    let refRekap = $(this).attr("data-ref");

    if ($(this).is(':checked')) {
      totalBill = parseInt(totalBill) + parseInt(jumlah);
      json_rekap[0][refRekap] = jumlah;
    } else {
      delete json_rekap[0][refRekap];
      totalBill = parseInt(totalBill) - parseInt(jumlah);
    }

    $("span#totalBill")
      .html(totalBill.toLocaleString('en-US')).attr("data-total", totalBill);
    bayarBill();
  })

  function Print(id) {
    var divContents = document.getElementById("print" + id).innerHTML;
    var a = window.open('');
    a.document.write('<title>Print Page</title>');
    a.document.write('<body style="margin-left: <?= $this->mdl_setting['print_ms'] ?>mm">');
    a.document.write(divContents);
    var window_width = $(window).width();
    a.print();

    if (window_width > 600) {
      a.close()
    } else {
      setTimeout(function() {
        a.close()
      }, 60000);
    }

    loadDiv();
  }

  function cekQris(ref_id, jumlah) {
    $.ajax({
      url: '<?= URL::BASE_URL ?>Kas/cek_qris/' + ref_id + '/' + jumlah,
      data: {},
      type: 'POST',
      beforeSend: function() {
        $(".loaderDiv").fadeIn("fast");
      },
      success: function(res) {
        if (res == 0) {
          loadDiv();
        }
      },
      complete: function() {
        $(".loaderDiv").fadeOut("slow");
      }
    });
  }

  function loadDiv() {
    var modeView = "<?= $modeView ?>";
    if (modeView != 2) {
      var pelanggan = $("select[name=pelanggan").val();
      $("div#load").load("<?= URL::BASE_URL ?>Operasi/loadData/" + pelanggan + "/0");
    }
    if (modeView == 2) {
      var pelanggan = $("select[name=pelanggan").val();
      var tahun = $("select[name=tahun").val();
      $("div#load").load("<?= URL::BASE_URL ?>Operasi/loadData/" + pelanggan + "/" + tahun);
    }
  }

  function bonJPG(id, noref, idPelanggan) {
    var txtPoin = $('span#poin' + id).html();
    var countMember = $('span#member' + id).html();

    if (txtPoin.length > 0) {
      $.ajax({
        url: '<?= URL::BASE_URL ?>Antrian/getPoin/' + idPelanggan,
        data: {},
        type: 'POST',
        success: function(response) {
          $('span.saldoPoin' + id).html(response);
          if (countMember > 0) {
            $.ajax({
              url: '<?= URL::BASE_URL ?>Member/textSaldo',
              data: {
                'id': idPelanggan,
              },
              type: 'POST',
              success: function(result) {
                $('td.textMember' + id).html(result);
                downloadJPG(id, noref)
              },
            });
          } else {
            downloadJPG(id, noref)
          }
        },
      });
    } else {
      if (countMember > 0) {
        $.ajax({
          url: '<?= URL::BASE_URL ?>Member/textSaldo',
          data: {
            'id': idPelanggan,
          },
          type: 'POST',
          success: function(result) {
            $('td.textMember' + id).html(result);
            downloadJPG(id, noref)
          },
        });
      } else {
        downloadJPG(id, noref)
      }
    }
  }
</script>