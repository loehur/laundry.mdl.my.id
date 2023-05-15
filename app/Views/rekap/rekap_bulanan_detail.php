<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-body p-0 table-responsive-sm">
            <table class="table table-sm w-100">
              <thead>
                <tr>
                  <th>Tanggal/Ref</th>
                  <th>Group/Note</th>
                  <th>Jumlah</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $total = 0;
                foreach ($data['data'] as $kld) {
                  $metod = $kld['metode_mutasi'];
                  $metode = "";
                  foreach ($this->dMetodeMutasi as $mm) {
                    if ($mm['id_metode_mutasi'] == $metod) {
                      $metode = $mm['metode_mutasi'];
                    }
                  }
                ?>
                  <tr>
                    <td><?= substr($kld['insertTime'], 0, 10) ?><br><small><?= $kld['id_kas'] ?>-<?= $kld['ref_transaksi'] ?></small></td>
                    <td><?= $kld['note_primary'] ?><br><small><?= $kld['note'] ?></small></td>
                    <td class="text-right"><small><?= $metode ?></small><br>
                      <?= number_format($kld['jumlah']) ?></td>
                  </tr>
                <?php
                  $total += $kld['jumlah'];
                }                ?>
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
      <div class="col">
        <div class="card">
          <div class="card-body p-0 table-responsive-sm">
            <table class="table table-info table-sm w-100">
              <tbody>
                <tr>
                  <td><b>Total</b></td>
                  <td class="text-right"><b><?= number_format($total) ?></b></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>