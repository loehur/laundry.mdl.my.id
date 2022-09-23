<?php
if ($data['formData']['id_pelanggan'] > 0) {
  $id_pelanggan = $data['formData']['id_pelanggan'];
} else {
  $id_pelanggan = "";
}

if ($data['formData']['tahun'] > 0) {
  $currentYear =   $data['formData']['tahun'];
} else {
  $currentYear = date('Y');
}
?>

<div class="content w-100 sticky-top" style="max-width:840px">
  <header>
    <div class="container-fluid">
      <div class=" bg-white p-1 rounded border">
        <div class="row m-1">
          <div class="col p-0" style="max-width: 270px;">
            <label>Pelanggan</label>
            <select name="pelanggan" class="tize form-control form-control-sm" required>
              <option value="" selected disabled>...</option>
              <?php foreach ($this->pelanggan as $a) { ?>
                <option <?php if ($id_pelanggan == $a['id_pelanggan']) {
                          echo "selected";
                        } ?> value="<?= $a['id_pelanggan'] ?>"><?= strtoupper($a['nama_pelanggan'])  ?> | <?= $a['nomor_pelanggan'] ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col pr-0" style="max-width: 100px;">
            <label>Tahun</label>
            <select name="tahun" class="form-control tize form-control-sm">
              <?php
              for ($x = 2021; $x <= $currentYear; $x++) {
              ?>
                <option class="text-right" value="<?= $x ?>" <?php if ($currentYear == $x) {
                                                                echo 'selected';
                                                              } ?>><?= $x ?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <div class="col" style="max-width: 60px;">
            <label>&nbsp;</label>
            <span onclick="cekData()" class="btn btn-sm btn-info form-control form-control-sm">Cek</span>
          </div>
        </div>
        <div class="row mt-1 mr-1 w-100">
          <form id="main">
            <div class="d-flex align-items-start align-items-end pb-1">
              <div class="pl-0 pr-1">
                <a href="<?= $this->BASE_URL ?>Antrian/i/1" type="button" class="btn btn-sm btn-outline-primary">
                  Terkini
                </a>
              </div>
              <div class="pl-0 pr-1">
                <a href="<?= $this->BASE_URL ?>Antrian/i/6" type="button" class="btn btn-sm btn-outline-success">
                  >1 Minggu
                </a>
              </div>
              <div class="pl-0 pr-1">
                <a href="<?= $this->BASE_URL ?>Antrian/i/7" type="button" class="btn btn-sm btn-outline-info">
                  >1 Bulan
                </a>
              </div>
              <div class="pl-0 pr-1">
                <a href="<?= $this->BASE_URL ?>Antrian/i/8" type="button" class="btn btn-sm btn-outline-secondary">
                  >1 Tahun
                </a>
              </div>
            </div>
          </form>
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
  });

  $('select.tize').selectize({
    onChange: function(value) {
      var tahun = $("select[name=tahun").val();
      if (value.length != 0) $("div#load").load("<?= $this->BASE_URL ?>Operasi/loadData/" + value + "/" + tahun);;
    }
  });

  function cekData() {
    var pelanggan = $("select[name=pelanggan").val();
    var tahun = $("select[name=tahun").val();
    if (pelanggan.length == 0 || tahun.length == 0) {
      return;
    } else {
      $("div#load").load("<?= $this->BASE_URL ?>Operasi/loadData/" + pelanggan + "/" + tahun);
    }
  }
</script>