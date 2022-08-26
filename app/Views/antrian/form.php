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
      <div class=" bg-white p-1 pb-2 rounded border">
        <div class="row" style="max-width: 800px;">
          <div class="col pt-2 pl-3 pr-0">
            <input id="searchInput" class="form-control form-control-sm mr-3 p-1 bg-light" type="text" placeholder="Pelanggan" style="max-width: 250px;">
          </div>
          <div class="col pt-2 pl-0 pr-3 ml-auto">
            <span class="float-right btn btn-sm btn-success clearTuntas">Refresh</span>
          </div>
        </div>
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