<?php
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
      $totalPoinPenjualan  = $totalPoinPenjualan +  $arrTotalPoin[$noref];
    }
    $no = 0;
    $subTotal = 0;
  }
}

$totalPoinMember = 0;
foreach ($data['data_member'] as $z) {
  $harga = $z['harga'];
  $idPoin = $z['id_poin'];
  $perPoin = $z['per_poin'];
  $gPoin_m = 0;
  $gPoin_m = floor($harga / $perPoin);
  $totalPoinMember += $gPoin_m;
}

$arrPoinManual = array_column($data['data_manual'], 'poin_jumlah');
$totalPoinManual = array_sum($arrPoinManual);

$sisaPoin = ($totalPoinPenjualan + $totalPoinManual + $totalPoinMember);
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="card-body p-0 table-responsive-sm">
            <div class="p-1 float-end me-2"><a href="<?= URL::BASE_URL ?>Poin/riwayat/<?= $data['pelanggan'] ?>" target="_blank">Riwayat</a></div>
            <table class="table table-sm w-100">
              <tbody id="tabelAntrian">
                <tr>
                  <td>Poin Manual</td>
                  <td>:</td>
                  <td class="text-right"><?= $totalPoinManual; ?></td>
                </tr>
                <tr>
                  <td>Poin Umum</td>
                  <td>:</td>
                  <td class="text-right"><?= $totalPoinPenjualan; ?></td>
                </tr>
                <tr>
                  <td>Poin Member</td>
                  <td>:</td>
                  <td class="text-right"><?= $totalPoinMember; ?></td>
                </tr>
                <tr>
                  <td>Sisa Poin</td>
                  <td>:</td>
                  <td class="text-right"> <b><?= $sisaPoin; ?></b></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="card-body p-0 table-responsive-sm">
            <button type="button" class="btn btn-sm btn-primary m-1 float-right" data-bs-toggle="modal" data-bs-target="#exampleModal">
              Tambah/Tukar Poin
            </button>
            <table class="table table-sm w-100">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Keterangan</th>
                  <th>Poin (+/-)</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data['data_manual'] as $b) { ?>
                  <tr>
                    <td><?= $b['insertTime'] ?></td>
                    <td><?= $b['keterangan'] ?></td>
                    <td class="text-right"><?= $b['poin_jumlah']; ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<form action="<?= URL::BASE_URL; ?>Poin/insert/<?= $data['pelanggan'] ?>" method="POST">
  <div class="modal" id="exampleModal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Transaksi POIN</h5>
        </div>
        <div class="modal-body">
          <!-- ====================== FORM ========================= -->
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="exampleInputEmail1">Keterangan</label>
                  <input type="text" name="f1" class="form-control form-control-sm" placeholder="Contoh: Tukar Dispenser" required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="exampleInputEmail1">Quantity (Poin) <br><small><b>Awali (-)</b> untuk Penukaran Contoh: -50, <br><b>Awali (+)</b> untuk Penambahan. Contoh: +50</small></label>
                  <input type="number" name="f2" class="form-control form-control-sm" placeholder="" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Tukar/Tambah</button>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('select.pelanggan').select2({
      theme: "classic"
    });
  });
</script>