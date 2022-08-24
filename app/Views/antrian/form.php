<style>
  table {
    border-radius: 15px;
    overflow: hidden
  }
</style>

<?php
if (count($data['dataTanggal']) > 0) {
  $currentYear =   $data['dataTanggal']['tahun'];
  $pelanggan_post = $data['pelanggan'];
} else {
  $currentYear = date('Y');
  $pelanggan_post = "";
}

$modeView = $data['modeView'];

?>

<div class="content w-100 sticky-top" style="max-width:840px">
  <header>
    <div class="container-fluid">
      <div class=" bg-white p-1 rounded border">
        <?php if ($modeView == 2 && isset($pelanggan_post)) { ?>
          <div class="row m-1">
            <div class="col p-0" style="max-width: 200px;">
              Pelanggan
              <select name="pelanggan" class="pelanggan_post tize form-control form-control-sm" required>
                <option value="" selected disabled>...</option>
                <?php foreach ($this->pelanggan as $a) { ?>
                  <option <?php if ($pelanggan_post == $a['id_pelanggan']) {
                            echo "selected";
                          } ?> value="<?= $a['id_pelanggan'] ?>"><?= strtoupper($a['nama_pelanggan'])  ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col pr-0" style="max-width: 100px;">
              Tahun
              <select name="Y" class="form-control tize form-control-sm">

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
              &nbsp;
              <span onclick="cekPelangganTuntas()" class="btn btn-sm btn-info form-control form-control-sm">Cek</span>
            </div>
          </div>
        <?php } else { ?>
          <div class="row" style="max-width: 800px;">
            <div class="col pt-2 pl-3 pr-0">
              <input id="searchInput" class="form-control form-control-sm mr-3 p-1 bg-light" type="text" placeholder="Pelanggan" style="max-width: 250px;">
            </div>
            <div class="col pt-2 pl-0 pr-3 ml-auto">
              <span class="float-right btn btn-sm btn-success clearTuntas">Refresh</span>
            </div>
          </div>
          <div class="row pl-1">
            <div class="col">
              <div class="d-flex align-items-start align-items-end">
                <div class="p-1 mr-auto">
                  <span id="rekapHarian">...</span>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
  </header>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>

<script>
  $(document).ready(function() {
    $('select.tize').selectize();
  });

  $("input#searchInput").on("keyup change", function() {
    search();
  });

  function search() {
    pelanggan = $("input#searchInput").val().toUpperCase();
    if (pelanggan.length > 0) {
      $("div.backShow").addClass('d-none');
      $("[class*=" + pelanggan + "]").removeClass('d-none');
    } else {
      $(".backShow").removeClass('d-none');
    }
  }

  function cekPelangganTuntas() {
    var pelanggan = $("select[name=pelanggan").val();
    var tahun = $("select[name=Y").val();
    $("div#load").load("<?= $this->BASE_URL ?>Antrian/loadTuntas/" + pelanggan + "/" + tahun);
  }
</script>