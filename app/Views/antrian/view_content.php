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
      <div class='col p-0 m-2 rounded'>
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

<div id="colAntri">
  <div class="row mx-0">
    <?php
    $prevPoin = 0;
    $arrRef = [];

    $arrPoin = [];
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
    $arrGetPoin = [];
    $arrTotalPoin = [];
    $arrBayar = [];

    $enHapus = true;
    $arrTuntas = [];

    $cols = 0;
    $countMember = 0;

    $arrRekapAntrian = [];
    $arrRekapAntrianToday = [];
    $arrRekapAntrianBesok = [];
    $arrRekapAntrianMiss = [];
    $arrRekapAntrianRak = [];
    $arrRekapAntrianKerja = [];

    $arrPelangganToday = [];
    $arrPelangganBesok = [];
    $arrPelangganMiss = [];
    $arrPelangganRak = [];
    $arrPelangganKerja = [];

    $tglToday = date('Y-m-d');
    $tglBesok = date('Y-m-d', strtotime('+1 days'));

    foreach ($data['data_main'] as $a) {
      $deadlineSetrikaToday = false;
      $deadlineSetrikaBesok = false;
      $deadlineSetrikaMiss = false;

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

      $deadline = date('Y-m-d', strtotime($f1 . ' + ' . $f12 . ' days'));
      $deadline = date('Y-m-d H:i:s', strtotime($deadline . ' + ' . $f13 . ' hours'));

      if (date('Y-m-d', strtotime($deadline)) == date('Y-m-d', strtotime($tglToday))) {
        $deadlineSetrikaToday = true;
      }

      if (date('Y-m-d', strtotime($deadline)) == date('Y-m-d', strtotime($tglBesok))) {
        $deadlineSetrikaBesok = true;
      }

      if (date('Y-m-d', strtotime($deadline)) < date('Y-m-d', strtotime($tglToday))) {
        $deadlineSetrikaMiss = true;
      }

      if ($f12 <> 0) {
        $tgl_selesai = date('d-m-Y', strtotime($f1 . ' +' . $f12 . ' days +' . $f13 . ' hours'));
      } else {
        $tgl_selesai = date('d-m-Y H:i', strtotime($f1 . ' +' . $f12 . ' days +' . $f13 . ' hours'));
      }

      $pelanggan = '';
      $no_pelanggan = '';
      $modeNotif = 1;

      foreach ($this->pelanggan as $c) {
        if ($c['id_pelanggan'] == $f17) {
          $pelanggan = $c['nama_pelanggan'];
          $no_pelanggan = $c['nomor_pelanggan'];
        }
      }

      $karyawan = '';
      foreach ($data['karyawan'] as $c) {
        if ($c['id_user'] == $f18) {
          $karyawan = $c['nama_user'];
          $karyawan_id = $c['id_user'];
        }
        if ($c['id_user'] == $id_ambil) {
          $karyawan_ambil = $c['nama_user'];
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

      $ambil_cek = ($id_ambil > 0) ? "<i class='fas fa-check-circle text-success'></i> <span class='fw-bold'>" . $karyawan_ambil . "</span> Ambil" : "<i class='far fa-circle'></i> Ambil";

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

      $pelanggan_show = $pelanggan;
      if (strlen($pelanggan) > 20) {
        $pelanggan_show = substr($pelanggan, 0, 20) . "...";
      }

      $list_layanan = "";
    ?>
      <?php

      if ($no_urut == 1) {
        $adaBayar = false;
        $cols++;
        echo "<div data-id_pelanggan='" . $f17 . "' id='grid" . $noref . "' class='" . $id . " R-" . $noref . " col shake_hover backShow " . strtoupper($pelanggan) . " p-0 px-1 mb-2 rounded' style='cursor:pointer'><div class='bg-white rounded p-0'>";
        echo "<table class='table table-sm m-0 rounded w-100 shadow-sm bg-white'>";
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
        $buttonNotif = '<b><i class="fab fa-whatsapp"></i></b>';
        $stNotif = "...";

        foreach ($data['data_notif'] as $notif) {
          if ($notif['no_ref'] == $noref) {
            $stNotif = "<b>" . ucwords(strtolower($notif['proses'])) . "</b> " . ucwords($notif['state']);
          }
        }
        $buttonNotif = "<span>" . $buttonNotif .  " </span>" . $stNotif;
        $tgl_terima = date('d/m/y H:i', strtotime($f1));
        echo "<tr class=' " . $classHead . " row" . $noref . "' id='tr" . $id . "'>";
        echo "<td nowrap><span style='cursor:pointer' title='" . $pelanggan . "'><b>" . strtoupper($pelanggan_show) . "</b> <small>" . $f17 . "</small></span></td>";
        echo "<td nowrap><small>" . $buttonNotif . "</small></td>";
        echo "<td nowrap class='text-right'><small><span class='text-dark'><span class='fw-bold'>" . $karyawan . "</span> " . $tgl_terima . "</span></small>
          
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

      $userOperasi = "";
      $arrList_layanan = unserialize($f5);
      $endLayanan = end($arrList_layanan);
      $countLayanan = count($arrList_layanan);

      foreach ($arrList_layanan as $b) {
        foreach ($this->dLayanan as $c) {
          if ($c['id_layanan'] == $b) {
            $check = 0;
            foreach ($data['operasi'] as $o) {
              if ($o['id_penjualan'] == $id && $o['jenis_operasi'] == $b) {
                $check++;
                foreach ($data['karyawan'] as $p) {
                  if ($p['id_user'] == $o['id_user_operasi']) {
                    $userOperasi = $p['nama_user'];
                  }
                }
              }
            }

            if ($check == 0) {
              $list_layanan = $list_layanan . "<i class='far fa-circle'></i> <span>" . $c['layanan'] . "</span><br>";
              $layananNow = $c['layanan'];

              if ($b == $endLayanan) {
                if (isset($arrRekapAntrian[$layananNow])) {
                  $arrRekapAntrian[$layananNow] += $f6;
                } else {
                  $arrRekapAntrian[$layananNow] = $f6;
                }

                if ($deadlineSetrikaToday == true) {
                  if (isset($arrRekapAntrianToday[$layananNow])) {
                    $arrRekapAntrianToday[$layananNow] += $f6;
                  } else {
                    $arrRekapAntrianToday[$layananNow] = $f6;
                  }
                  array_push($arrPelangganToday, $noref);

                  if (isset($arrRekapAntrianKerja[$layananNow])) {
                    $arrRekapAntrianKerja[$layananNow] += $f6;
                  } else {
                    $arrRekapAntrianKerja[$layananNow] = $f6;
                  }
                  array_push($arrPelangganKerja, $noref);
                } else {
                  if ($countLayanan == 1) {
                    if (isset($arrRekapAntrianKerja[$layananNow])) {
                      $arrRekapAntrianKerja[$layananNow] += $f6;
                    } else {
                      $arrRekapAntrianKerja[$layananNow] = $f6;
                    }
                    array_push($arrPelangganKerja, $noref);
                  }
                }

                if ($deadlineSetrikaBesok == true) {
                  if (isset($arrRekapAntrianBesok[$layananNow])) {
                    $arrRekapAntrianBesok[$layananNow] += $f6;
                  } else {
                    $arrRekapAntrianBesok[$layananNow] = $f6;
                  }
                  array_push($arrPelangganBesok, $noref);
                }
                if ($deadlineSetrikaMiss == true) {
                  if (isset($arrRekapAntrianMiss[$layananNow])) {
                    $arrRekapAntrianMiss[$layananNow] += $f6;
                  } else {
                    $arrRekapAntrianMiss[$layananNow] = $f6;
                  }
                  array_push($arrPelangganMiss, $noref);
                }
              }
            } else {
              $layananNow = $c['layanan'];
              if ($b == $endLayanan && strlen($letak) == 0) {
                if (isset($arrRekapAntrianRak[$layananNow])) {
                  $arrRekapAntrianRak[$layananNow] += $f6;
                } else {
                  $arrRekapAntrianRak[$layananNow] = $f6;
                }
                array_push($arrPelangganRak, $noref);
              }
              $list_layanan = $list_layanan . "<b><i class='fas fa-check-circle text-success'></i> " . ucfirst($userOperasi) . " </b>" . $c['layanan'] . " <span style='white-space: pre;'></span><br>";
            }
          }
        }
      }

      $total = $f7 * $qty_real;

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
      if ($member == 0) {
        if (strlen($show_diskon) > 0) {
          $tampilDiskon = "(Disc. " . $show_diskon . ")";
          $show_total = "<del>Rp" . number_format($f7 * $qty_real) . "</del><br>Rp" . number_format($total);
        } else {
          $tampilDiskon = "";
          $show_total = "Rp" . number_format($total);
        }
      } else {
        $show_total = "<span class='badge badge-success'>Member</span>";
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
      ?>

      <tr id='tr" . $id . "' class='border-top'>
        <td nowrap class='pb-0' style="width: 45%;">
          <b><?= $kategori ?></b><br><span class="<?= $classDurasi ?>" style='white-space: pre;'><?= $durasi ?></span> <?= $f12 ?>h <?= $f13 ?>j<br>
          <?php if ($letak <> "") { ?>
            <b class="text-success border-end me-1">
              <?= strtoupper($letak) ?>
            </b>
          <?php } ?>
          <small class="pe-1"><?= $id ?></small> <b><?= $show_qty ?></b>
          <br><?= $itemList ?>
        </td>
        <td class='pb-1' style="width: 23%;"><span style='white-space: pre;'><?= $list_layanan ?><?= $ambil_cek ?></td>
        <td class='pb-0 text-right' style="width: 32%;"><?= $show_total ?></td>
      </tr>

    <?php

      $showMutasi = "";
      foreach ($data['kas'] as $ka) {
        if ($ka['ref_transaksi'] == $noref) {
          $stBayar = "";

          foreach ($this->dStatusMutasi as $st) {
            if ($ka['status_mutasi'] == $st['id_status_mutasi']) {
              $stBayar = $st['status_mutasi'];
            }
          }

          foreach ($data['karyawan'] as $c) {
            if ($c['id_user'] == $ka['id_user']) {
              $karyawan_kas = $c['nama_user'];
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
              $statusM = "<span class='text-danger'><i class='fas fa-times-circle'></i> " . $stBayar . " <b>(" . $notenya . ")</b></span> - ";
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

          $showMutasi = $showMutasi . "<small>" . $statusM . "#" . $ka['id_kas'] . "</small> <b>" . ucwords($karyawan_kas) . "</b> " . date('d/m H:i', strtotime($ka['insertTime'])) . " " . $nominal . "<br>";
        }
      }

      if ($arrCount_Noref == $no_urut) {

        //SURCAS
        foreach ($data['surcas'] as $sca) {
          if ($sca['no_ref'] == $noref) {
            foreach ($this->surcas as $sc) {
              if ($sc['id_surcas_jenis'] == $sca['id_jenis_surcas']) {
                $surcasNya = $sc['surcas_jenis'];
              }
            }

            $id_surcas = $sca['id_surcas'];
            $jumlahCas = $sca['jumlah'];
            echo "<tr><td>Surcharge</td><td>" . $surcasNya . "</td><td align='right'>Rp" . number_format($jumlahCas) . "</td></tr>";
            $subTotal += $jumlahCas;
          }
        }

        $sisaTagihan = intval($subTotal) - $totalBayar;
        $textPoin = "";
        if (isset($arrTotalPoin[$noref]) && $arrTotalPoin[$noref] > 0) {
          $textPoin = "(Poin " . $arrTotalPoin[$noref] . ") ";
        }
        echo "<span class='d-none' id='poin" . $urutRef . "'>" . $textPoin . "</span>";
        echo "<span class='d-none' id='member" . $urutRef . "'>" . $countMember . "</span>";

        if ($sisaTagihan < 1) {
          $lunas = true;
        }

        echo "<tr class='row" . $noref . "'>";
        echo "<td class='text-center'><span class='d-none'>" . $pelanggan . "</span></td>";

        if ($lunas == false) {
          echo "<td></td>";
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

        $totalBayar = 0;
        $sisaTagihan = 0;
        $no_urut = 0;
        $subTotal = 0;
      }
    }


    $listAntri = "";

    if (count($arrRekapAntrianToday) > 0) {
      $listAntri .= "<b>Hari ini: </b>";
      foreach ($arrRekapAntrianToday as $key => $val) {
        $listAntri .= "<span class='text-danger' onclick='filterDeadline(1)' style='cursor:pointer'>" . $key . " " . $val . ", </span>";
      }
    }
    if (count($arrRekapAntrianRak) > 0) {
      $listAntri .= "<b>Rak: </b>";
      foreach ($arrRekapAntrianRak as $key => $val) {
        $listAntri .= "<span class='text-danger' onclick='filterDeadline(4)' style='cursor:pointer'>" . $key . " " . $val . ", </span>";
      }
    }
    if (count($arrRekapAntrianMiss) > 0) {
      $listAntri .= "<b>Terlewat: </b>";
      foreach ($arrRekapAntrianMiss as $key => $val) {
        $listAntri .= "<span class='text-danger' onclick='filterDeadline(3)' style='cursor:pointer'>" . $key . " " . $val . ", </span>";
      }
    }

    if (count($arrPelangganBesok) > 0) {
      $listAntri .= "<b>Besok: </b>";
      foreach ($arrRekapAntrianBesok as $key => $val) {
        $listAntri .= "<span class='text-primary' onclick='filterDeadline(2)' style='cursor:pointer'>" . $key . " " . $val . ", </span>";
      }
    }
    if (count($arrPelangganKerja) > 0) {
      $listAntri .= "<b>Kerja: </b>";
      foreach ($arrRekapAntrianKerja as $key => $val) {
        $listAntri .= "<span class='text-info' onclick='filterDeadline(5)' style='cursor:pointer'>" . $key . " " . $val . ", </span>";
      }
    }
    if (count($arrRekapAntrian) > 0) {
      $listAntri .= "<b>Antrian: </b>";
      foreach ($arrRekapAntrian as $key => $val) {
        $listAntri .= "<span class='text-success'>" . $key . " " . $val . ", </span>";
      }
    }
    ?>
  </div>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>

<script>
  var view = [];

  $(document).ready(function() {
    $("span#rekapAntri").html("<?= $listAntri ?>");
    view[1] = <?= json_encode($arrPelangganToday) ?>;
    view[2] = <?= json_encode($arrPelangganBesok) ?>;
    view[3] = <?= json_encode($arrPelangganMiss) ?>;
    view[4] = <?= json_encode($arrPelangganRak) ?>;
    view[5] = <?= json_encode($arrPelangganKerja) ?>;
  });

  $("div.shake_hover").click(function() {
    var id_pelanggan = $(this).attr('data-id_pelanggan');
    window.location.href = "<?= URL::BASE_URL ?>Operasi/i/0/" + id_pelanggan + "/0";
  })

  function filterDeadline(mode) {
    $("div.backShow").addClass('d-none');
    view[mode].forEach(filterFunction);
  }

  function filterFunction(item) {
    if (item.length > 0) {
      $("[class*=R-" + item + "]").removeClass('d-none');
    }
  }
</script>