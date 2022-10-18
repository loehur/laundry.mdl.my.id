<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-auto">
        <div class="card">
          <div class="card-body p-0">
            <table class="table table-sm w-100">
              <tbody>
                <?php foreach ($data['data_main'] as $a) {
                  $id = $a['id_user'];

                  $f2 = $a['id_cabang'];
                  $f2name = "";
                  foreach ($data['d2'] as $b) {
                    if ($f2 == $b['id_cabang']) {
                      $f2name = $b['kode_cabang'];
                    }
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
                  echo "<tr>";
                  echo "<td><span data-mode=2 data-id_value='" . $id . "' data-value='" . $a['nama_user'] . "'>#" . $id . " " . $a['nama_user'] . "</span></td>";
                  echo "<td><span data-mode=4 data-id_value='" . $id . "' data-value='" . $f2name . "'>" . $f2name . "</span></td>";
                  echo "<td><span data-mode=6 data-id_value='" . $id . "' data-value='" . $a['no_user'] . "'>" . $a['no_user'] . "</span></td>";
                  echo "<td><a data-id_value='" . $id . "' class='text-success enable' href='#'><i class='fas fa-recycle'></i></i></a></td>";
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
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>

<script>
  $(".enable").on("click", function(e) {
    e.preventDefault();
    var id_value = $(this).attr('data-id_value');
    $.ajax({
      url: "<?= $this->BASE_URL ?>Data_List/enable/1",
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
</script>