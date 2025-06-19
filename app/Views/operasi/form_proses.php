<?php
if ($data['id_pelanggan'] > 0) {
  $id_pelanggan = $data['id_pelanggan'];
} else {
  $id_pelanggan = 0;
}
?>

<div class="w-100 sticky-top mb-1 px-1">
  <div class="bg-white p-1 rounded border">
    <div class="row mx-0">
      <div class="col" style="max-width: 270px;">
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
        <span onclick="cekData()" class="btn btn-sm btn-secondary form-control form-control-sm" style="height: 34px;">OP</span>
      </div>
      <div class="col-auto pe-0">
        <label>&nbsp;</label>
        <a class="hrfsp" href="<?= URL::BASE_URL ?>Member/tambah_paket/<?= $id_pelanggan ?>"><span class="btn btn-sm btn-outline-secondary form-control form-control-sm" style="height: 34px;">SP</span></a>
      </div>
      <div class="col-auto pe-0">
        <label>&nbsp;</label>
        <a class="hrfsd" href="<?= URL::BASE_URL ?>SaldoTunai/tambah/<?= $id_pelanggan ?>"><span class="btn btn-sm btn-outline-secondary form-control form-control-sm" style="height: 34px;">SD</span></a>
      </div>
    </div>

    <?php if ($_SESSION['user']['book'] == date('Y')) { ?>
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
          </div>
        </form>
      </div>
    <?php } ?>
  </div>
</div>

<div id="load"></div>

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
      if (value.length != 0) {
        load_data_operasi(value);
      }
    },
  });

  $('.tize').click(function() {
    $("select.tize")[0].selectize.clear();
  })

  function load_data_operasi(id) {
    $('.hrfsp').attr('href', '<?= URL::BASE_URL ?>Member/tambah_paket/' + id);
    $('.hrfsd').attr('href', '<?= URL::BASE_URL ?>SaldoTunai/tambah/' + id);
    $("div#load").load("<?= URL::BASE_URL ?>Operasi/loadData/" + id + "/" + <?= $data['mode'] ?>);
  }

  function cekData() {
    var pelanggan = $("select[name=pelanggan").val();

    if (pelanggan.length == 0) {
      return;
    } else {
      load_data_operasi(pelanggan);
    }
  }
</script>