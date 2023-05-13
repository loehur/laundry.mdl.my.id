<?php if ($data['mode'] == 1) {
  $target_txt = "<b>Dalam Proses</b>";
} else {
  $target_txt = "<b>Non Proses</b>";
}
?>

<?php
if (isset($data['dateF']) && count($data['dateF']) > 0) {
  $currentMonth =   $data['dateF']['m'];
  $currentYear =   $data['dateF']['Y'];
  $currentDay =   $data['dateF']['d'];

  $currentMonthT =   $data['dateT']['m'];
  $currentYearT =   $data['dateT']['Y'];
  $currentDayT =   $data['dateT']['d'];
} else {
  $currentMonth = date('m');
  $currentYear = date('Y');
  $currentDay = date('d');

  $currentMonthT = date('m');
  $currentYearT = date('Y');
  $currentDayT = date('d');
}
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Broadcast Target <?= $target_txt ?></h4>

            <form action="<?= $this->BASE_URL; ?>Broadcast/i/<?= $data['mode'] ?>" method="POST">
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
                        <option class="text-right" value="<?= $year ?>" <?php if ($currentYear == $year) {
                                                                          echo 'selected';
                                                                        } ?>><?= $year ?></option>
                      <?php
                        $year--;
                      } ?>
                    </select>
                  </td>

                  <td class="pr-2 pl-2">s/d</td>

                  <td>
                    <select name="dt" class="form-control form-control-sm" style="width: auto;">
                      <option class="text-right" value="01" <?php if ($currentDayT == '01') {
                                                              echo 'selected';
                                                            } ?>>01</option>
                      <option class="text-right" value="02" <?php if ($currentDayT == '02') {
                                                              echo 'selected';
                                                            } ?>>02</option>
                      <option class="text-right" value="03" <?php if ($currentDayT == '03') {
                                                              echo 'selected';
                                                            } ?>>03</option>
                      <option class="text-right" value="04" <?php if ($currentDayT == '04') {
                                                              echo 'selected';
                                                            } ?>>04</option>
                      <option class="text-right" value="05" <?php if ($currentDayT == '05') {
                                                              echo 'selected';
                                                            } ?>>05</option>
                      <option class="text-right" value="06" <?php if ($currentDayT == '06') {
                                                              echo 'selected';
                                                            } ?>>06</option>
                      <option class="text-right" value="07" <?php if ($currentDayT == '07') {
                                                              echo 'selected';
                                                            } ?>>07</option>
                      <option class="text-right" value="08" <?php if ($currentDayT == '08') {
                                                              echo 'selected';
                                                            } ?>>08</option>
                      <option class="text-right" value="09" <?php if ($currentDayT == '09') {
                                                              echo 'selected';
                                                            } ?>>09</option>
                      <option class="text-right" value="10" <?php if ($currentDayT == '10') {
                                                              echo 'selected';
                                                            } ?>>10</option>
                      <option class="text-right" value="11" <?php if ($currentDayT == '11') {
                                                              echo 'selected';
                                                            } ?>>11</option>
                      <option class="text-right" value="12" <?php if ($currentDayT == '12') {
                                                              echo 'selected';
                                                            } ?>>12</option>
                      <option class="text-right" value="13" <?php if ($currentDayT == '13') {
                                                              echo 'selected';
                                                            } ?>>13</option>
                      <option class="text-right" value="14" <?php if ($currentDayT == '14') {
                                                              echo 'selected';
                                                            } ?>>14</option>
                      <option class="text-right" value="15" <?php if ($currentDayT == '15') {
                                                              echo 'selected';
                                                            } ?>>15</option>
                      <option class="text-right" value="16" <?php if ($currentDayT == '16') {
                                                              echo 'selected';
                                                            } ?>>16</option>
                      <option class="text-right" value="17" <?php if ($currentDayT == '17') {
                                                              echo 'selected';
                                                            } ?>>17</option>
                      <option class="text-right" value="18" <?php if ($currentDayT == '18') {
                                                              echo 'selected';
                                                            } ?>>18</option>
                      <option class="text-right" value="19" <?php if ($currentDayT == '19') {
                                                              echo 'selected';
                                                            } ?>>19</option>
                      <option class="text-right" value="20" <?php if ($currentDayT == '20') {
                                                              echo 'selected';
                                                            } ?>>20</option>
                      <option class="text-right" value="21" <?php if ($currentDayT == '21') {
                                                              echo 'selected';
                                                            } ?>>21</option>
                      <option class="text-right" value="22" <?php if ($currentDayT == '22') {
                                                              echo 'selected';
                                                            } ?>>22</option>
                      <option class="text-right" value="23" <?php if ($currentDayT == '23') {
                                                              echo 'selected';
                                                            } ?>>23</option>
                      <option class="text-right" value="24" <?php if ($currentDayT == '24') {
                                                              echo 'selected';
                                                            } ?>>24</option>
                      <option class="text-right" value="25" <?php if ($currentDayT == '25') {
                                                              echo 'selected';
                                                            } ?>>25</option>
                      <option class="text-right" value="26" <?php if ($currentDayT == '26') {
                                                              echo 'selected';
                                                            } ?>>26</option>
                      <option class="text-right" value="27" <?php if ($currentDayT == '27') {
                                                              echo 'selected';
                                                            } ?>>27</option>
                      <option class="text-right" value="28" <?php if ($currentDayT == '28') {
                                                              echo 'selected';
                                                            } ?>>28</option>
                      <option class="text-right" value="29" <?php if ($currentDayT == '29') {
                                                              echo 'selected';
                                                            } ?>>29</option>
                      <option class="text-right" value="30" <?php if ($currentDayT == '30') {
                                                              echo 'selected';
                                                            } ?>>30</option>
                      <option class="text-right" value="31" <?php if ($currentDayT == '31') {
                                                              echo 'selected';
                                                            } ?>>31</option>
                    </select>
                  </td>

                  <td>
                    <select name="mt" class="form-control form-control-sm" style="width: auto;">
                      <option class="text-right" value="01" <?php if ($currentMonthT == '01') {
                                                              echo 'selected';
                                                            } ?>>01</option>
                      <option class="text-right" value="02" <?php if ($currentMonthT == '02') {
                                                              echo 'selected';
                                                            } ?>>02</option>
                      <option class="text-right" value="03" <?php if ($currentMonthT == '03') {
                                                              echo 'selected';
                                                            } ?>>03</option>
                      <option class="text-right" value="04" <?php if ($currentMonthT == '04') {
                                                              echo 'selected';
                                                            } ?>>04</option>
                      <option class="text-right" value="05" <?php if ($currentMonthT == '05') {
                                                              echo 'selected';
                                                            } ?>>05</option>
                      <option class="text-right" value="06" <?php if ($currentMonthT == '06') {
                                                              echo 'selected';
                                                            } ?>>06</option>
                      <option class="text-right" value="07" <?php if ($currentMonthT == '07') {
                                                              echo 'selected';
                                                            } ?>>07</option>
                      <option class="text-right" value="08" <?php if ($currentMonthT == '08') {
                                                              echo 'selected';
                                                            } ?>>08</option>
                      <option class="text-right" value="09" <?php if ($currentMonthT == '09') {
                                                              echo 'selected';
                                                            } ?>>09</option>
                      <option class="text-right" value="10" <?php if ($currentMonthT == '10') {
                                                              echo 'selected';
                                                            } ?>>10</option>
                      <option class="text-right" value="11" <?php if ($currentMonthT == '11') {
                                                              echo 'selected';
                                                            } ?>>11</option>
                      <option class="text-right" value="12" <?php if ($currentMonthT == '12') {
                                                              echo 'selected';
                                                            } ?>>12</option>
                    </select>
                  </td>
                  <td>
                    <?php
                    $year = date('Y');
                    $oldYear = 2021;
                    ?>
                    <select name="Yt" class="form-control form-control-sm" style="width: auto;">
                      <?php
                      while ($year >= $oldYear) { ?>
                        <option class="text-right" value="<?= $year ?>" <?php if ($currentYearT == $year) {
                                                                          echo 'selected';
                                                                        } ?>><?= $year ?></option>
                      <?php
                        $year--;
                      } ?>
                    </select>
                  </td>

                  <td class="pr-2"><button class="form-control form-control-sm m-1 p-1 bg-success">Cek Pelanggan</button></td>
                  <td>
                    <button type="button" class="btn btn-sm btn-primary float-right" data-bs-toggle="modal" data-bs-target="#exampleModal">
                      + Broadcast
                    </button>
                  </td>
                </tr>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="card-body">
            <div class="container-fluid">
              <div class="row p-1">
                <?php
                $prevPoin = 0;
                $arrRef = array();

                $arrPoin = array();
                $jumlahRef = 0;

                foreach ($data['data'] as $a) {
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

                $arrRekapAntrian = array();
                $arrRekapAntrianToday = array();
                $arrRekapAntrianBesok = array();
                $arrRekapAntrianMiss = array();

                $arrPelangganToday = [];
                $arrPelangganBesok = [];
                $arrPelangganMiss = [];

                $tglToday = date('Y-m-d');
                $tglBesok = date('Y-m-d', strtotime('+1 days'));

                foreach ($data['data'] as $a) {
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

                  $ambil_cek = ($id_ambil > 0) ? "<i class='fas fa-check-circle text-success text-bold'></i> Ambil" : "<i class='far fa-circle'></i> Ambil";

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

                ?>
                  <?php

                  if ($no_urut == 1) {
                    $adaBayar = false;
                    $cols++;
                    echo "<div data-id_pelanggan='" . $f17 . "' id='grid" . $noref . "' class='" . $id . " col shake_hover backShow " . strtoupper($pelanggan) . " p-0 m-1 rounded' style='max-width:400px;cursor:pointer'><div class='bg-white rounded container p-0'>";
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
                    $buttonNotif = "<b>" . $modeNotifShow . "</b> Nota ";
                    $stNotif = "<i class='far fa-circle'></i>";

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
                      }
                    }
                    $buttonNotif = $stNotif . " <span>" . $buttonNotif . "</span>";

                    echo "<tr class=' " . $classHead . " row" . $noref . "' id='tr" . $id . "'>";
                    echo "<td><span style='cursor:pointer' title='" . $pelanggan . "'><b>" . strtoupper($pelanggan_show) . "</b> <small>[" . $f17 . "]</small></span></td>";
                    echo "<td nowrap>" . $buttonNotif . "</td>";
                    echo "<td nowrap class='text-right'><div><span class='text-dark'>" . substr($f1, 2, 14) . "</span></div>
          
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

                  $list_layanan = "";
                  $userOperasi = "";
                  $arrList_layanan = unserialize($f5);
                  $endLayanan = end($arrList_layanan);

                  foreach ($arrList_layanan as $b) {
                    foreach ($this->dLayanan as $c) {
                      if ($c['id_layanan'] == $b) {
                        $check = 0;
                        foreach ($data['operasi'] as $o) {
                          if ($o['id_penjualan'] == $id && $o['jenis_operasi'] == $b) {
                            $check++;
                            foreach ($this->userMerge as $p) {
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
                          }
                          if ($b == $endLayanan) {
                            if ($deadlineSetrikaToday == true) {
                              if (isset($arrRekapAntrianToday[$layananNow])) {
                                $arrRekapAntrianToday[$layananNow] += $f6;
                              } else {
                                $arrRekapAntrianToday[$layananNow] = $f6;
                              }
                              array_push($arrPelangganToday, $id);
                            }
                            if ($deadlineSetrikaBesok == true) {
                              if (isset($arrRekapAntrianBesok[$layananNow])) {
                                $arrRekapAntrianBesok[$layananNow] += $f6;
                              } else {
                                $arrRekapAntrianBesok[$layananNow] = $f6;
                              }
                              array_push($arrPelangganBesok, $id);
                            }
                            if ($deadlineSetrikaMiss == true) {
                              if (isset($arrRekapAntrianMiss[$layananNow])) {
                                $arrRekapAntrianMiss[$layananNow] += $f6;
                              } else {
                                $arrRekapAntrianMiss[$layananNow] = $f6;
                              }
                              array_push($arrPelangganMiss, $id);
                            }
                          }
                        } else {
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
                    $classDurasi = "border border-1 rounded pr-1 pl-1 bg-danger";
                  }
                  ?>

                  <tr id='tr" . $id . "' class='table-borderless'>
                    <td class='pb-0' style="width: 45%;"><span style='white-space: nowrap;'><span style='white-space: nowrap;'></span><b><?= $kategori ?></b><span class='badge badge-light'></span><br><span class="<?= $classDurasi ?>" style='white-space: pre;'><?= $durasi ?> (<?= $f12 ?>h <?= $f13 ?>j)</span><br><small>[<?= $id ?>]</small> <b><?= $show_qty ?></b><br><?= $itemList ?></td>
                    <td class='pb-1' style="width: 23%;"><span class='" . $classDurasi . "' style='white-space: pre;'><?= $list_layanan ?><?= $ambil_cek ?></td>
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

                      $showMutasi = $showMutasi . "<small>" . $statusM . "<b>#" . $ka['id_kas'] . " </b> " . substr($ka['insertTime'], 2, 14) . " " . $nominal . "</small><br>";
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
                  $listAntri .= "<b>Hari ini:</b> ";
                  foreach ($arrRekapAntrianToday as $key => $val) {
                    $listAntri .= "<span class='text-danger' onclick='filterDeadline(1)' style='cursor:pointer'>" . $key . " " . $val . ", </span>";
                  }
                }
                if (count($arrRekapAntrianMiss) > 0) {
                  $listAntri .= " <b>Terlewat:</b> ";
                  foreach ($arrRekapAntrianMiss as $key => $val) {
                    $listAntri .= "<span class='text-danger' onclick='filterDeadline(3)' style='cursor:pointer'>" . $key . " " . $val . ", </span>";
                  }
                }
                if (count($arrPelangganBesok) > 0) {
                  $listAntri .= "<b> Besok: </b>";
                  foreach ($arrRekapAntrianBesok as $key => $val) {
                    $listAntri .= "<span class='text-primary' onclick='filterDeadline(2)' style='cursor:pointer'>" . $key . " " . $val . ", </span>";
                  }
                }
                if (count($arrRekapAntrian) > 0) {
                  $listAntri .= " <b>Antrian:</b> ";
                  foreach ($arrRekapAntrian as $key => $val) {
                    $listAntri .= "<span class='text-success'>" . $key . " " . $val . ", </span>";
                  }
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Buat Broadcast</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="info" style="text-align: center;">Pesan akan di kirimkan kepada pelanggan pada rentang waktu yang ditentukan<br> (<?= $target_txt ?>)</div>
        <form action="<?= $this->BASE_URL; ?>Cabang_List/insert" method="POST">
          <div class="card-body">
            <div class="form-group">
              <label for="exampleInputEmail1">Pesan (Max. 300 karakter)</label><br>
              <textarea class="form-control" name="text" maxlength="300" rows="5"></textarea>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-sm btn-primary">Proses</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>