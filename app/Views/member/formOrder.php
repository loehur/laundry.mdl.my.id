<form action="<?= $this->BASE_URL ?>Member/deposit/<?= $data['pelanggan']; ?>" method="POST">
  <div class="modal-body">
    <div class="card-body">
      <div class="row">
        <div class="col">
          <label for="exampleInputEmail1">Paket Member</label>
          <select name="f1" class="orderDeposit form-control form-control-sm" id='kiloan' required>
            <?php
            $id_harga = $data['id_harga'];

            foreach ($data['main'] as $z) {
              foreach ($this->harga as $a) {
                if ($a['id_harga'] == $z['id_harga']) {

                  $kategori = "";
                  $layanan = "";
                  $durasi = "";
                  $unit = "";

                  foreach ($this->dPenjualan as $dp) {
                    if ($dp['id_penjualan_jenis'] == $a['id_penjualan_jenis']) {
                      foreach ($this->dSatuan as $ds) {
                        if ($ds['id_satuan'] == $dp['id_satuan']) {
                          $unit = $ds['nama_satuan'];
                        }
                      }
                    }
                  }

                  foreach (unserialize($a['list_layanan']) as $b) {
                    foreach ($this->dLayanan as $c) {
                      if ($b == $c['id_layanan']) {
                        $layanan = $layanan . " " . $c['layanan'];
                      }
                    }
                  }
                  foreach ($this->dDurasi as $c) {
                    if ($a['id_durasi'] == $c['id_durasi']) {
                      $durasi = $durasi . " " . $c['durasi'];
                    }
                  }

                  foreach ($this->itemGroup as $c) {
                    if ($a['id_item_group'] == $c['id_item_group']) {
                      $kategori = $kategori . " " . $c['item_kategori'];
                    }
                  }

                  if ($this->mdl_setting['def_price'] == 0) {
                    $harga = $z['harga'];
                  } else {
                    $harga = $z['harga_b'];
                    if ($harga == 0) {
                      $harga = $z['harga'];
                    }
                  }
            ?>
                  <option value="<?= $z['id_harga_paket'] ?>">M<?= $z['id_harga'] ?> | <?= $kategori ?> * <?= $layanan ?> * <?= $durasi ?> | <?= $z['qty'] . $unit ?>. <?= "Rp" . number_format($harga) ?></option>
            <?php
                }
              }
            } ?>
          </select>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-auto" style="min-width: 200px;">
          <label for="exampleInputEmail1">Karyawan</label>
          <select name="f2" class="tarik form-control form-control-sm" style="width: 100%;" required>
            <option value="" selected disabled></option>
            <optgroup label="<?= $this->dCabang['nama'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
              <?php foreach ($this->user as $a) { ?>
                <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
              <?php } ?>
            </optgroup>
            <?php if (count($this->userCabang) > 0) { ?>
              <optgroup label="----- Cabang Lain -----">
                <?php foreach ($this->userCabang as $a) { ?>
                  <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                <?php } ?>
              </optgroup>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
  </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-sm btn-primary">Tambah</button>
  </div>
  </div>
  </div>
</form>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>

<script>
  $(document).ready(function() {
    selectList();
  });

  function selectList() {
    $('select.tarik').select2({
      dropdownParent: $("#exampleModal"),
    });
    $('select.orderDeposit').select2({
      dropdownParent: $("#exampleModal"),
    });
  }
</script>