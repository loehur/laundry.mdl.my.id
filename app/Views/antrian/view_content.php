<link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/style.css" rel="stylesheet" />
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
        $pelanggan_show = substr($pelanggan, 0, 25) . "...";
      }

    ?>
      <?php

      if ($no_urut == 1) {
        $adaBayar = false;
        $cols++;
        echo "<div data-id_pelanggan='" . $f17 . "' id='grid" . $noref . "' class='col shake_hover backShow " . strtoupper($pelanggan) . " p-0 m-1 rounded' style='max-width:400px;cursor:pointer'><div class='bg-white rounded'>";
        echo "<table class='table table-sm m-0 rounded w-100 bg-white'>";
        $lunas = false;
        $totalBayar = 0;
        $subTotal = 0;
        $enHapus = true;
        $urutRef++;

        $dateToday = date("Y-m-d");
        if (strpos($f1, $dateToday) !== FALSE) {
          $classHead = 'table-primary';
        } else {
          $classHead = 'table-success';
        }

        $idLabel = $noref . "100";

        echo "<tr class=' " . $classHead . " row" . $noref . "' id='tr" . $id . "'>";
        echo "<td colspan='2'><span style='cursor:pointer' title='" . $pelanggan . "'><b>" . strtoupper($pelanggan_show) . "</b> <small>[" . $f17 . "]</small></span></td>";
        echo "<td nowrap><div><span class='text-dark'>" . substr($f1, 5, 11) . "</span></div>
          
          </td>";
        echo "</tr>";
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

      $list_layanan = "";
      $arrList_layanan = unserialize($f5);
      foreach ($arrList_layanan as $b) {
        foreach ($this->dLayanan as $c) {
          if ($c['id_layanan'] == $b) {
            $list_layanan = $list_layanan . "<span>" . $c['layanan'] . "</span><br>";
          }
        }
      }

      $total = $f7 * $qty_real;

      $subTotal = $subTotal + $total;
      $show_total = "";
      $show_total_print = "";
      $show_total_notif = "";

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
      ?>

      <tr id='tr" . $id . "' class='row" . $noref . " " . $classTRDurasi . " table-borderless'>
        <td class='pb-0' style="width: 38%;"><span style='white-space: nowrap;'><span style='white-space: nowrap;'></span><b><?= $kategori ?></b><span class='badge badge-light'></span><br><span class="<?= $classDurasi ?>" style='white-space: pre;'><?= $durasi ?> (<?= $f12 ?>h <?= $f13 ?>j)</span><br><?= $itemList ?></td>
        <td class='pb-0' style="width: 37%;"><b><?= $show_qty ?> <small>[<?= $id ?>]</small><br>Rp<?= number_format($subTotal) ?></b></td>
        <td class='pb-0' style="width: 25%;"><span class='" . $classDurasi . "' style='white-space: pre;'><?= $list_layanan ?></td>
      </tr>

    <?php

      if ($arrCount_Noref == $no_urut) {

        echo "</tbody></table>";
        echo "</div></div>";

        if ($cols == 2) {
          echo '<div class="w-100"></div>';
          $cols = 0;
        }

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

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/dom-to-image.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/FileSaver.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>

<script>
  $("div.shake_hover").click(function() {
    var id_pelanggan = $(this).attr('data-id_pelanggan');
    window.location.href = "<?= $this->BASE_URL ?>Operasi/i/1/" + id_pelanggan + "/0";
  })
</script>