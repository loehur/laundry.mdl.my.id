<style>
  table {
    border-radius: 15px;
    overflow: hidden
  }
</style>

<?php
if (count($data['data_main']) == 0) {
?>
  <div class="container-fluid">
    <div class="row">
      <div class='col p-0 m-2 rounded' style='max-width:400px;'>
        <div class='bg-white p-2 rounded'>
          Tidak ada Data
        </div>
      </div>
    </div>
  </div>

<?php
  exit();
}

$kodeCabang = $this->dCabang['kode_cabang'];
$modeView = $data['modeView'];
?>

<div id="colAntri" class="container-fluid">
  <div class="row p-1">
    <?php
    $prevPoin = 0;
    $arrRef = array();

    $arrPoin = array();
    $jumlahRef = 0;

    foreach ($data['data_main'] as $a) {
      $ref = $a['no_ref'];
      if (isset($arrRef[$ref])) {
        $arrRef[$ref] += 1;
      } else {
        $arrRef[$ref] = 1;
      }
    }

    $no_urut = 0;
    $urutRef = 0;
    $arrCount_Noref = 0;
    $listPrint = "";
    $listNotif = "";
    $arrGetPoin = array();
    $arrTotalPoin = array();
    $arrBayar = array();

    $enHapus = true;
    $arrTuntas = array();

    $cols = 0;
    $countMember = 0;

    $rekapAntrian = "";
    $arrRekapAntrian = array();

    foreach ($data['data_main'] as $a) {
      $no_urut += 1;
      $id = $a['id_penjualan'];
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
      $id_ambil = $a['id_user_ambil'];
      $tgl_ambil = $a['tgl_ambil'];
      $idPoin = $a['id_poin'];
      $perPoin = $a['per_poin'];
      $timeRef = $f1;
      $member = $a['member'];
      $showMember = "";
      $id_harga = $a['id_harga'];
      $countMember = $countMember + $member;
      $arrCount_Noref = $arrRef[$noref];

      if ($f12 <> 0) {
        $tgl_selesai = date('d-m-Y', strtotime($f1 . ' +' . $f12 . ' days +' . $f13 . ' hours'));
      } else {
        $tgl_selesai = date('d-m-Y H:i', strtotime($f1 . ' +' . $f12 . ' days +' . $f13 . ' hours'));
      }

      $pelanggan = '';
      $no_pelanggan = '';
      $modeNotif = 1;

      $modeNotifShow = "NONE";
      foreach ($this->pelanggan as $c) {
        if ($c['id_pelanggan'] == $f17) {
          $pelanggan = $c['nama_pelanggan'];
          $no_pelanggan = $c['nomor_pelanggan'];
          $modeNotif = $c['id_notif_mode'];

          foreach ($this->dNotifMode as $a) {
            if ($modeNotif == $a['id_notif_mode']) {
              $modeNotifShow  = $a['notif_mode'];
            }
          }
        }
      }

      if ($modeNotifShow == "Whatsapp") {
        $modeNotifShow = "WA";
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
        $show_qty = $f6 . $satuan . " (Min. " . $f16 . $satuan . ")";
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

      $pelanggan_show = $pelanggan;
      if (strlen($pelanggan) > 20) {
        $pelanggan_show = substr($pelanggan, 0, 20) . "...";
      }

      if ($no_urut == 1) {
        $adaBayar = false;
        $cols++;
        echo "<div id='grid" . $noref . "' class='col backShow " . strtoupper($pelanggan) . " p-0 m-1 rounded' style='max-width:400px;'><div class='bg-white rounded'>";
        echo "<table class='table table-sm m-0 rounded w-100 bg-white'>";
        $lunas = false;
        $totalBayar = 0;
        $subTotal = 0;
        $enHapus = true;
        $urutRef++;
        $buttonNotif = "<a href='#' data-idPelanggan = '" . $f17 . "' data-urutRef='" . $urutRef . "' data-hp='" . $no_pelanggan . "' data-mode='" . $modeNotif . "' data-ref='" . $noref . "' data-time='" . $timeRef . "' class='text-dark sendNotif'><i class='far fa-paper-plane'></i> " . $modeNotifShow . " <span id='notif" . $urutRef . "'></span></a>";

        foreach ($data['notif'] as $notif) {
          if ($notif['no_ref'] == $noref) {
            $stGet = $notif['status'];
            switch ($stGet) {
              case 1:
              case 5:
                $stNotif = "<i class='far fa-circle text-warning text-bold'></i>";
                break;
              case 2:
                $stNotif = "<i class='fas fa-check-circle text-success'></i>";
                break;
              default:
                $stNotif = "<i class='fas fa-exclamation-circle text-danger'></i>";
                break;
            }
            $buttonNotif = "<span>" . $modeNotifShow . " " . $stNotif . "</span>";
          }
        }

        $buttonDirectWA = "<a href='#' data-idPelanggan = '" . $f17 . "' data-urutRef='" . $urutRef . "' data-hp='" . $no_pelanggan . "' data-mode='" . $modeNotif . "' data-ref='" . $noref . "' data-time='" . $timeRef . "' class='directWA'><i class='fas fa-paper-plane'></i> DWA</span></a>";

        $dateToday = date("Y-m-d");
        if (strpos($f1, $dateToday) !== FALSE) {
          $classHead = 'table-primary';
        } else {
          $classHead = 'table-success';
        }

        $idLabel = $noref . "100";

        echo "<tr class=' " . $classHead . " row" . $noref . "' id='tr" . $id . "'>";
        echo "<td class='text-center'><a href='#' class='text-dark' onclick='PrintContentRef(" . $urutRef . ", " . $f17 . ")'><i class='fas fa-print'></i></a></td>";
        echo "<td colspan='3'>
          
          <span style='cursor:pointer' title='" . $pelanggan . "'><b>" . strtoupper($pelanggan_show) . "</b></span>
          <div class='float-right'>
          <small>
          <span class='bg-white rounded pr-1 pl-1'>" . $buttonNotif . "</span>
          &nbsp;<a href='#'><span onclick=Print(" . $idLabel . ") class='bg-white rounded pr-1 pl-1'><i class='fa fa-tag'></i></span></a>
          <a href='#' class='tambahCas' data-ref=" . $noref . " data-tr='id_transaksi'><span class='bg-white rounded pr-1 pl-1' data-bs-toggle='modal' data-bs-target='#exampleModalSurcas'><i class='fa fa-plus'></i></span></a>
          <span class='bg-white rounded pr-1 pl-1'><a class='text-dark' href='" . $this->BASE_URL . "I/i/" . $this->id_laundry . "/" . $f17 . "' target='_blank'><i class='fas fa-file-invoice'></i> Bill</a></span>
          <span class='bg-white rounded pr-1 pl-1'>" .  $buttonDirectWA  . "</span>
          <a class='text-dark bg-white rounded pr-1 pl-1' href='#' onclick='bonJPG(" . $urutRef . "," . $noref . ", " . $f17 . ")' class=''><i class='far fa-arrow-alt-circle-down'></i> JPG</a>
          </small>
          </div>
          
          </td>";
        echo "</tr>";
      }

      foreach ($data['kas'] as $byr) {
        if ($byr['ref_transaksi'] ==  $noref && $byr['status_mutasi'] == 3) {
          $idKas = $byr['id_kas'];
          $arrBayar[$noref][$idKas] = $byr['jumlah'];
          $totalBayar = array_sum($arrBayar[$noref]);
        }
        if ($byr['ref_transaksi'] ==  $noref) {
          $adaBayar = true;
        }
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
      $list_layanan = "<small><b><i class='fas fa-check-circle text-success'></i> " . $karyawan . "</b> Diterima <span style='white-space: pre;'>" . substr($f1, 5, 11) . "</span></small><br>";
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
                  foreach ($data['notif_penjualan'] as $notif) {
                    if ($notif['no_ref'] == $id) {
                      $stGet = $notif['status'];
                      switch ($stGet) {
                        case 1:
                        case 5:
                          $stNotif = "<i class='far fa-circle text-warning text-bold'></i>";
                          break;
                        case 2:
                          $stNotif = "<i class='fas fa-check-circle text-success'></i>";
                          break;
                        default:
                          $stNotif = "<i class='fas fa-exclamation-circle text-danger'></i>";
                          break;
                      }
                      $buttonNotifSelesai = "<small><span>" . $stNotif . " <b>" . $modeNotifShow . "</b> Selesai " . substr($notif['updateTime'], 5, 11) . "</span></small><br>";
                    }
                  }
                }

                $list_layanan = $list_layanan . "<small><b><i class='fas fa-check-circle text-success'></i> " . $user . "</b> " . $c['layanan'] . " <span style='white-space: pre;'>" . substr($o['insertTime'], 5, 11) . "</span></small><br>" . $buttonNotifSelesai;

                $doneLayanan++;
                $enHapus = false;
              }
            }
            if ($check == 0) {
              if ($b == $endLayanan) {
                $list_layanan = $list_layanan . "<span id='" . $id . $b . "' data-layanan='" . $c['layanan'] . "' data-value='" . $c['id_layanan'] . "' data-id='" . $id . "' data-ref='" . $noref . "' data-bs-toggle='modal' data-bs-target='#exampleModal' class='endLayanan'><small><a href='' class='text-dark'><i class='fas fa-info-circle text-info'></i> " . $c['layanan'] . "</a></small></span><br><span class='d-none ambilAfterSelesai" . $id . $b . "'><small><a href='#' data-id='" . $id . "' data-ref='" . $noref . "' data-bs-toggle='modal' data-bs-target='#exampleModal4' class='ambil text-dark ambil" . $id . "'><i class='fas fa-info-circle'></i> Ambil</a></small></span>";
              } else {
                $list_layanan = $list_layanan . "<span id='" . $id . $b . "' data-layanan='" . $c['layanan'] . "' data-value='" . $c['id_layanan'] . "' data-id='" . $id . "' data-ref='" . $noref . "' data-bs-toggle='modal' data-bs-target='#exampleModal' class='addOperasi'><small><a href='' class='text-dark'><i class='fas fa-info-circle text-info'></i> " . $c['layanan'] . "</a></small></span> <br>";
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

      if ($endLayananDone == true) {
        $buttonDirectWAselesai = "<a href='#' data-idPelanggan = '" . $f17 . "' data-id='" . $id . "' data-hp='" . $no_pelanggan . "' class='directWA_selesai'> Direct WA </i></span></a>";
      } else {
        $buttonDirectWAselesai = "";
      }

      $ambilDone = false;
      if ($id_ambil > 0) {
        $list_layanan = $list_layanan . "<small><b><i class='fas fa-check-circle text-success'></i> " . $userAmbil . "</b> Ambil <span style='white-space: pre;'>" . substr($tgl_ambil, 5, 11) . "</span></small><br>";
        $ambilDone = true;
      }

      $buttonAmbil = "";
      if ($id_ambil == 0 && $endLayananDone == true) {
        $buttonAmbil = "<small><a href='#' data-id='" . $id . "' data-ref='" . $noref . "' data-bs-toggle='modal' data-bs-target='#exampleModal4' class='ambil text-dark ambil" . $id . "'><i class='fas fa-info-circle'></i> Ambil</a></small>";
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
          $show_total_print = "-" . $show_diskon . " <del>Rp" . number_format($f7 * $qty_real) . "</del> Rp" . number_format($total);
          $show_total_notif = "Rp" . number_format($f7 * $qty_real) . "-" . $show_diskon . " Rp" . number_format($total) . " ";
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
        $classDurasi = "border border-1 rounded pr-1 pl-1 bg-danger";
      }

      $classTRDurasi = "";
      if (strpos($durasi, "-D") !== false) {
        $classTRDurasi = "table-warning";
      }

      echo "<tr id='tr" . $id . "'class='row" . $noref . " " . $classTRDurasi . " table-borderless'>";

      echo "<td nowrap class='text-center'><a href='#' class='mb-1 text-secondary' onclick='Print(" . $id . ")'><i class='fas fa-print'></i></a><br>";
      if (strlen($letak) > 0) {
        $statusRak = "<h6 class='m-0 p-0'><span data-id='" . $id . "' data-value='" . strtoupper($letak) . "' class='text-success m-0 p-0 font-weight-bold editRak noRAK" . $id . "'>" . strtoupper($letak) . "</span></h6>";
      } else {
        $statusRak = "<h6 class='m-0 p-0'><span data-id='" . $id . "' data-value='" . strtoupper($letak) . "' class='text-success m-0 p-0 font-weight-bold editRak noRAK" . $id . "'>[ ]</span></h6>";
      }

      if ($endLayananDone == false) {
        $statusRak = "<span class='text-success editRak noRAK" . $id . "'></span>";
      }
      echo $statusRak;
      echo "</td>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>[" . $id . "] " . $buttonDirectWAselesai . "</small><br><b>" . $kategori . "</b><span class='badge badge-light'></span>
        <br><span class='" . $classDurasi . "' style='white-space: pre;'>" . $durasi . " (" . $f12 . "h " . $f13 . "j)</span><br><b>" . $show_qty . "</b> " . $tampilDiskon . "<br>" . $itemList . "</td>";
      echo "<td nowrap>" . $list_layanan . $buttonAmbil . "</td>";
      echo "<td class='text-right'>" . $show_total . "</td>";
      echo "</tr>";
      echo "<tr class='" . $classTRDurasi . "'>";
      if (strlen($f8) > 0) {
        echo "<td style='border-top:0' colspan='5' class='m-0 pt-0'><span class='badge badge-warning'>" . $f8 . "</span></td>";
      } else {
        echo "<td style='border-top:0' colspan='5' class='m-0 pt-0'><span class='badge badge-warning'></span></td>";
      }
      echo " </tr>";

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
              $statusM = "<b><i class='fas fa-check-circle text-success'></i></b> " . $notenya . " ";
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

          $showMutasi = $showMutasi . "<small>" . $statusM . "<b>#" . $ka['id_kas'] . " " . $userKas . "</b> " . substr($ka['insertTime'], 5, 11) . " " . $nominal . "</small><br>";
        }
      }

      $spkPrint = "";
      $spkPrint = "<tr><td colspan='2'>[" . $this->dCabang['kode_cabang'] . "-" . $id . "] <br>Selesai [<b>" . $tgl_selesai . "</b>]</td></tr><tr><td>" . $penjualan . "</td><td>" . $kategori . "</td></tr><tr><td><b>" . strtoupper($durasi) . "</b></td><td><b>" . strtoupper($list_layanan_print) . "</b></td></tr><tr><td><b>" . $show_qty . "</b></td><td style='text-align: right;'><b>" . $show_total_print . "</b></td></tr><tr><td colspan='2'>" . $itemListPrint . "</td></tr><tr><td colspan='2'>" . $showNote . "</td></tr><tr><td colspan='2' style='border-bottom:1px dashed black;'></td></tr>";
      $listPrint = $listPrint . $spkPrint;

      $listNotif = $listNotif . "[" . $this->dCabang['kode_cabang'] . "-" . $id . "] " . $kategori . " " . $durasi . " " . $list_layanan_print . $show_qty . " " . $show_total_notif . ", ";
      echo "<span class='d-none selesai" . $id . "' data-hp='" . $no_pelanggan . "' data-mode='" . $modeNotif . "'>Pak/Bu " . strtoupper($pelanggan) . ", Laundry Item [" . $kodeCabang . "-" . $id_harga . "-" . $id . "] Sudah Selesai. " . $show_total_notif . ". laundry.mdl.my.id/I/i/" . $this->id_laundry . "/" . $f17 . "</span>";

    ?> <tr class="d-none">
        <td>
          <div class="d-none" id="print<?= $id ?>" style="width:50mm;background-color:white; border:1px solid grey">
            <style>
              html .table {
                font-family: 'Titillium Web', sans-serif;
              }

              html .content {
                font-family: 'Titillium Web', sans-serif;
              }

              html body {
                font-family: 'Titillium Web', sans-serif;
              }

              hr {
                border-top: 1px dashed black;
              }
            </style>
            <table style="width:42mm; font-size:x-small; margin-top:10px; margin-bottom:10px">
              <tr>
                <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                  <b> <?= $this->dLaundry['nama_laundry'] ?> [ <?= $this->dCabang['kode_cabang'] ?></b> ]<br>
                  <?= $this->dCabang['alamat'] ?>
                </td>
              </tr>
              <tr>
                <td colspan="2" style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                  <font size='2'><b><?= strtoupper($pelanggan) ?></b></font><br>
                  Ref. <?= $noref ?><br>
                  <?= $f1 ?>
                </td>
              </tr>
              <?= $spkPrint ?>
              <tr>
                <td colspan="2">.<br>.<br>.<br>.<br>.<br>.<br>
                  <hr>
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
      <?php

      if ($arrCount_Noref == $no_urut) {

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
            $tglCas = "<small><b><i class='fas fa-check-circle text-success'></i> " . $userCas . "</b> Input <span style='white-space: pre;'>" . substr($sca['insertTime'], 5, 11) . "</span></small><br>";
            echo "<tr><td></td><td>" . $surcasNya . "</td><td>" . $tglCas . "</td><td align='right'>Rp" . number_format($jumlahCas) . "</td></tr>";
            $subTotal += $jumlahCas;

            $spkPrint = "<tr><td colspan='2'>[" . $this->dCabang['kode_cabang'] . "-S" . $id_surcas . "] <br><b>" . $surcasNya . "</b></td></tr><tr><td></td><td style='text-align: right;'><b>Rp" . number_format($jumlahCas) . "</b></td></tr><tr><td colspan='2' style='border-bottom:1px dashed black;'></td></tr>";
            $listPrint = $listPrint . $spkPrint;

            $listNotif = $listNotif . "[" . $this->dCabang['kode_cabang'] . "-S" . $id_surcas . "] " . $surcasNya . " Rp" . number_format($jumlahCas) . ", ";
          }
        }

        if ($totalBayar > 0) {
          $enHapus = false;
        }
        $sisaTagihan = intval($subTotal) - $totalBayar;
        $textPoin = "";
        if (isset($arrTotalPoin[$noref]) && $arrTotalPoin[$noref] > 0) {
          $textPoin = "(Poin " . $arrTotalPoin[$noref] . ") ";
        }
        echo "<span class='d-none' id='poin" . $urutRef . "'>" . $textPoin . "</span>";
        echo "<span class='d-none' id='member" . $urutRef . "'>" . $countMember . "</span>";

        $buttonHapus = "";
        if ($enHapus == true || $this->id_privilege >= 100) {
          $buttonHapus = "<small><a href='#' data-ref='" . $noref . "' class='hapusRef mb-1'><i class='fas fa-trash-alt text-secondary'></i></a><small> ";
        }
        if ($sisaTagihan < 1) {
          $lunas = true;
        }

        echo "<tr class='row" . $noref . "'>";
        echo "<td class='text-center'><span class='d-none'>" . $pelanggan . "</span>" . $buttonHapus . "</td>";
        if ($lunas == true && $endLayananDone == true && $ambilDone == true) {
          if ($modeView <> 2) { // 2 SUDAH TUNTAS
            array_push($arrTuntas, $noref);
          }
        }
        if ($lunas == false) {
          echo "<td class='buttonBayar" . $noref . "'><small><a href='#' data-ref='" . $noref . "' data-bayar='" . $sisaTagihan . "' data-idPelanggan='" . $f17 . "' data-bs-toggle='modal' data-bs-target='#exampleModal2' class='bayar mb-1 text-dark'><i class='fas fa-tag text-danger'></i> Bayar</a></small></td>";
          echo "<td nowrap colspan='3' class='text-right'><small><font color='green'>" . $textPoin . "</font></small> <span class='showLunas" . $noref . "'></span><b> Rp" . number_format($subTotal) . "</b><br>";
        } else {
          echo "<td nowrap colspan='3' class='text-right'><small><font color='green'>" . $textPoin . "</font></small>  <b><i class='fas fa-check-circle text-success'></i> Rp" . number_format($subTotal) . "</b><br>";
        }
        echo "</td></tr>";

        if ($adaBayar == true) {
          $classMutasi = "";
        } else {
          $classMutasi = "d-none";
        }

        echo "<tr class='row" . $noref . " sisaTagihan" . $noref . " " . $classMutasi . "'>";
        echo "<td nowrap colspan='4' class='text-right'>";
        echo $showMutasi;
        echo "<span class='text-danger sisaTagihan" . $noref . "'>";
        if (($sisaTagihan < intval($subTotal)) && (intval($sisaTagihan) > 0)) {
          echo  "<b><i class='fas fa-exclamation-circle'></i> Sisa Rp" . number_format($sisaTagihan) . "</b>";
        }
        echo "</span>";
        echo "</td>";
        echo "</tr>";


        echo "</tbody></table>";
        echo "</div></div>";

        if ($cols == 2) {
          echo '<div class="w-100"></div>';
          $cols = 0;
        }

        if ($member > 0) {
          $totalText = "";
        } else {
          $totalText = "[Total] Rp" . number_format($subTotal) . ", [Bayar] Rp" . number_format($totalBayar) . ". " . $textPoin;
        }

      ?>
        <!-- NOTIF -->
        <div class="d-none">
          <span id="<?= $urutRef ?>">Pak/Bu <?= strtoupper($pelanggan) ?>, Diterima Laundry <?= $listNotif . $totalText ?>laundry.mdl.my.id/I/i/<?= $this->id_laundry . "/" . $f17 ?></span>
        </div>
        <div class="d-none" id="print<?= $urutRef ?>" style="width:50mm;background-color:white; padding-bottom:10px">
          <style>
            html .table {
              font-family: 'Titillium Web', sans-serif;
            }

            html .content {
              font-family: 'Titillium Web', sans-serif;
            }

            html body {
              font-family: 'Titillium Web', sans-serif;
            }

            hr {
              border-top: 1px dashed black;
            }
          </style>
          <table style="width:42mm; font-size:x-small; margin-top:10px; margin-bottom:10px">
            <tr>
              <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                <b> <?= $this->dLaundry['nama_laundry'] ?> [ <?= $this->dCabang['kode_cabang'] ?></b> ]<br>
                <?= $this->dCabang['alamat'] ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                <font size='2'><b><?= strtoupper($pelanggan) ?></b></font><br>
                Ref. <?= $noref ?><br>
                <?= $f1 ?>
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
              <td colspan="2">.<br>.<br>.<br>.<br>.<br>.<br>
                <hr>
              </td>
            </tr>
          </table>
        </div>

        <div class="d-none" id="print<?= $noref ?>100" style="width:50mm;background-color:white; padding-bottom:10px">
          <style>
            html .table {
              font-family: 'Titillium Web', sans-serif;
            }

            html .content {
              font-family: 'Titillium Web', sans-serif;
            }

            html body {
              font-family: 'Titillium Web', sans-serif;
            }

            hr {
              border-top: 1px dashed black;
            }
          </style>
          <table style="width:42mm; margin-top:10px; margin-bottom:10px">
            <tr>
              <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                <br>
                <font size='1'><?= $this->dLaundry['nama_laundry'] ?> [<b><?= $this->dCabang['kode_cabang'] ?></b> ]<br>
                  <?= $f1 ?></font>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                <font size='5'><b><?= strtoupper($pelanggan) ?></b></font>
              </td>
            </tr>
            <tr>
              <td colspan="2" style="text-align: left;border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                .<br>.<br>.<br>.<br>.<br>.<br>
              </td>
            </tr>
          </table>
        </div>

    <?php
        $totalBayar = 0;
        $sisaTagihan = 0;
        $no_urut = 0;
        $subTotal = 0;
        $listPrint = "";
        $listNotif = "";
        $enHapus = true;
      }
    }
    ?>
  </div>
</div>

<form data-operasi="" class="ajax" action="<?= $this->BASE_URL; ?>Antrian/bayar" method="POST">
  <div class="modal" id="exampleModal2">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pembayaran</h5>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Jumlah (Rp)</label>
                  <input type="number" name="maxBayar" class="form-control float jumlahBayar" id="exampleInputEmail1" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Bayar (Rp) <a class="btn badge badge-primary bayarPas">Bayar Pas (Click)</a></label>
                  <input type="number" name="f1" class="form-control dibayar" id="exampleInputEmail1" required>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Kembalian (Rp)</label>
                  <input type="number" class="form-control float kembalian" id="exampleInputEmail1" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Metode</label>
                  <select name="f4" class="form-control form-control-sm metodeBayar" style="width: 100%;" required>
                    <?php foreach ($this->dMetodeMutasi as $a) { ?>
                      <option value="<?= $a['id_metode_mutasi'] ?>"><?= $a['metode_mutasi'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Penerima</label>
                  <select name="f2" class="form-control form-control-sm tize userChangeBayar" style="width: 100%;" required>
                    <option value="" selected disabled></option>
                    <optgroup label="<?= $this->dLaundry['nama_laundry'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
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
                  <input type="hidden" class="idItem" name="f3" value="" required>
                  <input type="hidden" class="idPelanggan" name="idPelanggan" value="" required>
                </div>
              </div>
            </div>
            <div class="row" id="nTunai">
              <div class="col-sm-12">
                <div class="form-group">
                  <div class="form-group">
                    <label for="exampleInputEmail1" class="text-danger">Catatan Non Tunai <small>(Contoh: BRI)</small></label>
                    <input type="text" name="noteBayar" maxlength="10" class="form-control border-danger" id="exampleInputEmail1" placeholder="" style="text-transform:uppercase">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Bayar</button>
        </div>
      </div>
    </div>
  </div>
</form>

<form class="ajax" data-operasi="" action="<?= $this->BASE_URL; ?>Antrian/ambil" method="POST">
  <div class="modal" id="exampleModal4">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ambil Laundry</b></h5>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Pengembali</label>
              <select name="f1" class="ambil form-control form-control-sm tize userChange" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <optgroup label="<?= $this->dLaundry['nama_laundry'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
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

<form data-operasi="" class="operasi ajax" action="<?= $this->BASE_URL; ?>Antrian/operasi" method="POST">
  <div class="modal" id="exampleModal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Selesai <b class="operasi"></b>!</h5>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Karyawan</label>
              <select name="f1" class="operasi form-control tize form-control-sm userChange" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <optgroup label="<?= $this->dLaundry['nama_laundry'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
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
              <input type="hidden" class="valueItem" name="f3" value="" required>
              <input type="hidden" class="textNotif" name="text" value="" required>
              <input type="hidden" class="hpNotif" name="hp" value="" required>
              <input type="hidden" class="modeNotif" name="mode" value="" required>
            </div>
            <div class="form-group letakRAK">
              <label for="exampleInputEmail1">Letak / Rak</label>
              <input id='letakRAK' type="text" maxlength="2" name="rak" style="text-transform: uppercase" class="form-control" id="exampleInputEmail1">
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

<form class="ajax" action="<?= $this->BASE_URL; ?>Antrian/surcas" method="POST">
  <div class="modal" id="exampleModalSurcas">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Surcharge/Biaya Tambahan</h5>
        </div>
        <div class="modal-body">
          <div class="card-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Jenis Surcharge</label>
              <select name="surcas" class="form-control form-control-sm" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <?php foreach ($this->surcas as $sc) { ?>
                  <option value="<?= $sc['id_surcas_jenis'] ?>"><?= $sc['surcas_jenis'] ?></option>
                <?php } ?>
              </select>
            </div>
            <input type="hidden" name="no_ref" id="id_transaksi">
            <div class="form-group">
              <label for="exampleInputEmail1">Jumlah Biaya</label>
              <input type="number" name="jumlah" class="form-control">
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Di input Oleh</label>
              <select name="user" class="form-control tize form-control-sm userSurcas" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <optgroup label="<?= $this->dLaundry['nama_laundry'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
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

  $(document).ready(function() {
    clearTuntas();
    var diBayar = 0;
    var noref = '';
    var idRow = '';
    var idtargetOperasi = '';
    $("div#nTunai").hide();
    $('select.tize').selectize();
  });

  function clearTuntas() {
    var dataNya = '<?= serialize($arrTuntas) ?>';
    var countArr = <?= count($arrTuntas) ?>;
    var arrTuntas = <?= json_encode($arrTuntas) ?>;

    if (countArr > 0) {
      $.ajax({
        url: '<?= $this->BASE_URL ?>Antrian/clearTuntas',
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
      success: function() {
        loadDiv();
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
    var modeNya = $('span.selesai' + idNya).attr('data-mode');
    $("input.textNotif").val(textNya);
    $("input.hpNotif").val(hpNya);
    $("input.modeNotif").val(modeNya);
    idRow = idNya;
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
    var modeNya = $('span.selesai' + idNya).attr('data-mode');
    $("input.textNotif").val(textNya);
    $("input.hpNotif").val(hpNya);
    $("input.modeNotif").val(modeNya);
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
  $("a.bayar").on('click', function(e) {
    e.preventDefault();
    totalTagihan = 0;
    $('form').attr("data-operasi", "operasiBayar");
    var refNya = $(this).attr('data-ref');
    var bayarNya = $(this).attr('data-bayar');
    var id_pelanggan = $(this).attr('data-idPelanggan');
    $("input.idItem").val(refNya);
    $("input.jumlahBayar").val(bayarNya);
    $("input.idPelanggan").val(id_pelanggan);
    $("input.jumlahBayar").attr({
      'max': bayarNya
    });
    totalTagihan = bayarNya;
    noref = refNya;
  });

  $('.tambahCas').click(function() {
    noref = $(this).attr('data-ref');
    idNya = $(this).attr('data-tr');
    $("#" + idNya).val(noref);
  });

  $("a.hapusRef").on('dblclick', function(e) {
    e.preventDefault();
    var refNya = $(this).attr('data-ref');
    $.ajax({
      url: '<?= $this->BASE_URL ?>Antrian/hapusRef',
      data: {
        ref: refNya,
      },
      type: "POST",
      success: function(response) {
        $("tr.row" + refNya).fadeOut(1000);
      },
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

  $("a.sendNotif").on('click', function(e) {
    e.preventDefault();
    var urutRef = $(this).attr('data-urutRef');
    var id_pelanggan = $(this).attr('data-idPelanggan');
    var hpNya = $(this).attr('data-hp');
    var modeNya = $(this).attr('data-mode');
    var refNya = $(this).attr('data-ref');
    var timeNya = $(this).attr('data-time');
    var textNya = $("span#" + urutRef).html();
    var countMember = $("span#member" + urutRef).html();
    $.ajax({
      url: '<?= $this->BASE_URL ?>Antrian/sendNotif/' + countMember + "/1",
      data: {
        hp: hpNya,
        text: textNya,
        mode: modeNya,
        ref: refNya,
        time: timeNya,
        idPelanggan: id_pelanggan
      },
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

  $("a.directWA").on('click', function(e) {
    e.preventDefault();
    var urutRef = $(this).attr('data-urutRef');
    var id_pelanggan = $(this).attr('data-idPelanggan');
    var hpNya = $(this).attr('data-hp');
    var modeNya = $(this).attr('data-mode');
    var refNya = $(this).attr('data-ref');
    var timeNya = $(this).attr('data-time');
    var textNya = $("span#" + urutRef).html();
    var countMember = $("span#member" + urutRef).html();
    $.ajax({
      url: '<?= $this->BASE_URL ?>Antrian/directWA/' + countMember,
      data: {
        hp: hpNya,
        text: textNya,
        mode: modeNya,
        ref: refNya,
        time: timeNya,
        idPelanggan: id_pelanggan
      },
      type: "POST",
      success: function(result) {
        var number = '62' + hpNya.substring(1);
        window.open("https://wa.me/" + number + "?text=" + result);
      },
    });
  });

  $("a.bayarPas").on('click', function(e) {
    e.preventDefault();
    var jumlahPas = $("input.jumlahBayar").val();
    $("input.dibayar").val(jumlahPas);
    diBayar = $("input.dibayar").val();
  });

  $("select.metodeBayar").on("keyup change", function() {
    if ($(this).val() == 2) {
      $("div#nTunai").show();
    } else {
      $("div#nTunai").hide();
    }
  });

  $('span.clearTuntas').click(function() {
    $("input#searchInput").val("");
    $(".backShow").removeClass('d-none');
    clearTuntas();
  });

  $("input.dibayar").on("keyup change", function() {
    diBayar = 0;
    diBayar = $(this).val();
    var kembalian = $(this).val() - $('input.jumlahBayar').val()
    if (kembalian > 0) {
      $('input.kembalian').val(kembalian);
    } else {
      $('input.kembalian').val(0);
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
          url: '<?= $this->BASE_URL ?>Antrian/updateRak/',
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
    $("#" + idDIV).removeClass("d-none");
    domtoimage.toPng(document.getElementById(idDIV)).then(function(dataUrl) {
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
        url: '<?= $this->BASE_URL ?>Antrian/getPoin',
        data: {
          'id': idPelanggan,
        },
        type: 'POST',
        success: function(response) {
          $('span.saldoPoin' + id).html(response);
          if (countMember > 0) {
            $.ajax({
              url: '<?= $this->BASE_URL ?>Member/textSaldo',
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
          url: '<?= $this->BASE_URL ?>Member/textSaldo',
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

  function Print(id) {
    var printContents = document.getElementById("print" + id).innerHTML;
    var originalContents = document.body.innerHTML;
    window.document.body.style = 'margin:0';
    window.document.writeln(printContents);
    window.print();

    var id_pelanggan = "<?= $data['pelanggan'] ?>";
    window.location.href = "<?= $this->BASE_URL ?>Operasi/i/1/" + id_pelanggan + "/0";
  }

  function loadDiv() {
    var modeView = "<?= $modeView ?>";
    if (modeView != 2) {
      var pelanggan = $("select[name=pelanggan").val();
      $("div#load").load("<?= $this->BASE_URL ?>Operasi/loadData/" + pelanggan + "/0");
    }
    if (modeView == 2) {
      var pelanggan = $("select[name=pelanggan").val();
      var tahun = $("select[name=tahun").val();
      $("div#load").load("<?= $this->BASE_URL ?>Operasi/loadData/" + pelanggan + "/" + tahun);
    }
  }

  function bonJPG(id, noref, idPelanggan) {
    var txtPoin = $('span#poin' + id).html();
    var countMember = $('span#member' + id).html();

    if (txtPoin.length > 0) {
      $.ajax({
        url: '<?= $this->BASE_URL ?>Antrian/getPoin',
        data: {
          'id': idPelanggan,
        },
        type: 'POST',
        success: function(response) {
          $('span.saldoPoin' + id).html(response);
          if (countMember > 0) {
            $.ajax({
              url: '<?= $this->BASE_URL ?>Member/textSaldo',
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
          url: '<?= $this->BASE_URL ?>Member/textSaldo',
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