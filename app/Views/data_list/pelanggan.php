<?php $page = $data['z']['page']; ?>

<div class="content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-auto">

        <div class="card">
          <div class="card-header">
            <button type="button" class="btn btn-sm btn-primary float-right" data-bs-toggle="modal" data-bs-target="#exampleModal">
              Tambah Pelanggan
            </button>
          </div>
          <div class="content sticky-top">
            <input id="search" class="form-control form-control-sm m-1 p-1 w-25 bg-light w-50" type="text" placeholder="Pelanggan..">
          </div>
          <div class="card-body p-1 mt-1">
            <table class="table table-sm w-100">
              <tbody>
                <?php
                $no = 0;
                foreach ($data['data_main'] as $a) {
                  $id = $a['id_pelanggan'];
                  $f1 = $a['nama_pelanggan'];
                  $f2 = $a['nomor_pelanggan'];
                  $f3 = $a['id_notif_mode'];
                  $f4 = $a['alamat'];
                  $f5 = $a['disc'];
                  $no++;

                  $f3name = "None";
                  foreach ($this->dNotifMode as $a) {
                    if ($f3 == $a['id_notif_mode']) {
                      $f3name = $a['notif_mode'];
                    }
                  }

                  if ($f2 == '') {
                    $f2 = '08';
                  }
                  echo "<tr>";
                  echo "<td><small>" . $id . "</small><br><span data-mode='1' data-id_value='" . $id . "' data-value='" . $f1 . "'>" . strtoupper($f1) . "</span></td>";
                  echo "<td nowrap><span data-mode='2' data-id_value='" . $id . "' data-value='" . $f2 . "'>" . $f2 . "</span><br><span data-mode='3' data-id_value='" . $id . "' data-value='" . $f3name . "'>" . $f3name . "</span></td>";

                  if ($this->id_privilege == 100 || $this->id_privilege == 101) {
                    echo "<td align='right'>Disc. <span data-mode='5' data-id_value='" . $id . "' data-value='" . $f5 . "'>" . $f5 . "</span>%</td>";
                  } else {
                    echo "<td>Disc. <span data-mode='4' data-id_value='" . $id . "' data-value='" . $f4 . "'>" . $f4 . "</span></td>";
                  }

                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Penambahan Pelanggan</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <!-- ====================== FORM ========================= -->
          <form action="<?= $this->BASE_URL; ?>Data_List/insert/<?= $page ?>" method="POST">
            <div class="card-body">
              <div class="form-group msg"></div>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Nama Pelanggan</label>
              <input type="text" name="f1" class="form-control" id="exampleInputEmail1" placeholder="" required>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Nomor</label>
              <input type="text" name="f2" class="form-control" id="exampleInputEmail1" placeholder="" required>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Notif Mode</label>
              <select name="f3" class="form-control" required>
                <option value="" disabled selected>---</option>
                <?php foreach ($this->dNotifMode as $a) { ?>
                  <option value="<?= $a['id_notif_mode'] ?>"><?= $a['notif_mode'] ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Alamat (Optional)</label>
              <input type="text" name="f4" class="form-control" id="exampleInputEmail1" placeholder="">
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
        success: function(response) {
          if (response == 1) {
            location.reload(true);
          } else {
            $("div.msg").html('<div class="alert alert-danger" role="alert">' + response + '</div>');
          }
        },
      });
    });

    var click = 0;
    $("span").on('dblclick', function() {

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

      switch (mode) {
        case '1':
        case '2':
        case '4':
          span.html("<input type='text' id='value_' value='" + value + "'>");
          break;
        case '5':
          span.html("<input type='number' id='value_' value='" + value + "'>");
          break;
        case '3':
          span.html('<select id="value_"><option value="' + value + '" selected>' + valHtml + '</option><?php foreach ($this->dNotifMode as $a) { ?><option value="<?= $a['id_notif_mode'] ?>"><?= $a['notif_mode'] ?></option><?php } ?></select>');
          break;
        default:
      }

      $("#value_").focus();
      $("#value_").focusout(function() {
        var value_after = $(this).val();
        if (value_after === value_before) {
          span.html(value);
          click = 0;
        } else {
          $.ajax({
            url: '<?= $this->BASE_URL ?>Data_List/updateCell/<?= $page ?>',
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

    $("input#search").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("table tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });
</script>