<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="row">
            <div class="col m-2">
              Related : <span id="forbidden"></span>
            </div>
            <div class="col m-2">
              <span class="btn btn-sm btn-danger clearHapus float-right">Hapus Semua</span>
            </div>
          </div>
          <div class="card-body p-0 table-responsive-sm">
            <table class="table table-sm w-100">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Jenis</th>
                  <th>Item List</th>
                  <th>Durasi</th>
                  <th>Layanan</th>
                  <th>Qty/Diskon</th>
                  <th>Masuk/Note</th>
                  <th class="text-right">Total</th>
                </tr>
              </thead>
              <tbody id="tabelAntrian">
                <?php
                $arrRef = array();
                $prevRef = '';
                $countRef = 0;
                foreach ($data['data_main'] as $a) {
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

                foreach ($data['data_main'] as $a) {
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

                  $karyawan = '';
                  foreach ($this->user as $c) {
                    if ($c['id_user'] == $f18) {
                      $karyawan = $c['nama_user'];
                    }
                  }


                  if ($no == 1) {
                    $enHapus = true;
                    $urutRef++;
                    $buttonNotif = "<span class='badge badge-light sendNotif'>" . $modeNotifShow . " Nota</span>";

                    foreach ($data['notif'] as $notif) {
                      if ($notif['no_ref'] == $noref) {
                        $buttonNotif = "<span>" . $modeNotifShow . " Nota <i class='fas fa-check-circle text-success'></i></span>";
                      }
                    }

                    echo "<tr class='table-success' id='tr" . $id . "'>";
                    echo "</tr>";
                    echo "<tr class='table-success' id='tr" . $id . "'>";
                    echo "<td></td>";
                    echo "<td colspan='6'> 
                    <b>" . strtoupper($pelanggan) . "</b>  | " . $buttonNotif . "
                    </td><td class='text-right'><small>CS: " . $karyawan . "</small></td>";
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

                  $durasi = "";
                  foreach ($this->dDurasi as $b) {
                    if ($b['id_durasi'] == $f11) {
                      $durasi = $b['durasi'];
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
                        foreach ($data['operasi'] as $o) {
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
                  echo "<td class='text-center'>";
                  if ($countLayanan ==  $doneLayanan) {
                    $statusRak = "<br><button class='badge badge-light selesai' data-id='" . $id . "' data-hp='" . $no_pelanggan . "' data-mode='" . $modeNotif . "' data-bs-toggle='modal' data-bs-target='#exampleModal3'><i class='fas fa-check'></i></button>";
                    foreach ($data['notif_penjualan'] as $notif) {
                      if ($notif['id_penjualan'] == $id) {
                        $statusRak = "<br><span class='text-success'><b>" . strtoupper($letak) . "</b></span>";
                      }
                    }
                    echo $statusRak;
                  }
                  echo "</td>";
                  echo "<td>" . $id . " | <span><b>" . $pelanggan . "</b></span> <span class='badge badge-light'>" . $penjualan . "</span><br>" . $kategori . "</td>";
                  echo "<td>" . $itemList . "</td>";
                  echo "<td>" . $durasi . "<br><span style='white-space: pre;'>(" . $f12 . "h " . $f13 . "j)</span></td>";
                  echo "<td>" . $list_layanan . "</td>";
                  echo "<td class='text-right'>" . $show_qty . "<br>" . $show_diskon . "</td>";
                  echo "<td>" . substr($f1, 5, 11) . "<br><small>" . $f8 . "</small></td>";
                  echo "<td class='text-right'>Rp" . $show_total . "</td>";
                  echo "</tr>";

                  $showMutasi = "";
                  $userKas = "";
                  $totalBayar = 0;
                  foreach ($data['kas'] as $ka) {
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
                    $buttonHapus = "<button data-ref='" . $noref . "' class='restoreRef badge-success mb-1 rounded btn-outline-success'><i class='fas fa-recycle'></i></button> ";
                    if ($totalBayar > 0) {
                      $forbiddenCount++;
                      array_push($arrNoref, $noref);
                    }
                    echo "<tr>";
                    echo "<td class='text-center'>" . $buttonHapus . "</td>";
                    echo "<td class='text-right'><span class='d-none'>" . $pelanggan . "</span>Pembayaran:</td><td colspan='3'>" . $showMutasi . "</td>";
                    echo "<td>" . $showSisa . "</td>";


                    if ($sisaTagihan > 0) {
                      echo "<td class='text-center'></td>";
                    } else {
                      echo "<td class='text-center'><i class='fas fa-check-circle text-success'></i> <span>Lunas</span></td>";
                    }
                    echo "<td class='text-right'><b>Rp" . number_format($subTotal) . "</b></td></>";
                    echo "</tr>";

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
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>

<script>
  $(document).ready(function() {
    $("span#forbidden").html("<?= $forbiddenCount ?>");

    $("button.restoreRef").on('click', function(e) {
      e.preventDefault();
      var refNya = $(this).attr('data-ref');
      $.ajax({
        url: '<?= $this->BASE_URL ?>Antrian/restoreRef',
        data: {
          ref: refNya,
        },
        type: "POST",
        success: function(response) {
          location.reload(true);
        },
      });
    });

    $('span.clearHapus').click(function() {
      var dataID = '<?= serialize($arrID) ?>';
      var dataRef = '<?= serialize($arrNoref) ?>';
      var countForbid = <?= $forbiddenCount ?>;
      var countID = <?= count($arrID) ?>;

      if (countForbid > 0) {
        $.ajax({
          url: '<?= $this->BASE_URL ?>DataHapus/hapusRelated',
          data: {
            'transaksi': 1,
            'dataID': dataID,
            'dataRef': dataRef,
          },
          type: 'POST',
          success: function() {
            location.reload(true);
          },
        });
      }
      if (countForbid == 0 && countID > 0) {
        $.ajax({
          url: '<?= $this->BASE_URL ?>DataHapus/hapusID',
          data: {
            'table': 'penjualan',
            'kolomID': 'id_penjualan',
            'dataID': dataID,
          },
          type: 'POST',
          success: function() {
            location.reload(true);
          },
        });
      }
    });

  });
</script>