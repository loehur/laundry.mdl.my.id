<div class="content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title text-success">Diskon <b>Kuantitas</b></h4>

            <button type="button" class="btn btn-sm py-0 btn-primary float-right" data-bs-toggle="modal" data-bs-target="#exampleModal">
              +
            </button>

          </div>
          <!-- /.card-header -->
          <div class="card-body p-0">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Jenis</th>
                  <th>Qty Disc</th>
                  <th>Disc Qty</th>
                  <th>Combo</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data['data_main'] as $a) {
                  $id = $a['id_diskon'];
                  $f2 = $a['id_penjualan_jenis'];
                  $f4 = $a['qty_disc'];
                  $f5 = $a['disc_qty'];
                  $f6 = $a['combo'];
                  $penjualan = "";
                  $unit = "";

                  foreach ($this->dPenjualan as $a) {
                    if ($a['id_penjualan_jenis'] == $f2) {
                      $penjualan = $a['penjualan_jenis'];
                    }
                  }

                  foreach ($this->dSatuan as $a) {
                    if ($a['id_satuan'] == $f2) {
                      $unit = $a['nama_satuan'];
                    }
                  }

                  if ($f6 == 0) {
                    $permit = "Tidak Boleh";
                  } else {
                    $permit = "Boleh";
                  }

                  echo "<tr>";
                  echo "<td>" . $penjualan . "</td>";
                  echo "<td class='text-end'><span class='cell' data-mode='2' data-id_value='" . $id . "' data-value='" . $f4 . "'>" . $f4 . "</span>" . $unit . "</td>";
                  echo "<td class='text-end'><span class='cell' data-mode='3' data-id_value='" . $id . "' data-value='" . $f5 . "'>" . $f5 . "</span>%</td>";
                  echo "<td><span style='cursor:pointer' class='cell_s' data-id_value='" . $id . "' data-value='" . $f6 . "'>" . $permit . "</span></td>";
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Set Diskon</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <form action="<?= URL::BASE_URL; ?>SetDiskon/insert" method="POST">
                  <div class="card-body">

                    <!-- ======================================================== -->
                    <div class="form-group">
                      <label>Jenis Penjualan</label>
                      <select name="f1" class="form-control form-control-sm" required>
                        <option value="" disabled selected>---</option>
                        <?php foreach ($this->dPenjualan as $a) { ?>
                          <option value="<?= $a['id_penjualan_jenis'] ?>"><?= $a['penjualan_jenis'] ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Kuantitas Diskon</label>
                      <input type="number" min="0" name="f3" class="form-control form-control-sm" placeholder="" required>
                    </div>
                    <div class="form-group">
                      <label>Diskon %</label>
                      <input type="number" min="0" name="f4" class="form-control form-control-sm" placeholder="" required>
                    </div>
                    <div class="form-group">
                      <label>Izinkan Kombinasi dengan Diskon Partner/Khusus</label>
                      <select class="form-select form-select-sm" name="combo" aria-label=".form-select-sm example" required>
                        <option value="0">Tidak Boleh</option>
                        <option value="1">Boleh</option>
                      </select>
                    </div>
                    <!-- ======================================================== -->

                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-sm btn-primary">Tambah</button>
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
<script src="<?= URL::ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= URL::ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= URL::ASSETS_URL ?>plugins/bootstrap-5.3/js/bootstrap.bundle.min.js"></script>
<script src="<?= URL::ASSETS_URL ?>plugins/select2/select2.min.js"></script>

<script>
  $(document).ready(function() {

  });

  $("form").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      data: $(this).serialize(),
      type: $(this).attr("method"),
      success: function(response) {
        location.reload(true);
      },
    });
  });

  var click = 0;
  $(".cell").on('dblclick', function() {

    click = click + 1;
    if (click != 1) {
      return;
    }

    var id_value = $(this).attr('data-id_value');
    var value = $(this).attr('data-value');
    var mode = $(this).attr('data-mode');
    var value_before = value;
    var span = $(this);

    var valHtml = $(this).html();
    span.html("<input type='number' min='0' class='form-control-sm text-center' style='width:50px' id='value_' value='" + value + "'>");

    $("#value_").focus();
    $("#value_").focusout(function() {
      var value_after = $(this).val();
      if (value_after === value_before) {
        span.html(valHtml);
        click = 0;
      } else {
        $.ajax({
          url: '<?= URL::BASE_URL ?>SetDiskon/updateCell',
          data: {
            'id': id_value,
            'value': value_after,
            'mode': mode
          },
          type: 'POST',
          dataType: 'html',
          success: function(response) {
            span.html(value_after);
            click = 0;
          },
        });
      }
    });
  });

  $(".cell_s").on('dblclick', function() {
    var id_value = $(this).attr('data-id_value');
    var value = $(this).attr('data-value');
    if (value == 0) {
      value = 1;
    } else {
      value = 0;
    }
    $.ajax({
      url: '<?= URL::BASE_URL ?>SetDiskon/updateCell_s',
      data: {
        'id': id_value,
        'value': value,
      },
      type: 'POST',
      dataType: 'html',
      success: function(response) {
        location.reload(true);
      },
    });
  });
</script>