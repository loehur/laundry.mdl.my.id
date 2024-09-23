<div class="content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-auto">

        <div class="card">
          <div class="card-header">
            <h4 class="card-title"><span class="text-success"><?= $data['z']['title'] ?></span></h4>

            <button type="button" class="btn btn-sm btn-primary float-right" data-bs-toggle="modal" data-bs-target="#exampleModal">
              Tambah Poin Set
            </button>

          </div>
          <!-- card-header -->
          <div class="card-body p-0">
            <table class="table table-sm" aria-describedby="example2_info">
              <thead>
                <tr>
                  <th>#</th>
                  <th>List Jenis Penjualan</th>
                  <th>Per Poin</th>
                  <th>Opsi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 0;
                $sisa_item = $this->dPenjualan;
                foreach ($data['data_main'] as $a) {
                  $no++;
                  $id = $a['id_poin_set'];
                  $f1 = $a['list_penjualan_jenis'];
                  $f2 = $a['per_poin'];


                  $list1 = "";
                  $f1b = unserialize($f1);
                  $arrCount = count($f1b);

                  foreach ($f1b as $c) {
                    foreach ($this->dPenjualan as $dkey => $d) {
                      if ($d['id_penjualan_jenis'] == $c) {
                        unset($sisa_item[$dkey]);
                        if ($arrCount > 1) {
                          $list1 = $list1 . "<span id='item" . $d['id_penjualan_jenis'] . "' class='badge badge-light text-dark'>" . $d['penjualan_jenis'] . " <a id='" . $id . "' data-idItem='" . $d['id_penjualan_jenis'] . "' data-value='" . $f1 . "' class='text-danger removeItem' href='#'><i class='fas fa-times-circle'></i></a></span> ";
                        } else {
                          $list1 = $list1 . "<span class='badge badge-light text-dark'>" . $d['penjualan_jenis'] . "</span> ";
                        }
                      }
                    }
                  }

                  echo "<tr>";
                  echo "<td class='text-right'>$no</td>";
                  echo "<td>" . $list1 . "</span></td>";
                  echo "<td class='text-right'><span class='cell' data-mode='1' data-id_value='" . $id . "' data-value='" . $f2 . "'>" . $f2 . "</span></td>";
                  echo "<td><a data-value='" . $f1 . "' data-id='" . $id . "' class='addItem badge btn-primary' data-bs-toggle='modal' data-bs-target='#exampleModal2' href='#'><i class='fas fa-plus-circle'></i></a></td>";
                  echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ============================================================= MODAL =============================================================== -->

        <div class="modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= $data['z']['title'] ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
              </div>
              <div class="modal-body">
                <form action="<?= URL::BASE_URL; ?>SetPoin/insert" method="POST">
                  <div class="card-body">

                    <!-- ======================================================== -->
                    <div class="form-group">
                      <label>Jenis Penjualan</label>
                      <select name="f1[]" class="select2 form-control form-control-sm" style="width: 100%" multiple="multiple" required>
                        <?php foreach ($sisa_item as $a) { ?>
                          <option value="<?= $a['id_penjualan_jenis'] ?>"><?= $a['penjualan_jenis'] ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Per Poin (Rp)</label>
                      <input type="number" name="f2" class="form-control form-control-sm" min='1' required>
                    </div>
                    <!-- ====================================================================================== -->

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

        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Tambah Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="<?= URL::BASE_URL; ?>SetPoin/addItem" method="POST">
                  <div class="card-body">
                    <div class="form-group">
                      <label>Item</label>
                      <select name="f1" class="select2a form-control form-control-sm" style="width: 100%" required>
                        <option value="" selected></option>
                        <?php foreach ($sisa_item as $a) { ?>
                          <option value="<?= $a['id_penjualan_jenis'] ?>"><?= $a['penjualan_jenis'] ?></option>
                        <?php } ?>
                      </select>
                      <input type="hidden" id="idItem" name="f2" value="" required>
                      <input type="hidden" id="valueItem" name="f3" value="" required>
                    </div>
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

        <!-- SCRIPT -->
        <script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
        <script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
        <script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
        <script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>

        <!-- FUNCTION SCRIPTS -->
        <script>
          $(document).ready(function() {
            $('.select2').select2({
              theme: "classic"
            });

            $('.select2a').select2({
              placeholder: "Pilih ITEM",
              dropdownParent: $("#exampleModal2")
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

            $(".cell").on('dblclick', function() {
              var id_value = $(this).attr('data-id_value');
              var value = $(this).attr('data-value');
              var mode = $(this).attr('data-mode');
              var value_before = value;
              var span = $(this);

              var valHtml = $(this).html();
              span.html("<input type='text' style='width:150px;' class='form-control-sm text-center' id='value_' value='" + value + "'>");

              $("#value_").focus();
              $("#value_").focusout(function() {
                var value_after = $(this).val();
                if (value_after === value_before) {
                  span.html(valHtml);
                } else {
                  $.ajax({
                    url: "<?= URL::BASE_URL ?>setPoin/updateCell",
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

            $("a.removeItem").on('click', function(e) {
              e.preventDefault();
              var idNya = $(this).attr('id');
              var idItemNya = $(this).attr('data-idItem');
              var valueNya = $(this).attr('data-value');

              $.ajax({
                url: '<?= URL::BASE_URL ?>setPoin/removeItem',
                data: {
                  'id': idNya,
                  'id_item': idItemNya,
                  'value': valueNya
                },
                type: 'POST',
                success: function() {
                  $("#item" + idItemNya).remove();
                  location.reload(true);
                },
              });
            });

            $("a.addItem").on('click', function(e) {
              e.preventDefault();
              var idNya = $(this).attr('data-id');
              var valueNya = $(this).attr('data-value');
              $("input#idItem").val(idNya);
              $("input#valueItem").val(valueNya);
            });

            $("a.removeRow").on('click', function(e) {
              e.preventDefault();
              var idNya = $(this).attr('data-id');
              $.ajax({
                url: '<?= URL::BASE_URL ?>setGroup/removeRow',
                data: {
                  'id': idNya
                },
                type: 'POST',
                success: function() {
                  location.reload(true);
                },
              });
            });
          })
        </script>