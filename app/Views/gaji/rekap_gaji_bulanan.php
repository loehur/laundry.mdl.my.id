<?php
if (count($data['dataTanggal']) > 0) {
  $currentMonth =   $data['dataTanggal']['bulan'];
  $currentYear =   $data['dataTanggal']['tahun'];
} else {
  $currentMonth = date('m');
  $currentYear = date('Y');
}

$dateOn =  $currentYear . "-" . $currentMonth;

$aDate = strtotime($dateOn);
$bDate = strtotime(date("Y-m"));
$intervalDate = ($bDate - $aDate) / 60 / 60 / 24;


$r = array();
$r_gl = $data['gajiLaundry'];

$user = 0;
foreach ($data['data_main'] as $a) {
  $user = $a['id_user_operasi'];
  $cabang = $a['id_cabang'];
  $jenis_operasi = $a['jenis_operasi'];
  $jenis = $a['id_penjualan_jenis'];

  if (isset($r[$user][$jenis][$jenis_operasi][$cabang]) ==  TRUE) {
    $r[$user][$jenis][$jenis_operasi][$cabang] =  $r[$user][$jenis][$jenis_operasi][$cabang] + $a['qty'];
  } else {
    $r[$user][$jenis][$jenis_operasi][$cabang] = $a['qty'];
  }
}

foreach ($data['dTerima'] as $a) {
  $user = $a['id_user'];
  $cabang = $a['id_cabang'];
  $jenis_operasi = 9000;
  $jenis = "9000";

  if (isset($r[$user][$jenis][$jenis_operasi][$cabang]) ==  TRUE) {
    $r[$user][$jenis][$jenis_operasi][$cabang] =  $r[$user][$jenis][$jenis_operasi][$cabang] + $a['terima'];
  } else {
    $r[$user][$jenis][$jenis_operasi][$cabang] = $a['terima'];
  }
}

foreach ($data['dKembali'] as $a) {
  $user = $a['id_user'];
  $cabang = $a['id_cabang'];
  $jenis_operasi = 9001;
  $jenis = "9001";

  if (isset($r[$user][$jenis][$jenis_operasi][$cabang]) ==  TRUE) {
    $r[$user][$jenis][$jenis_operasi][$cabang] =  $r[$user][$jenis][$jenis_operasi][$cabang] + $a['kembali'];
  } else {
    $r[$user][$jenis][$jenis_operasi][$cabang] = $a['kembali'];
  }
}

$id_user = $data['user']['id'];
$nama_user = "";
foreach ($this->user as $uc) {
  if ($uc['id_user'] == $data['user']['id']) {
    $nama_user = "<small>[" . $uc['id_user'] . "]</small> - <b>" . $uc['nama_user'] . "</b>";
  }
}

$r_pengali = array();
$r_pengali_id = array();
foreach ($data['gaji']['gaji_pengali'] as $a) {
  $r_pengali[$a['id_karyawan']][$a['id_pengali']] = $a['gaji_pengali'];
  $r_pengali_id[$a['id_karyawan']][$a['id_pengali']] = $a['id_gaji_pengali'];
}

$pengali_list = $data['gaji']['pengali_list'];

$totalDapat = 0;
$totalPotong = 0;
$totalTerima = 0;

$arrInject = array();
$noInject = 0;

?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="content sticky-top pl-1 pr-2">
            <form action="<?= $this->BASE_URL; ?>Gaji" method="POST">
              <table class="w-100">
                <tr>
                  <td>
                    <select name="user" class="form-control form-control-sm karyawan" style="width: 100%;" required>
                      <option value="" selected disabled>Karyawan</option>
                      <?php if (count($this->user) > 0) {
                        foreach ($this->user as $a) { ?>
                          <option <?php if ($data['user']['id'] == $a['id_user']) {
                                    echo "selected";
                                  } ?> id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                      <?php }
                      } ?>
                    </select>
                  </td>
                  <td>
                    <select name="m" class="form-control form-control-sm" style="width: auto;">
                      <option class="text-right" value="01" <?php if ($currentMonth == '01') {
                                                              echo 'selected';
                                                            } ?>>01</option>
                      <option class="text-right" value="02" <?php if ($currentMonth == '02') {
                                                              echo 'selected';
                                                            } ?>>02</option>
                      <option class="text-right" value="03" <?php if ($currentMonth == '03') {
                                                              echo 'selected';
                                                            } ?>>03</option>
                      <option class="text-right" value="04" <?php if ($currentMonth == '04') {
                                                              echo 'selected';
                                                            } ?>>04</option>
                      <option class="text-right" value="05" <?php if ($currentMonth == '05') {
                                                              echo 'selected';
                                                            } ?>>05</option>
                      <option class="text-right" value="06" <?php if ($currentMonth == '06') {
                                                              echo 'selected';
                                                            } ?>>06</option>
                      <option class="text-right" value="07" <?php if ($currentMonth == '07') {
                                                              echo 'selected';
                                                            } ?>>07</option>
                      <option class="text-right" value="08" <?php if ($currentMonth == '08') {
                                                              echo 'selected';
                                                            } ?>>08</option>
                      <option class="text-right" value="09" <?php if ($currentMonth == '09') {
                                                              echo 'selected';
                                                            } ?>>09</option>
                      <option class="text-right" value="10" <?php if ($currentMonth == '10') {
                                                              echo 'selected';
                                                            } ?>>10</option>
                      <option class="text-right" value="11" <?php if ($currentMonth == '11') {
                                                              echo 'selected';
                                                            } ?>>11</option>
                      <option class="text-right" value="12" <?php if ($currentMonth == '12') {
                                                              echo 'selected';
                                                            } ?>>12</option>
                    </select>
                  </td>
                  <td>
                    <select name="Y" class="form-control form-control-sm" style="width: auto;">
                      <option class="text-right" value="2021" <?php if ($currentYear == 2021) {
                                                                echo 'selected';
                                                              } ?>>2021</option>
                      <option class="text-right" value="2022" <?php if ($currentYear == 2022) {
                                                                echo 'selected';
                                                              } ?>>2022</option>
                    </select>
                  </td>
                  <td><button class="form-control btn-success form-control-sm m-1 p-1 bg-light">Cek</td>
                  <?php if ($nama_user <> "") { ?>
                    <td>
                      <div class="btn-group ml-2">
                        <button type="button" class="btn btn-sm btn-dark dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                          Set Gaji
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                          <a class="dropdown-item" href="#exampleModal" data-bs-toggle="modal">FEE Layanan Laundry</a>
                          <a class="dropdown-item" href="#exampleModal1" data-bs-toggle="modal">FEE Pengali</a>
                          <a class="dropdown-item" href="#exampleModal2" data-bs-toggle="modal">QTY Pengali</a>
                        </div>
                      </div>
                    </td>
          </div>
        <?php } ?>
        </tr>
        </table>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<div class="row ml-1">
  <?php if ($nama_user <> "" && $intervalDate < 60) { ?>
    <div class="col p-1">
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <?php
            echo '<div class="col-auto">';
            echo '<div class="card">';

            echo '<table class="table table-sm m-0 w-100" style="min-width: 300px;">';
            echo '<tbody>';

            echo "<tr>";
            echo "<td colspan='3' class='pb-3'><span>" . strtoupper($nama_user) . " [ " . $this->dCabang['kode_cabang'] . " ]</span></td>";
            echo "<td class='text-right'><a href='#' id='tetapkan' class='btn badge badge-primary'>Tetapkan</a></td>";
            echo "</tr>";

            foreach ($r as $userID => $arrJenisJual) {
              $totalGajiLaundry = 0;
              $feeLaundry = 0;
              foreach ($this->user as $uc) {
                if ($uc['id_user'] == $userID) {
                  $user = "<small>[" . $uc['id_user'] . "]</small> - <b>" . $uc['nama_user'] . "<b>";
                  foreach ($arrJenisJual as $jenisJualID => $arrLayanan) {
                    $id_penjualan = 0;
                    $penjualan = "";
                    $satuan = "";
                    foreach ($this->dPenjualan as $jp) {
                      if ($jp['id_penjualan_jenis'] == $jenisJualID) {
                        $id_penjualan = $jp['id_penjualan_jenis'];
                        $penjualan = $jp['penjualan_jenis'];
                        foreach ($this->dSatuan as $js) {
                          if ($js['id_satuan'] == $jp['id_satuan']) {
                            $satuan = $js['nama_satuan'];
                          }
                        }
                      }
                    }

                    $id_layanan = 0;
                    foreach ($arrLayanan as $layananID => $arrCabang) {
                      $layanan = "Non";
                      $totalPerUser = 0;
                      foreach ($this->dLayanan as $dl) {
                        if ($dl['id_layanan'] == $layananID) {
                          $layanan = $dl['layanan'];
                          $id_layanan = $dl['id_layanan'];
                          foreach ($arrCabang as $cabangID => $c) {
                            $totalPerUser = $totalPerUser + $c;
                          }
                        }
                      }

                      if ($layanan == "Non") {
                        continue;
                      }

                      $gaji_laundry = 0;
                      $bonus_target = 0;
                      $target = 0;
                      $id_gl = 0;
                      $max_target = 0;
                      $max_target_fill = 0;
                      foreach ($data['gaji']['gaji_laundry'] as $gp) {
                        if ($gp['id_karyawan'] == $id_user && $gp['id_layanan'] == $id_layanan && $gp['jenis_penjualan'] == $id_penjualan) {
                          $gaji_laundry = $gp['gaji_laundry'];
                          $target = $gp['target'];
                          $bonus_target = $gp['bonus_target'];
                          $id_gl = $gp['id_gaji_laundry'];
                          $max_target = $gp['max_target'];
                          $max_target_fill = $max_target;
                        }
                      }

                      $bonus = 0;
                      $xBonus = 0;
                      if ($max_target <> 0) {
                        if ($totalPerUser <= $max_target) {
                          $max_target = $totalPerUser;
                        }
                      } else {
                        $max_target = $totalPerUser;
                      }

                      if ($target > 0) {
                        if ($totalPerUser > 0) {
                          $xBonus = floor($max_target / $target);
                          $bonus = $xBonus * $bonus_target;
                        }
                      }

                      $totalGajiLaundry = $gaji_laundry * $totalPerUser;

                      echo "<tr>";
                      echo "<td nowrap><small>" . $penjualan . "</small><br>" . $layanan . "<br><small>Target</small><br>
                  
                      <span class='edit' data-table='gaji_laundry' data-col='target' data-id_edit='" . $id_gl . "'>" . $target . "</span></td>";
                      echo "<td class='text-right'><small>Qty</small><br>" . number_format($totalPerUser) . "
                      
                      <br><small>Max Target</small><br>
                      <span class='edit' data-table='gaji_laundry' data-col='max_target' data-id_edit='" . $id_gl . "'>" . $max_target_fill . "</span>                  
                  
                  </td>";
                      echo "<td class='text-right'><small>Fee</small><br>Rp
                  
                  <span class='edit' data-table='gaji_laundry' data-col='gaji_laundry' data-id_edit='" . $id_gl . "'>" . $gaji_laundry . "</span>
                  
                  <br><small>Bonus/Target</small><br>
                      <span class='edit' data-table='gaji_laundry' data-col='bonus_target' data-id_edit='" . $id_gl . "'>" . $bonus_target . "</span>
                  
                  </td>";

                      echo "<td class='text-right'><small>Total</small><br>Rp" . number_format($totalGajiLaundry) . "<br><small>Bonus</small><br>Rp" . number_format($bonus) . "</td>";
                      echo "</tr>";

                      if ($totalGajiLaundry > 0) {

                        $totalDapat += $totalGajiLaundry;
                        $totalDapat += $bonus;

                        $noInject += 1;
                        $ref = "P" . $id_penjualan . "L" . $id_layanan;
                        $arrInject[$noInject] = array(
                          "tipe" => 1,
                          "ref" => $ref,
                          "deskripsi" => $penjualan . " " . $layanan,
                          "qty" => $totalPerUser,
                          "jumlah" => $totalGajiLaundry
                        );
                      }
                      if ($bonus > 0) {
                        $noInject += 1;
                        $ref = "P" . $id_penjualan . "L" . $id_layanan . "-B";
                        $arrInject[$noInject] = array(
                          "tipe" => 1,
                          "ref" => $ref,
                          "deskripsi" => "Bonus " . $ref,
                          "qty" => $xBonus,
                          "jumlah" => $bonus
                        );
                      }
                    }
                  }
                  $totalTerima = 0;
                  foreach ($data['dTerima'] as $a) {
                    if ($uc['id_user'] == $a['id_user']) {
                      $totalTerima = $totalTerima + $a['terima'];
                    }
                  }

                  if (isset($r_pengali[$id_user][1])) {
                    $feeTerima = $r_pengali[$id_user][1];
                    $id_gp = $r_pengali_id[$id_user][1];
                  } else {
                    $feeTerima = 0;
                    $id_gp = 0;
                  }

                  $totalFeeTerima = $totalTerima * $feeTerima;

                  echo "<tr>";
                  echo "<td nowrap><small>Laundry</small><br>Terima</td>";
                  echo "<td class='text-right'><small>Qty</small><br>" . $totalTerima . "</td>";
                  echo "<td class='text-right'><small>Fee</small><br>Rp
              
              <span class='edit' data-table='gaji_pengali' data-col='gaji_pengali' data-id_edit='" . $id_gp . "'>" . $feeTerima . "</span>

              </td>";
                  echo "<td class='text-right'><small>Total</small><br>Rp" . number_format($totalFeeTerima) . "</td>";
                  echo "</tr>";

                  if ($totalFeeTerima > 0) {
                    $totalDapat += $totalFeeTerima;

                    $noInject += 1;
                    $ref = "AL1";
                    $arrInject[$noInject] = array(
                      "tipe" => 1,
                      "ref" => $ref,
                      "deskripsi" => "Laundry Terima",
                      "qty" => $totalTerima,
                      "jumlah" => $totalFeeTerima
                    );
                  }

                  $totalKembali = 0;
                  foreach ($data['dKembali'] as $a) {
                    if ($uc['id_user'] == $a['id_user_ambil']) {
                      $totalKembali = $totalKembali + $a['kembali'];
                    }
                  }

                  if (isset($r_pengali[$id_user][2])) {
                    $feeKembali = $r_pengali[$id_user][2];
                    $id_gp = $r_pengali_id[$id_user][2];
                  } else {
                    $feeKembali = 0;
                    $id_gp = 0;
                  }

                  $totalFeeKembali = $totalKembali * $feeKembali;
                  echo "<tr>";
                  echo "<td nowrap class=''><small>Laundry</small><br>Kembali</td>";
                  echo "<td class='text-right'><small>Qty</small><br>" . $totalKembali . "</td>";
                  echo "<td class='text-right'><small>Fee</small><br>Rp
              
              <span class='edit' data-table='gaji_pengali' data-col='gaji_pengali' data-id_edit='" . $id_gp . "'>" . $feeKembali . "</span>

              </td>";
                  echo "<td class='text-right'><small>Total</small><br>Rp" . number_format($totalFeeKembali) . "</td>";
                  echo "</tr>";

                  if ($totalFeeKembali > 0) {
                    $totalDapat += $totalFeeKembali;
                    $noInject += 1;
                    $ref = "AL2";
                    $arrInject[$noInject] = array(
                      "tipe" => 1,
                      "ref" => $ref,
                      "deskripsi" => "Laundry Kembali",
                      "qty" => $totalKembali,
                      "jumlah" => $totalFeeKembali
                    );
                  }
                }
              }
            }
            $dataPengali = $data['gaji']['gaji_pengali_data'];
            if (count($dataPengali) > 0) {
              $feePTotal = 0;
              foreach ($dataPengali as $b) {
                if ($b['id_karyawan'] == $id_user) {

                  $idPengali = $b['id_pengali'];
                  $idPengaliData = $b['id_pengali_data'];

                  if (isset($r_pengali[$id_user][$idPengali])) {
                    $feeP = $r_pengali[$id_user][$idPengali];
                    $id_gp = $r_pengali_id[$id_user][$idPengali];
                  } else {
                    $feeP = 0;
                    $id_gp = 0;
                  }

                  $pengaliJenis = "";
                  foreach ($pengali_list as $pl) {
                    if ($pl['id_pengali'] == $idPengali) {
                      $pengaliJenis = $pl['pengali_jenis'];
                    }
                  }

                  $qty = $b['qty'];
                  $feePTotal = $qty * $feeP;

                  echo "<tr>";
                  echo "<td nowrap class=''><small>Laundry</small><br>" . $pengaliJenis . "</td>";
                  echo "<td class='text-right'><small>Qty</small><br>
                  
                  <span class='edit' data-table='gaji_pengali_data' data-col='qty' data-id_edit='" . $idPengaliData . "'>" . $qty . "</span>

                  </td>";
                  echo "<td class='text-right'><small>Fee</small><br>Rp
              
              <span class='edit' data-table='gaji_pengali' data-col='gaji_pengali' data-id_edit='" . $id_gp . "'>" . $feeP . "</span>
    
              </td>";
                  echo "<td class='text-right'><small>Total</small><br>Rp" . number_format($feePTotal) . "</td>";
                  echo "</tr>";

                  if ($feePTotal > 0) {
                    $totalDapat += $feePTotal;
                    $noInject += 1;
                    $ref = "HT" . $idPengali;
                    $arrInject[$noInject] = array(
                      "tipe" => 1,
                      "ref" => $ref,
                      "deskripsi" => $pengaliJenis,
                      "qty" => $qty,
                      "jumlah" => $feePTotal
                    );
                  }
                }
              }
            }

            //POTONGAN
            if (count($data['user']['kasbon']) > 0) {
              echo "<tr class='table-danger'>";
              echo "<td colspan='4' class='pt-2'>Potongan</td>";
              echo "</tr>";
              foreach ($data['user']['kasbon'] as $uk) {
                $potKasbon = $uk['jumlah'];
                $id_kas = $uk['id_kas'];
                $tgl = substr($uk['insertTime'], 0, 10);
                echo "<tr>";
                echo "<td colspan='3'>Kasbon [" . $tgl . "]</td>";
                echo "<td class='text-right'>Rp" . number_format($potKasbon) . "</td>";
                echo "</tr>";

                $totalPotong += $potKasbon;
                if ($potKasbon > 0) {
                  $noInject += 1;
                  $ref = $id_kas;
                  $arrInject[$noInject] = array(
                    "tipe" => 2,
                    "ref" => $ref,
                    "deskripsi" => "KB [" . $tgl . "]",
                    "qty" => 1,
                    "jumlah" => $potKasbon
                  );
                }
              }
            }

            $totalTerima = $totalDapat - $totalPotong;

            echo '</tbody>';
            echo '</table>';
            echo '</div></div>';
            ?>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <?php
  $tr_gaji = "";
  $totalGaji = 0;
  $totalPot = 0;
  $totalTer = 0;
  foreach ($data['gaji']['fix'] as $gf) {
    $jGaji = $gf['jumlah'];
    if ($gf['tipe'] == 1) {
      $totalGaji += $jGaji;
      $vGaji = "Rp" . number_format($gf['jumlah']);
    } else {
      $totalPot += $jGaji;
      $vGaji = "-Rp" . number_format($gf['jumlah']);
    }

    $tr_gaji = $tr_gaji . "<tr><td colspan=''>[ " . $gf['ref'] . " ]<br>" . $gf['deskripsi'] . "</td><td align='right'>[ " . $gf['qty'] . " ]<br>" . $vGaji . "</td></tr>";
  }
  $totalTer = $totalGaji - $totalPot;
  ?>

  <?php if ($nama_user <> "") { ?>
    <div class="col p-1 bg-white mr-4 mt-1">
      <span id="print" style="width:50mm;background-color:white; padding-bottom:10px">
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
              <b> <?= $this->dLaundry['nama_laundry'] ?> [ <?= $this->dCabang['kode_cabang'] ?> ]</b><br>-- SALARY SLIP --
            </td>
          </tr>
          <tr>
            <td colspan="2" style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
              <font size='2'><b><?= strtoupper($nama_user) ?></font>
              </b><br>Tahun/Bulan: <?= $dateOn ?>
            </td>

            <?= $tr_gaji ?>

          <tr>
            <td colspan="2" style="border-bottom:1px dashed black;"></td>
          </tr>
          <tr>
            <td>
              <b>Total Gaji</b>
            </td>
            <td style="text-align: right;">
              <b><?= "Rp" . number_format($totalGaji) ?></b>
            </td>
          </tr>
          <tr>
            <td>
              Total Potongan
            </td>
            <td style="text-align: right;">
              -Rp<?= number_format($totalPot) ?>
            </td>
          </tr>
          <tr>
            <td>

              Gaji Diterima
            </td>
            <td style="text-align: right;">
              Rp<?= number_format($totalTer) ?>
            </td>
          </tr>
          <tr>
            <td colspan="2" style="border-bottom:1px dashed black;"></td>
          </tr>
          <tr>
            <td colspan="2">.<br>.</td>
          </tr>
        </table>
      </span>
      <button onclick="Print()">Print</button>
    </div>
  <?php } ?>
</div>

<div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">FEE Layanan Laundry</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form class="jq" action="<?= $this->BASE_URL; ?>Gaji/set_gaji_laundry" method="POST">
          <div class="card-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Jenis Penjualan</label>
              <select name="penjualan" class="form-control form-control-sm userChange" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <?php foreach ($this->dPenjualan as $a) { ?>
                  <option id="<?= $a['id_penjualan_jenis'] ?>" value="<?= $a['id_penjualan_jenis'] ?>"><?= $a['penjualan_jenis'] ?></option>
                <?php } ?>
                </optgroup>
              </select>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Jenis Layanan</label>
              <select name="layanan" class="form-control form-control-sm userChange" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <?php foreach ($this->dLayanan as $a) { ?>
                  <option id="<?= $a['id_layanan'] ?>" value="<?= $a['id_layanan'] ?>"><?= $a['layanan'] ?></option>
                <?php } ?>
                </optgroup>
              </select>
            </div>
            <input name='id_user' type="hidden" value="<?= $data['user']['id'] ?>" />
            <div class="form-group">
              <label for="exampleInputEmail1">Fee (Rp)</label>
              <input type="number" name="fee" min="1" class="form-control" id="exampleInputEmail1" placeholder="" required>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Target <small>Berlaku Kelipatan</small></label>
              <input type="number" name="target" min="0" class="form-control" value="0" id="exampleInputEmail1" placeholder="" required>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Max Target <small>(0 Jika Tanpa Max Target)</small></label>
              <input type="number" name="max_target" min="0" class="form-control" value="0" id="exampleInputEmail1" placeholder="" required>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Bonus Target</label>
              <input type="number" name="bonus_target" min="0" class="form-control" value="0" id="exampleInputEmail1" placeholder="" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">FEE Pengali</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form class="jq" action="<?= $this->BASE_URL; ?>Gaji/set_gaji_pengali" method="POST">
          <div class="card-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Jenis Pengali</label>
              <select name="pengali" class="form-control form-control-sm userChange" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <?php foreach ($pengali_list as $a) { ?>
                  <option value="<?= $a['id_pengali'] ?>"><?= $a['pengali_jenis'] ?></option>
                <?php } ?>
              </select>
            </div>
            <input name='id_user' type="hidden" value="<?= $data['user']['id'] ?>" />
            <div class="form-group">
              <label for="exampleInputEmail1">Fee (Rp)</label>
              <input type="number" name="fee" min="1" class="form-control" id="exampleInputEmail1" placeholder="" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">QTY Pengali</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form class="jq" action="<?= $this->BASE_URL; ?>Gaji/set_harian_tunjangan" method="POST">
          <div class="card-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Jenis Pengali</label>
              <select name="pengali" class="form-control form-control-sm userChange" style="width: 100%;" required>
                <option value="" selected disabled></option>
                <?php foreach ($pengali_list as $a) {
                  if ($a['id_pengali'] > 2) { ?>
                    <option value="<?= $a['id_pengali'] ?>"><?= $a['pengali_jenis'] ?></option>
                <?php }
                } ?>
              </select>
            </div>
            <input name='id_user' type="hidden" value="<?= $data['user']['id'] ?>" />
            <input name='tgl' type="hidden" value="<?= $currentYear . "-" . $currentMonth ?>" />
            <div class="form-group">
              <label for="exampleInputEmail1">Qty (Banyak)</label>
              <input type="number" name="qty" min="1" class="form-control" id="exampleInputEmail1" value="1" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $dataInject = serialize($arrInject); ?>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>

<script>
  $("form.jq").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      data: $(this).serialize(),
      type: $(this).attr("method"),
      success: function(response) {
        if (response == 1) {
          location.reload(true);
        } else {
          alert(response);
        }
      },
    });
  });

  $("a#tetapkan").click(function() {
    var inject = '<?= $dataInject ?>';
    $.ajax({
      url: '<?= $this->BASE_URL ?>Gaji/tetapkan/<?= $id_user ?>/<?= $dateOn ?>',
      data: {
        data_inject: inject
      },
      type: "POST",
      success: function(response) {
        if (response == 1) {
          location.reload(true);
        } else {
          alert(response);
        }
      },
    });
  });

  var WindowObject;

  function Print() {
    var DocumentContainer = document.getElementById('print');
    WindowObject = window.open('', 'windowName', '', true);
    WindowObject.document.body.innerHTML = '';
    WindowObject.document.write('<title>Cetak | MDL</title><body style="margin:0">');
    WindowObject.document.writeln(DocumentContainer.innerHTML);
    WindowObject.print();
  }

  var click = 0;
  $("span.edit").on('dblclick', function() {
    click = click + 1;
    if (click != 1) {
      return;
    }

    var id_edit = $(this).attr('data-id_edit');
    var value = $(this).html();
    var col = $(this).attr('data-col');
    var table = $(this).attr('data-table');
    var value_before = value;
    var span = $(this);

    var valHtml = $(this).html();
    span.html("<input type='number' style='width:70px' id='value" + id_edit + "' value='" + value + "'>");

    $("#value" + id_edit).focus();
    $("#value" + id_edit).focusout(function() {
      var value_after = $(this).val();
      if (value_after === value_before) {
        span.html(value);
        click = 0;
      } else {
        $.ajax({
          url: '<?= $this->BASE_URL ?>Gaji/updateCell',
          data: {
            'id': id_edit,
            'value': value_after,
            'col': col,
            'table': table
          },
          type: 'POST',
          dataType: 'html',
          success: function(response) {
            location.reload(true);
          },
        });
      }
    });
  });
</script>