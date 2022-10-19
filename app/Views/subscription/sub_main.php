<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <button type="button" class="btn btn-sm btn-primary m-2 pl-1 pr-1 pt-0 pb-0 buttonTambah" data-bs-toggle="modal" data-bs-target="#exampleModal">
          (+) Perpanjangan</b>
        </button>
      </div>
      <span class="float-right">Pastikan transfer sesuai jumlah yang tertera hingga digit terakhir! sistem akan melakukan pengecekan 1x24 jam.</span>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <?php
      $registered = strtotime($this->cabang_registerd);
      $aktifFrom =  strtotime("+31 day", $registered);
      foreach ($data['data'] as $z) { ?>
        <div class="col p-0 m-1 rounded">
          <div class="bg-white rounded">
            <table class="table table-sm w-100">
              <tbody>
                <?php
                $id = $z['id_trx'];
                $stBayar = "<b>Proses</b>";
                $active = "";

                switch ($z['trx_status']) {
                  case 3:
                    $stBayar = "<b>Sukses</b>";
                    $active = "<span class='text-success'><b> (Active)</b></span>";
                    break;
                }
                $aktifTo = $z['toDate'];
                $phpdateAktif = strtotime($aktifTo);
                $aktifToShow = date('d-m-Y', $phpdateAktif);
                $today = strtotime(date('Y-m-d'));
                $classTR = "";
                if ($today > $phpdateAktif) {
                  $classTR = "table-secondary";
                  $active = "<span class='text-secondary'><b> (Expired)</b></span>";
                }
                $dibuat = substr($z['insertTime'], 8, 2) . "-" . substr($z['insertTime'], 5, 2) . "-" . substr($z['insertTime'], 0, 4)
                ?>
                <tr class="<?= $classTR ?>">
                  <td><small>#<?= $dibuat ?><br>Masa Aktif:</small><br><?= $aktifToShow . "<small>" . $active . "</small>" ?></td>
                  <td><small>Jumlah/Status</small><br><b><?= number_format($z['jumlah'] + $id) ?><br><small><?= $stBayar ?></b></small></td>
                  <td><small>Metode Bayar</small><br><?= strtoupper($z['bank']) . " - " . $z['no_rek'] . "<br>" . $z['nama_rek'] ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      <?php
      } ?>
    </div>
  </div>
</div>


<form class="ajax" action="<?= $this->BASE_URL; ?>Subscription/insert" method="POST">
  <div class="modal" id="exampleModal">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Perpanjangan Langganan</h5>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="exampleInputEmail1">Paket Langganan</label>
                  <select name="f1" class="bayar form-control form-control-sm" style="width: 100%;" required>
                    <option value="1">1 Bulan</option>
                    <option value="2">2 Bulan</option>
                    <option value="12">12 Bulan</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="exampleInputEmail1">Metode Bayar</label>
                  <select name="f2" class="bayar form-control form-control-sm" style="width: 100%;" required>
                    <?php foreach ($data['bank'] as $b) { ?>
                      <option value="<?= $b['id_bank'] ?>"><?= $b['bank'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Proses</button>
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