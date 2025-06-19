<style>
  table {
    border-radius: 15px;
    overflow: hidden
  }
</style>

<?php $modeView = $data['modeView'];
?>

<div class="w-100 sticky-top px-1 mb-2">
  <div class="py-2 bg-white rounded shadow-sm">
    <div class="row mx-0">
      <div class="col">
        <input id="searchInput" class="form-control form-control-sm mr-3 p-1" type="text" placeholder="Pelanggan" style="max-width: 215px;">
      </div>
    </div>

    <?php if ($_SESSION['user']['book'] == date('Y')) { ?>
      <div class="row ml-0 mt-1 mr-1 w-100">
        <div class="col">
          <div class="d-flex align-items-start align-items-end pt-1">
            <div class="pl-0 pe-1">
              <?php $outline = ($modeView == 1) ? "" : "outline-" ?>
              <a href="<?= URL::BASE_URL ?>Antrian/i/1" type="button" class="btn btn-sm btn-<?= $outline ?>primary">
                Terkini
              </a>
              <?php $outline = "outline-" ?>
            </div>
            <div class="pl-0 pe-1">
              <?php $outline = ($modeView == 6) ? "" : "outline-" ?>
              <a href="<?= URL::BASE_URL ?>Antrian/i/6" type="button" class="btn btn-sm btn-<?= $outline ?>success">
                >1 Minggu
              </a>
              <?php $outline = "outline-" ?>
            </div>
            <div class="pl-0 pe-1">
              <?php $outline = ($modeView == 7) ? "" : "outline-" ?>
              <a href="<?= URL::BASE_URL ?>Antrian/i/7" type="button" class="btn btn-sm btn-<?= $outline ?>info">
                >1 Bulan
              </a>
              <?php $outline = "outline-" ?>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>

    <div class="row ml-0 mt-1 mr-1 w-100">
      <div class="col">
        <span id="rekapAntri"></span>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>

<script>
  $("input#searchInput").on("keyup change", function() {
    search();
  });

  function search() {
    var pelanggan = $("input#searchInput").val().toUpperCase();
    if (pelanggan.length > 0) {
      $("div.backShow").addClass('d-none');
      $("[class*=" + pelanggan + "]").removeClass('d-none');
    } else {
      $(".backShow").removeClass('d-none');
    }
  }
</script>