<?php
$dPelanggan = $data['data_pelanggan'];
?>

<head>
  <meta charset="utf-8">
  <link rel="icon" href="<?= $this->ASSETS_URL ?>icon/logo.png">
  <title><?= strtoupper($dPelanggan['nama_pelanggan']) ?> | MDL</title>
  <meta name="viewport" content="width=480px, user-scalable=no">
  <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>css/ionicons.min.css">
  <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/fontawesome-free-5.15.4-web/css/all.css">
  <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.min.css">
  <link rel="stylesheet" href="<?= $this->ASSETS_URL ?>plugins/adminLTE-3.1.0/css/adminlte.min.css">

  <!-- FONT -->
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

    table {
      border-radius: 15px;
      overflow: hidden
    }
  </style>
</head>

<div class="content">
  <div class="container-fluid mb-1 ml-1 pt-2 border border-bottom" style="position: sticky; top:0px; background-color:white;z-index:2">
    <div class="row p-1">
      <div class="col m-auto" style="max-width: 480px;">
        Bpk/Ibu. <span class="text-success"><b><?= strtoupper($dPelanggan['nama_pelanggan']) ?></b></span>
        <a href="<?= $this->BASE_URL ?>I/i/<?= $dPelanggan['id_pelanggan'] ?>" class="float-right"><span class='border rounded pr-1 pl-1 border-warning'>Tagihan</span></a>
        <br><span class="text-bold">Saldo Tunai:</span> <span class="text-bold text-primary" id="sisa"></span> | <span><small>Last 30 transactions | Updated: <?php echo DATE('Y-m-d') ?></small></span>
      </div>
    </div>
  </div>

  <?php

  echo '<div class="container-fluid" style="z-index:1">';
  echo '<div class="row p-1">';
  echo "<div class='col m-auto w-100 backShow " . strtoupper($dPelanggan['nama_pelanggan']) . " p-0 m-1 rounded' style='max-width:460;'><div class='bg-white rounded border border-success'>";
  echo "<table class='table table-sm m-0 rounded w-100'>";

  $tampil = 30;
  $baris = count($data['data_main']);
  $buang = $baris - $tampil;
  $no = 0;

  foreach ($data['data_main'] as $a) {
    $no += 1;
    if ($buang > 0) {
      $buang -= 1;
      continue;
    }

    if ($no == $baris) {
      $classLast = 'bg-success';
      $textSaldo = 'Saldo Terkini';
    } else {
      $classLast = '';
      $textSaldo = 'Saldo';
    }

    $id = $a['id_kas'];
    $tgl = $a['insertTime'];
    $jumlah = $a['jumlah'];
    $jenis_mutasi = $a['jenis_mutasi'];
    $jenis_transaksi = $a['jenis_transaksi'];
    $saldo = $a['saldo'];

    $topay = "Laundry";
    if ($jenis_transaksi == 3) {
      $topay = "Member";
    }

    if ($jenis_transaksi == 6 && $jenis_mutasi == 1) {
      echo "<tr class='table-success'>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>Deposit<br>Trx ID. [<b>" . $id . "</b>]</small></td>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>Tanggal<br> " . $tgl . "</small></td>";
      echo "<td class='text-right'><small>Topup Rp<br></small><b>" . number_format($jumlah) . "</b></td>";
      echo "<td class='text-right " . $classLast . "'><small>" . $textSaldo . "<br></small><b>" . number_format($saldo) .  "</b></td>";
      echo "</tr>";
    } elseif ($jenis_mutasi == 2 && $jenis_transaksi == 1) {
      echo "<tr class='table-light'>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>Bayar " . $topay . "<br>Trx ID. [<b>" . $id . "</b>]</small></td>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>Tanggal<br> " . $tgl . "</small></td>";
      echo "<td class='text-right'><small>Debit Rp<br></small><b>-" . number_format($jumlah) . "</b></td>";
      echo "<td class='text-right " . $classLast . "'><small>" . $textSaldo . "<br></small><b>" . number_format($saldo) .  "</b></td>";
      echo "</tr>";
    } elseif ($jenis_mutasi == 2 && $jenis_transaksi == 3) {
      echo "<tr class='table-light'>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>Bayar " . $topay . "<br>Trx ID. [<b>" . $id . "</b>]</small></td>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>Tanggal<br> " . $tgl . "</small></td>";
      echo "<td class='text-right'><small>Debit Rp<br></small><b>-" . number_format($jumlah) . "</b></td>";
      echo "<td class='text-right " . $classLast . "'><small>" . $textSaldo . "<br></small><b>" . number_format($saldo) .  "</b></td>";
      echo "</tr>";
    } elseif ($jenis_mutasi == 2 && $jenis_transaksi == 6) {
      echo "<tr class='table-danger'>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>Refund<br>Trx ID. [<b>" . $id . "</b>]</small></td>";
      echo "<td class='pb-0'><span style='white-space: nowrap;'></span><small>Tanggal<br> " . $tgl . "</small></td>";
      echo "<td class='text-right'><small>Debit Rp<br></small><b>-" . number_format($jumlah) . "</b></td>";
      echo "<td class='text-right " . $classLast . "'><small>" . $textSaldo . "<br></small><b>" . number_format($saldo) .  "</b></td>";
      echo "</tr>";
    }
  }

  echo "</table>";
  echo "</div></div></div></div>";
  ?>
</div>

<!-- SCRIPT -->
<script src=" <?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap/js/bootstrap.min.js"></script>

<script>
  $(document).ready(function() {
    $("span#sisa").html("<?= number_format($saldo) ?>");
  })
</script>