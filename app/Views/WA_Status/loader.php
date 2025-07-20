<div class="content mt-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="card p-2" id="load">
          <div class="text-nowrap text-center">
            <i class="fas fa-spinner text-warning"></i> Loading...
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>

<script>
  $(document).ready(function() {
    reload();

    function reload() {
      $("div#load").load("<?= URL::BASE_URL ?>WA_Status/content");
      setTimeout(reload, 5000);
    }
  });
</script>