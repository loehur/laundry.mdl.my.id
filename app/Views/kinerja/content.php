<?php
if (count($data['dataTanggal']) > 0) {
  $currentMonth =   $data['dataTanggal']['bulan'];
  $currentYear =   $data['dataTanggal']['tahun'];
} else {
  $currentMonth = date('m');
  $currentYear = date('Y');
}

$r = array();
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
?>



<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="content sticky-top m-3">
            <form action="<?= $this->BASE_URL; ?>Kinerja" method="POST">
              <table class="w-100">
                <tr>
                  <td class="w-25"></td>
                  <td class="w-50"></td>
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
                  <td><button class="form-control form-control-sm m-1 p-1 bg-light">Cek</td>
                </tr>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <?php
      foreach ($r as $userID => $arrJenisJual) {
        foreach ($this->user as $uc) {
          if ($uc['id_user'] == $userID) {

            $user = "<small>[" . $uc['id_user'] . "]</small> - <b>" . $uc['nama_user'] . "<b>";

            echo '<div class="col-auto">';
            echo '<div class="card p-1">';
            echo '<table class="table table-sm">';
            echo '<tbody>';

            echo "<tr>";
            echo "<td colspan='3'>" . strtoupper($user) . "</td>";
            echo "</tr>";

            $penjualan = "Non";

            foreach ($arrJenisJual as $jenisJualID => $arrLayanan) {
              $satuan = "";
              foreach ($this->dPenjualan as $jp) {
                if ($jp['id_penjualan_jenis'] == $jenisJualID) {
                  $penjualan = $jp['penjualan_jenis'];
                  foreach ($this->dSatuan as $js) {
                    if ($js['id_satuan'] == $jp['id_satuan']) {
                      $satuan = $js['nama_satuan'];
                    }
                  }
                }
              }

              echo "<tr class='table-primary'>";
              echo "<td colspan='3'>[ " . $penjualan . " ]</td>";
              echo "</tr>";
              echo "<tr>";
              echo "<td colspan='3'></td>";
              echo "</tr>";

              $layanan = "Non";
              foreach ($arrLayanan as $layananID => $arrCabang) {
                $totalPerUser = 0;
                foreach ($this->dLayanan as $dl) {
                  if ($dl['id_layanan'] == $layananID) {
                    $layanan = $dl['layanan'];
                    foreach ($arrCabang as $cabangID => $c) {
                      $totalPerUser = $totalPerUser + $c;
                      foreach ($this->listCabang as $lc) {
                        if ($lc['id_cabang'] == $cabangID) {
                          $cabang = $lc['kode_cabang'];
                        }
                      }
                      echo "<tr>";
                      echo "<td nowrap>" . $layanan . " <small>[" . $cabang . "]</small></td>";
                      echo "<td class='text-right'>" . $c . "</td>";
                      echo "</tr>";
                    }
                  }
                }
                echo "<tr style='background-color:#F0F8FF'>";
                echo "<td nowrap><small>[<b>Total </b>" . $penjualan . " " . $layanan . "]</small></td>";
                echo "<td class='text-right'><b>" . $totalPerUser . "</b></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td colspan='3'></td>";
                echo "</tr>";
              }
            }

            echo "<tr class='table-primary'>";
            echo "<td colspan='3'>[ Terima/Kembali ]</td>";

            $totalTerima = 0;
            foreach ($data['dTerima'] as $a) {
              if ($uc['id_user'] == $a['id_user']) {
                foreach ($this->listCabang as $lc) {
                  if ($lc['id_cabang'] == $a['id_cabang']) {
                    $cabang = $lc['kode_cabang'];
                  }
                }
                $totalTerima = $totalTerima + $a['terima'];
                echo "<tr>";
                echo "<td nowrap>Terima [" . $cabang . "]</td>";
                echo "<td class='text-right'>" . $a['terima'] . "</td>";
                echo "</tr>";
              }
            }
            echo "<tr style='background-color:#F0F8FF'>";
            echo "<td nowrap><small>[<b>Total </b>Terima]</small></td>";
            echo "<td class='text-right'><b>" . $totalTerima . "</b></td>";
            echo "</tr>";

            $totalKembali = 0;
            foreach ($data['dKembali'] as $a) {
              if ($uc['id_user'] == $a['id_user_ambil']) {
                foreach ($this->listCabang as $lc) {
                  if ($lc['id_cabang'] == $a['id_cabang']) {
                    $cabang = $lc['kode_cabang'];
                  }
                }
                $totalKembali = $totalKembali + $a['kembali'];
                echo "<tr>";
                echo "<td nowrap>Kembali [" . $cabang . "]</td>";
                echo "<td class='text-right'>" . $a['kembali'] . "</td>";
                echo "</tr>";
              }
            }
            echo "<tr style='background-color:#F0F8FF'>";
            echo "<td nowrap><small>[<b>Total </b>Kembali]</small></td>";
            echo "<td class='text-right'><b>" . $totalKembali . "</b></td>";
            echo "</tr>";

            echo "</tr>";
            echo '</tbody>';
            echo '</table>';
            echo '</div></div>';
          }
        }
      }
      ?>
    </div>
  </div>
</div>