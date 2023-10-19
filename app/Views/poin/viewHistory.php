<head>
  <meta charset="utf-8">
  <link rel="icon" href="<?= $this->ASSETS_URL ?>icon/logo.png">
  <title>Riwayat Poin | MDL</title>
  <meta name="viewport" content="width=410, user-scalable=no">
  <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.min.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">
  <!-- FONT -->

  <?php $fontStyle = "'Titillium Web', sans-serif;" ?>

  <style>
    html .table {
      font-family: <?= $fontStyle ?>;
    }

    html .content {
      font-family: <?= $fontStyle ?>;
    }

    html body {
      font-family: <?= $fontStyle ?>;
    }

    @media print {
      p div {
        font-family: <?= $fontStyle ?>;
        font-size: 14px;
      }
    }

    .modal-backdrop {
      opacity: 0.1 !important;
    }
  </style>
</head>

<div class="row px-3 pt-2">
  <?php
  $arrUmum = [];
  $arrMember = [];

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
  $arrGetPoin = array();
  $arrTotalPoin = array();
  $arrPoin = array();
  $totalPoinPenjualan = 0;

  foreach ($data['data_main'] as $a) {
    $no++;
    $tgl_u = $a['insertTime'];
    $f6 = $a['qty'];
    $f7 = $a['harga'];
    $f16 = $a['min_order'];
    $noref = $a['no_ref'];
    $idPoin = $a['id_poin'];
    $perPoin = $a['per_poin'];

    $qty_real = 0;
    if ($f6 < $f16) {
      $qty_real = $f16;
    } else {
      $qty_real = $f6;
    }

    if ($no == 1) {
      $subTotal = 0;
      $urutRef++;
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
    $total = ($f7 * $qty_real);
    $subTotal = $subTotal + $total;
    foreach ($arrRef as $key => $m) {
      if ($key == $noref) {
        $arrCount = $m;
      }
    }
    if ($arrCount == $no) {
      if (isset($arrTotalPoin[$noref]) && $arrTotalPoin[$noref] > 0) {
        $totalPoinPenjualan += $arrTotalPoin[$noref];
        array_push($arrUmum, [$noref, $tgl_u, $arrTotalPoin[$noref]]);
      }
      $no = 0;
      $subTotal = 0;
    }
  }

  ?>
  <div class="col-auto border mt-1 mx-1" style="min-width: 400px;">
    <table class="w-100">
      <thead>
        <th>
          No. Referensi
        </th>
        <th class="ps-2">
          Tanggal
        </th>
        <th class="text-end">
          <label class="text-primary"><b>Umum</b></label>
        </th>
      </thead>
      <?php
      foreach ($arrUmum as $au) { ?>
        <tr>
          <td>
            <?= $au[0] ?>
          </td>
          <td class="ps-2">
            <?= substr($au[1], 0, 10)  ?>
          </td>
          <td class="text-end">
            +<?= $au[2] ?>
          </td>
        </tr>
      <?php }
      ?>
      <tr>
        <td colspan="3" class="border-top text-end"><b><?= $totalPoinPenjualan ?></b></td>
      </tr>
    </table>
  </div>

  <?php
  $totalPoinMember = 0;
  foreach ($data['data_member'] as $z) {
    $id = $z['id_member'];
    $tgl_m = $z['insertTime'];
    $harga = $z['harga'];
    $idPoin = $z['id_poin'];
    $perPoin = $z['per_poin'];
    $gPoin_m = 0;
    $gPoin_m = floor($harga / $perPoin);
    $totalPoinMember += $gPoin_m;
    array_push($arrMember, [$id, $tgl_m, $gPoin_m]);
  }
  ?>
  <div class="col-auto border mt-1 mx-1" style="min-width: 400px;">
    <table class="w-100">
      <thead>
        <th>
          Trx. ID
        </th>
        <th class="ps-2">
          Tanggal
        </th>
        <th class="text-end">
          <label class="text-success"><b>Member</b></label>
        </th>
      </thead>
      <?php
      foreach ($arrMember as $au) { ?>
        <tr>
          <td>
            <?= $au[0] ?>
          </td>
          <td class="ps-2">
            <?= substr($au[1], 0, 10)  ?>
          </td>
          <td class="text-end">
            +<?= $au[2] ?>
          </td>
        </tr>
      <?php }
      ?>
      <tr>
        <td colspan="3" class="border-top text-end"><b><?= $totalPoinMember ?></b></td>
      </tr>
    </table>
  </div>
  <?php
  $arrPoinManual = array_column($data['data_manual'], 'poin_jumlah');
  $totalPoinManual = array_sum($arrPoinManual);

  ?>
  <div class="col-auto border mt-1 mx-1" style="min-width: 400px;">
    <table class="w-100">
      <thead>
        <th>
          Trx. ID
        </th>
        <th class="ps-2">
          Tanggal
        </th>
        <th class="text-end">
          <label class="text-danger"><b>Manual</b></label>
        </th>
      </thead>
      <?php
      foreach ($data['data_manual'] as $au) { ?>
        <tr>
          <td>
            <?= $au['id_poin'] ?>
          </td>
          <td class="ps-2">
            <?= substr($au['insertTime'], 0, 10)  ?>
          </td>
          <td class="text-end">
            <?= ($au['poin_jumlah'] > 0) ? "+" : "" ?><?= $au['poin_jumlah'] ?>
          </td>
        </tr>
      <?php }
      ?>
      <tr>
        <td colspan="3" class="border-top text-end"><b><?= $totalPoinManual ?></b></td>
      </tr>
    </table>
  </div>
  <?php

  $sisaPoin = ($totalPoinPenjualan + $totalPoinManual + $totalPoinMember);

  ?>
</div>