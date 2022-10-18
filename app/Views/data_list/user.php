<style>
  .row-disabled {
    background-color: rgba(236, 240, 241, 0.5);
    pointer-events: none;
    width: 100%;
  }
</style>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="card-header">
            <button type="button" class="btn btn-sm btn-primary float-right" data-bs-toggle="modal" data-bs-target="#exampleModal">
              Tambah Karyawan
            </button>
          </div>
          <div class="card-body p-0">
            <table class="table table-sm" style="max-width: 450px;">
              <tbody>
                <?php
                $no = 0;
                foreach ($data['data_main'] as $a) {
                  $no++;

                  $id = $a['id_user'];
                  $f2 = $a['id_cabang'];
                  $f2name = "";
                  foreach ($data['d2'] as $b) {
                    if ($f2 == $b['id_cabang']) {
                      $f2name = $b['kode_cabang'];
                    }
                  }

                  if (!isset($cabangRow)) {
                    echo "<tr class='table-primary'><td colspan=11 class='text-center'>#" . $f2 . " <b>" . $f2name . "</b></td></tr>";
                  }
                  if (isset($cabangRow) && $cabangRow <> $f2) {
                    echo "<tr class='table-primary'><td colspan=11 class='text-center'>#" . $f2 . " <b>" . $f2name . "</b></td></tr>";
                  }

                  $f3 = $a['id_privilege'];
                  $f3name = "";
                  foreach ($this->dPrivilege as $b) {
                    if ($f3 == $b['id_privilege']) {
                      $f3name = $b['privilege'];
                    }
                  }

                  if (strlen($f3name) == 0) {
                    $f3name = "Admin";
                  }

                  $f4 = $a['id_kota'];
                  $f4name = "";
                  foreach ($this->dKota as $b) {
                    if ($f4 == $b['id_kota']) {
                      $f4name = $b['nama_kota'];
                    }
                  }

                  $f5 = $a['akses_layanan'];
                  $list_layanan = "";

                  if ($f3 <> 100) {
                    $arrList_layanan = unserialize($f5);
                    foreach ($arrList_layanan as $b) {
                      foreach ($this->dLayanan as $c) {
                        if ($c['id_layanan'] == $b) {
                          $list_layanan = $list_layanan . " " . $c['layanan'];
                        }
                      }
                    }
                  } else {
                    $list_layanan = "Semua Layanan";
                  }

                  $alamat = ($a['domisili'] == "") ? '[ ]' : $a['domisili'];
                  if ($f3 <> 100) {
                    $classAdmin = "";
                  } else {
                    $classAdmin = "row-disabled";
                  }

                  echo "<tr class='" . $classAdmin . "'>";
                  echo "<td><span data-mode=4 data-id_value='" . $id . "' data-value='" . $f2name . "'>" . $f2name . "</span> " . $no . "#<b>" . $id . "</b> ";
                  echo "<span data-mode=2 data-id_value='" . $id . "' data-value='" . $a['nama_user'] . "'>" . $a['nama_user'] . "</span><br><span data-mode=5 data-id_value='" . $id . "' data-value='" . $f3name . "'>" . $f3name . "</span><br><span id='tdlayanan' data-mode=11 data-id_value='" . $id . "' data-value='" . $a['akses_layanan'] . "'>" . $list_layanan . "</span> <a data-id='" . $id . "' class='addItem badge btn-primary' data-bs-toggle='modal' data-bs-target='#exampleModal2' href='#'><i class='fas fa-edit'></i></a>";
                  echo "</td>";
                  echo "<td><span data-mode=6 data-id_value='" . $id . "' data-value='" . $a['no_user'] . "'>" . $a['no_user'] . "</span><br><span data-mode=7 data-id_value='" . $id . "' data-value='" . $a['email'] . "'>" . $a['email'] . "</span><br><span data-mode=8 data-id_value='" . $id . "' data-value='" . $f4name . "'>" . $f4name . "</span></td>";
                  echo "<td><small>Domisili</small><br><span data-mode=10 data-id_value='" . $id . "' data-value='" . $a['domisili'] . "'>" . $alamat . "</span></td>";
                  echo "<td class='text-right'>";
                  echo " ";
                  echo "</td>";
                  echo "<td><a data-id_value='" . $id . "' class='text-danger enable' href='#'><i class='fas fa-times-circle'></i></a></td>";
                  echo "</tr>";
                  $cabangRow = $f2;
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Penambahan Karyawan</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <!-- ====================== FORM ========================= -->
        <form action="<?= $this->BASE_URL; ?>Data_List/insert/user" method="POST">
          <div class="card-body">
            <div class="form-group">
              <div class="row">
                <div class="col">
                  <label for="exampleInputEmail1">Nama Karyawan</label>
                  <input type="text" name="f1" class="form-control" id="exampleInputEmail1" placeholder="" required>
                </div>
                <div class="col">
                  <label for="exampleInputEmail1">Cabang</label>
                  <select name="f3" class="form-control" required>
                    <option value="" disabled selected>---</option>
                    <?php foreach ($data['d2'] as $a) { ?>
                      <option value="<?= $a['id_cabang'] ?>"><?= $a['kode_cabang'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col">
                  <label for="exampleInputEmail1">Nomor HP</label>
                  <input type="text" name="f5" class="form-control" id="exampleInputEmail1" placeholder="" required>
                </div>
                <div class="col">
                  <label for="exampleInputEmail1">Email</label>
                  <input type="email" name="f6" class="form-control" id="exampleInputEmail1" placeholder="" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col">
                  <label for="exampleInputEmail1">Privilege</label>
                  <select name="f4" class="form-control" required>
                    <option value="" disabled selected>---</option>
                    <?php foreach ($this->dPrivilege as $a) { ?>
                      <option value="<?= $a['id_privilege'] ?>"><?= $a['privilege'] ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col">
                  <label for="exampleInputEmail1">Kota</label>
                  <select name="f7" class="form-control" required>
                    <option value="" disabled selected>---</option>
                    <?php foreach ($this->dKota as $a) { ?>
                      <option value="<?= $a['id_kota'] ?>"><?= $a['nama_kota'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="exampleInputEmail1">Domisili (Optional)</label>
              <input type="text" name="f8" class="form-control" id="exampleInputEmail1" placeholder="">
            </div>
            <div class="form-group">
              <label>Akses Layanan</label>
              <select class="selectMulti form-control form-control-sm" style="width: 100%" name="f9[]" multiple="multiple" required>
                <?php foreach ($this->dLayanan as $a) { ?>
                  <option value="<?= $a['id_layanan'] ?>"><?= $a['layanan'] ?></option>
                <?php } ?>
              </select>
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
<div class="modal" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Akses Layanan</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <form action="<?= $this->BASE_URL; ?>Data_List/updateCell/user" method="POST">
          <div class="card-body">
            <div class="form-group">
              <label>Akses Layanan</label>
              <select class="selectMulti form-control form-control-sm" style="width: 100%" name="value[]" multiple="multiple" required>
                <?php foreach ($this->dLayanan as $a) { ?>
                  <option value="<?= $a['id_layanan'] ?>"><?= $a['layanan'] ?></option>
                <?php } ?>
              </select>
              <input type="hidden" id="idItem" name="id" value="" required>
              <input type="hidden" name="mode" value="11" required>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">Update</button>
      </div>
      </form>
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

    $('.selectMulti').select2({
      theme: "classic"
    });

    $("form").on("submit", function(e) {
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        data: $(this).serialize(),
        type: $(this).attr("method"),
        success: function() {
          location.reload(true);
        },
      });
    });

    $("a.addItem").on('click', function(e) {
      e.preventDefault();
      var idNya = $(this).attr('data-id');
      var valueNya = $(this).attr('data-value');
      $("input#idItem").val(idNya);
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
        case '6':
        case '7':
        case '10':
          span.html("<input type='text' id='value_' value='" + value + "'>");
          break;
        case '4':
          span.html('<select id="value_"><option value="' + value + '" selected>' + valHtml + '</option><?php foreach ($data['d2'] as $a) { ?><option value="<?= $a['id_cabang'] ?>"><?= $a['kode_cabang'] ?></option><?php } ?></select>');
          break;
        case '5':
          span.html('<select id="value_"><option value="' + value + '" selected>' + valHtml + '</option><?php foreach ($this->dPrivilege as $a) : ?><option value="<?= $a['id_privilege'] ?>"><?= $a['privilege'] ?></option><?php endforeach ?></select>');
          break;
        case '8':
          span.html('<select id="value_"><option value="' + value + '" selected>' + valHtml + '</option><?php foreach ($this->dKota as $a) { ?><option value="<?= $a['id_kota'] ?>"><?= $a['nama_kota'] ?></option><?php } ?></select>');
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
            url: '<?= $this->BASE_URL ?>Data_List/updateCell/user',
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

    $(".enable").on("click", function(e) {
      e.preventDefault();
      var id_value = $(this).attr('data-id_value');
      $.ajax({
        url: "<?= $this->BASE_URL ?>Data_List/enable/0",
        data: {
          'id': id_value,
        },
        type: 'POST',
        success: function(response) {
          $('tr.tr' + id_value).remove();
          location.reload(true);
        },
      });
    });
  });
</script>