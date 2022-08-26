<style>
  table {
    border-radius: 15px;
    overflow: hidden
  }
</style>

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
      <div class=" bg-white p-1 rounded border">
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
          <div class="col" style="max-width: 60px;">
            <label>&nbsp;</label>
            <span onclick="cekData()" class="btn btn-sm btn-info form-control form-control-sm">Cek</span>
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

  function cekData() {
    var pelanggan = $("select[name=pelanggan").val();
    if (pelanggan.length == 0) {
      return;
    } else {
      $("div#load").load("<?= $this->BASE_URL ?>Operasi/loadData/" + pelanggan + "/0");
    }
  }
</script>