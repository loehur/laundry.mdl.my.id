<?php
if (count($data['dataTanggal']) > 0) {
  $currentMonth =   $data['dataTanggal']['bulan'];
  $currentYear =   $data['dataTanggal']['tahun'];
  $currentDay =   $data['dataTanggal']['tanggal'];
} else {
  $currentMonth = date('m');
  $currentYear = date('Y');
  $currentDay = date('d');
}

?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="content sticky-top m-3">
            <form action="<?= $this->BASE_URL; ?>Rekap/i/1" method="POST">
              <table class="w-100">
                <tr>
                  <td>
                    <select name="d" class="form-control form-control-sm" style="width: auto;">
                      <option class="text-right" value="01" <?php if ($currentDay == '01') {
                                                              echo 'selected';
                                                            } ?>>01</option>
                      <option class="text-right" value="02" <?php if ($currentDay == '02') {
                                                              echo 'selected';
                                                            } ?>>02</option>
                      <option class="text-right" value="03" <?php if ($currentDay == '03') {
                                                              echo 'selected';
                                                            } ?>>03</option>
                      <option class="text-right" value="04" <?php if ($currentDay == '04') {
                                                              echo 'selected';
                                                            } ?>>04</option>
                      <option class="text-right" value="05" <?php if ($currentDay == '05') {
                                                              echo 'selected';
                                                            } ?>>05</option>
                      <option class="text-right" value="06" <?php if ($currentDay == '06') {
                                                              echo 'selected';
                                                            } ?>>06</option>
                      <option class="text-right" value="07" <?php if ($currentDay == '07') {
                                                              echo 'selected';
                                                            } ?>>07</option>
                      <option class="text-right" value="08" <?php if ($currentDay == '08') {
                                                              echo 'selected';
                                                            } ?>>08</option>
                      <option class="text-right" value="09" <?php if ($currentDay == '09') {
                                                              echo 'selected';
                                                            } ?>>09</option>
                      <option class="text-right" value="10" <?php if ($currentDay == '10') {
                                                              echo 'selected';
                                                            } ?>>10</option>
                      <option class="text-right" value="11" <?php if ($currentDay == '11') {
                                                              echo 'selected';
                                                            } ?>>11</option>
                      <option class="text-right" value="12" <?php if ($currentDay == '12') {
                                                              echo 'selected';
                                                            } ?>>12</option>
                      <option class="text-right" value="13" <?php if ($currentDay == '13') {
                                                              echo 'selected';
                                                            } ?>>13</option>
                      <option class="text-right" value="14" <?php if ($currentDay == '14') {
                                                              echo 'selected';
                                                            } ?>>14</option>
                      <option class="text-right" value="15" <?php if ($currentDay == '15') {
                                                              echo 'selected';
                                                            } ?>>15</option>
                      <option class="text-right" value="16" <?php if ($currentDay == '16') {
                                                              echo 'selected';
                                                            } ?>>16</option>
                      <option class="text-right" value="17" <?php if ($currentDay == '17') {
                                                              echo 'selected';
                                                            } ?>>17</option>
                      <option class="text-right" value="18" <?php if ($currentDay == '18') {
                                                              echo 'selected';
                                                            } ?>>18</option>
                      <option class="text-right" value="19" <?php if ($currentDay == '19') {
                                                              echo 'selected';
                                                            } ?>>19</option>
                      <option class="text-right" value="20" <?php if ($currentDay == '20') {
                                                              echo 'selected';
                                                            } ?>>20</option>
                      <option class="text-right" value="21" <?php if ($currentDay == '21') {
                                                              echo 'selected';
                                                            } ?>>21</option>
                      <option class="text-right" value="22" <?php if ($currentDay == '22') {
                                                              echo 'selected';
                                                            } ?>>22</option>
                      <option class="text-right" value="23" <?php if ($currentDay == '23') {
                                                              echo 'selected';
                                                            } ?>>23</option>
                      <option class="text-right" value="24" <?php if ($currentDay == '24') {
                                                              echo 'selected';
                                                            } ?>>24</option>
                      <option class="text-right" value="25" <?php if ($currentDay == '25') {
                                                              echo 'selected';
                                                            } ?>>25</option>
                      <option class="text-right" value="26" <?php if ($currentDay == '26') {
                                                              echo 'selected';
                                                            } ?>>26</option>
                      <option class="text-right" value="27" <?php if ($currentDay == '27') {
                                                              echo 'selected';
                                                            } ?>>27</option>
                      <option class="text-right" value="28" <?php if ($currentDay == '28') {
                                                              echo 'selected';
                                                            } ?>>28</option>
                      <option class="text-right" value="29" <?php if ($currentDay == '29') {
                                                              echo 'selected';
                                                            } ?>>29</option>
                      <option class="text-right" value="30" <?php if ($currentDay == '30') {
                                                              echo 'selected';
                                                            } ?>>30</option>
                      <option class="text-right" value="31" <?php if ($currentDay == '31') {
                                                              echo 'selected';
                                                            } ?>>31</option>
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
                    <?php
                    $year = date('Y');
                    $oldYear = 2021;
                    ?>
                    <select name="Y" class="form-control form-control-sm" style="width: auto;">
                      <?php
                      while ($year >= $oldYear) { ?>
                        <option class="text-right" value="2021" <?php if ($currentYear == $year) {
                                                                  echo 'selected';
                                                                } ?>><?= $year ?></option>
                      <?php
                        $year--;
                      } ?>
                    </select>
                  </td>

                  <td><button class="form-control form-control-sm m-1 p-1 bg-light">Cek</td>
                  <td class="w-50"></td>
                </tr>
              </table>
            </form>
          </div>
        </div>

        <div class="card">
          <?php
          $rekap = array();
          $rekapQty = array();
          foreach ($data['data_main'] as $a) {
            $serLayanan = $a['list_layanan'];
            if (isset($rekap[$a['id_penjualan_jenis']][$serLayanan]) ==  TRUE) {
              $rekap[$a['id_penjualan_jenis']][$serLayanan] =  $rekap[$a['id_penjualan_jenis']][$serLayanan] + $a['qty'];
            } else {
              $rekap[$a['id_penjualan_jenis']][$serLayanan] = $a['qty'];
            }

            if (isset($rekapQty[$a['id_penjualan_jenis']]) ==  TRUE) {
              $rekapQty[$a['id_penjualan_jenis']] =  $rekapQty[$a['id_penjualan_jenis']] + $a['qty'];
            } else {
              $rekapQty[$a['id_penjualan_jenis']] = $a['qty'];
            }
          }
          ?>
          <div class="card-body mt-1 p-0 table-responsive-sm">
            <table class="table table-sm w-100">
              <thead>
                <tr>
                  <th>Jenis</th>
                  <th class="text-right">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($rekapQty as $keyA => $a) {
                  foreach ($this->dPenjualan as $b) {
                    if ($b['id_penjualan_jenis'] == $keyA) {
                      $jenisPenjualan = $b['penjualan_jenis'];
                      $unit = "";
                      foreach ($this->dSatuan as $sa) {
                        if ($sa['id_satuan'] == $b['id_satuan']) {
                          $unit = $sa['nama_satuan'];
                        }
                      }
                      echo "<tr>";
                      echo "<td class='text-primary'><b>" . $jenisPenjualan . "</b></td>";
                      echo "<td class='text-right'><b>" . $a . "</b> " . $unit . "</td>";
                      echo "</tr>";
                    }
                  }
                }
                ?>
              </tbody>
            </table>
          </div>
          <br>
          <div class="card-body p-0 table-responsive-sm">
            <table class="table table-sm w-100">
              <tbody>
                <?php
                $jenisPenjualan = "";
                $jenisPenjualanBefore = "";

                foreach ($rekap as $keyA => $a) {
                  foreach ($this->dPenjualan as $b) {
                    if ($b['id_penjualan_jenis'] == $keyA) {
                      $unit = "";
                      foreach ($this->dSatuan as $sa) {
                        if ($sa['id_satuan'] == $b['id_satuan']) {
                          $unit = $sa['nama_satuan'];
                        }
                      }

                      foreach ($a as $keyB => $c) {
                        $serLayanan = $keyB;
                        $arrLayanan = unserialize($keyB);
                        $layanan = "";
                        foreach ($arrLayanan as $d) {
                          foreach ($this->dLayanan as $e) {
                            if ($d == $e['id_layanan']) {
                              $layanan = $layanan . " " . $e['layanan'];
                            }
                          }
                        }
                        $jenisPenjualan = $b['penjualan_jenis'];
                        if ($jenisPenjualan == $jenisPenjualanBefore) {
                          $jenisPenjualan = "";
                        }
                        echo "<tr>";
                        echo "<td class='text-primary'><b>" . $jenisPenjualan . "</b></td>";
                        echo "<td>" . $layanan . "</td>";
                        echo "<td class='text-right'><b>" . $c . "</b> " . $unit . "</td>";
                        echo "</tr>";
                        $jenisPenjualanBefore = $b['penjualan_jenis'];
                      }
                    }
                  }
                }
                ?>
              </tbody>
            </table>
          </div>

          <?php $total_pendapatan = $data['kasLaundry'] + $data['kasMember']; ?>

          <br>
          <div class="card-body p-0 table-responsive-sm">
            <table class="table table-sm w-100">
              <tbody>
                <tr>
                  <td>Pendapatan Laundry <span class="text-primary">Umum</span></td>
                  <td class="text-right"><b>Rp<?= number_format($data['kasLaundry']) ?></b></td>
                </tr>
                <tr>
                  <td>Pendapatan Laundry <span class="text-success">Member</span></td>
                  <td class="text-right"><b>Rp<?= number_format($data['kasMember']) ?></b></td>
                </tr>
                <tr class="table-success">
                  <td>Total Pendapatan</td>
                  <td class="text-right"><b>Rp<?= number_format($total_pendapatan) ?></b></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-body mt-1 p-0 table-responsive-sm">
            <table class="table table-sm w-100">
              <thead>
                <tr>
                  <th>Jenis</th>
                  <th class="text-right">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $total_keluar = 0;
                foreach ($data['kas_keluar'] as $a) {
                  echo "<tr>";
                  echo "<td class=''>" . $a['note_primary'] . "</td>";
                  echo "<td class='text-right'><b>Rp" . number_format($a['total']) . "</b></td>";
                  echo "</tr>";
                  $total_keluar += $a['total'];
                }
                ?>
              </tbody>
            </table>
          </div>
          <br>
          <div class="card-body p-0 table-responsive-sm">
            <table class="table table-sm w-100">
              <tbody>
                <tr class="table-danger">
                  <td><b>Total Pengeluaran</b></td>
                  <td class="text-right"><b>Rp<?= number_format($total_keluar) ?></b></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card bg-secondary">
          <div class="card-body m-0 p-0 table-responsive-sm">
            <table class="table table-sm w-100">
              <tbody>
                <?php
                echo "<tr>";
                echo "<td class=''>Laba/Rugi</td>";
                echo "<td class='text-right'><b>Rp " . number_format($total_pendapatan - $total_keluar) . "</b></td>";
                echo "</tr>";
                ?>
              </tbody>
            </table>
          </div>
        </div>


      </div>
    </div>
  </div>
</div>