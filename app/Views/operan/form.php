<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="content sticky-top m-3">
            <div class="d-flex align-items-start align-items-end">
              <?php
              $idOperan = $data['idOperan'];
              ?>
              <div class="p-1">
                <label>ID Item (3 Digit Terkahir)</label>
                <input name="idOperan" class="form-control form-control-sm" value="<?= $idOperan ?>" style="width: auto;" required />
              </div>
              <div class="p-1">
                <button onclick="loadDiv()" class="form-control form-control-sm bg-primary">Cek</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="load"></div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>

<script>
  function loadDiv() {
    var idOperan = $("input[name=idOperan]").val();
    $("div#load").load("<?= $this->BASE_URL ?>Operan/load/" + idOperan);
  }

  $('input[name=idOperan]').keypress(function(event) {
    if (event.keyCode == 13) {
      loadDiv();
    }
  });
</script>