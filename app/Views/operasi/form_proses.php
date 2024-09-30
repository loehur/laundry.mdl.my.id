<?php
if ($data['formData']['id_pelanggan'] > 0) {
  $id_pelanggan = $data['formData']['id_pelanggan'];
} else {
  $id_pelanggan = "";
}
?>

<div class="content w-100 sticky-top" style="max-width:840px">
  <header>
    <div class="container-fluid">
      <div class="bg-white p-1 rounded border">
        <div class="row m-1">
          <div class="col p-0" style="max-width: 270px;">
            <label>Pelanggan</label>
            <select name="pelanggan" class="id_pelanggan tize form-control form-control-sm" required>
              <option value="" selected disabled>...</option>
              <?php foreach ($this->pelanggan as $a) { ?>
                <option <?php if ($id_pelanggan == $a['id_pelanggan']) {
                          echo "selected";
                        } ?> value="<?= $a['id_pelanggan'] ?>"><?= strtoupper($a['nama_pelanggan'])  ?> | <?= $a['nomor_pelanggan'] ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-auto pe-0">
            <label>&nbsp;</label>
            <span onclick="cekData()" class="btn btn-sm btn-outline-info form-control form-control-sm" style="height: 34px;">Cek</span>
          </div>
          <div class="col-auto pe-0">
            <label>&nbsp;</label>
            <a href="<?= URL::BASE_URL ?>Member/tambah_paket/<?= $id_pelanggan ?>"><span class="btn btn-sm btn-outline-secondary form-control form-control-sm" style="height: 34px;">SP</span></a>
          </div>
          <div class="col-auto pe-0">
            <label>&nbsp;</label>
            <a href="<?= URL::BASE_URL ?>SaldoTunai/tambah/" <?= $id_pelanggan ?>></a> <span class="btn btn-sm btn-outline-secondary form-control form-control-sm" style="height: 34px;">SD</span>
          </div>
        </div>
        <div class="row mt-1 mr-1 w-100">
          <form id="main">
            <div class="d-flex align-items-start align-items-end pb-1">
              <div class="pl-0 pr-1">
                <a href="<?= URL::BASE_URL ?>Antrian/i/1" type="button" class="btn btn-sm btn-outline-primary">
                  Terkini
                </a>
              </div>
              <div class="pl-0 pr-1">
                <a href="<?= URL::BASE_URL ?>Antrian/i/6" type="button" class="btn btn-sm btn-outline-success">
                  >1 Minggu
                </a>
              </div>
              <div class="pl-0 pr-1">
                <a href="<?= URL::BASE_URL ?>Antrian/i/7" type="button" class="btn btn-sm btn-outline-info">
                  >1 Bulan
                </a>
              </div>
              <div class="pl-0 pr-1">
                <a href="<?= URL::BASE_URL ?>Antrian/i/8" type="button" class="btn btn-sm btn-outline-secondary">
                  >1 Tahun
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </header>
</div>

<div id="load" class="content"></div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>

<script>
  $(document).ready(function() {
    $('select.tize').selectize();
    var pelanggan = $("select[name=pelanggan").val();
    if (pelanggan.length != 0) cekData();
  });

  5

  $('select.tize').selectize({
    onChange: function(value) {
      if (value.length != 0) $("div#load").load("<?= URL::BASE_URL ?>Operasi/loadData/" + value + "/0");;
    },
  });

  $('.tize').click(function() {
    $("select.tize")[0].selectize.clear();
  })

  function cekData() {
    var pelanggan = $("select[name=pelanggan").val();
    if (pelanggan.length == 0) {
      return;
    } else {
      $("div#load").load("<?= URL::BASE_URL ?>Operasi/loadData/" + pelanggan + "/0");
    }
  }
</script>