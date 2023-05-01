<?php if ($data['mode'] == 1) {
  $target_txt = "<b>Dalam Proses</b>";
} else {
  $target_txt = "<b>Non Proses</b>";
}
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Broadcast Target <?= $target_txt ?></h4>
            <!-- <button type="button" class="btn btn-sm btn-primary float-right" data-bs-toggle="modal" data-bs-target="#exampleModal">
              + Broadcast
            </button> -->
          </div>
          <div class="card-body p-0">

          </div>
        </div>

        <div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Buat Broadcast</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <div id="info" style="text-align: center;">Pesan akan di kirim kepada pelanggan 10 hari terakhir (<?= $target_txt ?>)</div>
                <form action="<?= $this->BASE_URL; ?>Cabang_List/insert" method="POST">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="exampleInputEmail1">Date</label>
                      <input type="text" name="date_" id="dp1" class="form-control form-control-sm" placeholder="yyyy-dd-mm" required>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputEmail1">Pesan (Max. 300 karakter)</label><br>
                      <textarea class="form-control" name="text" maxlength="300" rows="5"></textarea>
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-sm btn-primary">Proses</button>
              </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>

<script>
  $(document).ready(function() {

    $("form").on("submit", function(e) {
      e.preventDefault();
      $.ajax({

        url: $(this).attr('action'),
        data: $(this).serialize(),
        type: $(this).attr("method"),
        dataType: 'html',

        success: function(response) {
          location.reload(true);
        },
      });
    });

    $(".selectRow").click(function() {
      var idNya = $(this).attr('data-id');
      $.ajax({
        url: "<?= $this->BASE_URL ?>Cabang_List/selectCabang",
        data: {
          'id': idNya
        },
        type: "POST",
        success: function(response) {
          location.reload(true);
        },
      });
    });

    $(".cell").on('dblclick', function() {
      var id_value = $(this).attr('data-id_value');
      var value = $(this).attr('data-value');
      var mode = $(this).attr('data-mode');
      var value_before = value;
      var span = $(this);

      var valHtml = $(this).html();
      if (mode == 3) {
        span.html('<select id="value_" required><option value="' + value + '" selected>' + valHtml + '</option><?php foreach ($this->dKota as $a) { ?><option value="<?= $a['id_kota'] ?>"><?= $a['nama_kota'] ?></option><?php } ?></select>');
      } else {
        span.html("<input type='text' id='value_' value='" + value + "'>");
      }

      $("#value_").focus();
      $("#value_").focusout(function() {
        var value_after = $(this).val();
        if (value_after === value_before) {
          span.html(valHtml);
        } else {
          $.ajax({
            url: '<?= $this->BASE_URL ?>Cabang_List/update',
            data: {
              'id': id_value,
              'value': value_after,
              'mode': mode
            },
            type: 'POST',
            dataType: 'html',
            success: function(response) {
              location.reload(true);
            },
          });
        }
      });
    });

  });
</script>