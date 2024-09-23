<?php $pelanggan = $data['pelanggan'] ?>

<div class="content">
  <form action="<?= URL::BASE_URL; ?>Poin/menu" method="POST">
    <div class="container">
      <div class="row">
        <div class="col-sm">
          <label for="exampleInputEmail1">Pelanggan</label>
          <select name="pelanggan" class="pelanggan form-control form-control-sm" style="width:auto;" required>
            <option value="" selected disabled>...</option>
            <?php foreach ($this->pelanggan as $a) { ?>
              <option id="<?= $a['id_pelanggan'] ?>" value="<?= $a['id_pelanggan'] ?>" <?php if ($pelanggan == $a['id_pelanggan']) {
                                                                                          echo 'selected';
                                                                                        } ?>><?= strtoupper($a['nama_pelanggan']) . " | " . $a['nomor_pelanggan']  ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-sm">
          <label for="exampleInputEmail1" class="invisible">-</label>
          <div class="form-group">
            <button type="submit" class="btn btn-sm btn-success">
              Cek Poin
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('select.pelanggan').select2({
      theme: "classic"
    });
  });
</script>