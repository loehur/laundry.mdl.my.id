<?php $c = $data['cetak']; ?>

<div class="content mt-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="card p-2">
          <form class="orderProses" action="<?= $this->BASE_URL ?>PackLabel/cetak" method="POST">
            <div class="row">
              <div class="col-auto m-1">
                <label>Pelanggan</label>
              </div>
              <div class="col m-1">
                <select name="pelanggan" class="tize form-control form-control-sm" style="width: 100%;" required>
                  <?php foreach ($data['all'] as $a) {
                    $cabang = "";
                    foreach ($this->listCabang as $dc) {
                      if ($dc['id_cabang'] == $a['id_cabang'])
                        $cabang = $dc['kode_cabang'];
                    }
                  ?>
                    <option <?= (isset($c['pelanggan']) && $c['pelanggan'] == strtoupper($a['nama_pelanggan']) ? "selected" : "") ?> value="<?= strtoupper($a['nama_pelanggan']) . "_EXP_" . $cabang ?>"><?= strtoupper($a['nama_pelanggan']) . " " . " [" . strtoupper($cabang) . "]"  ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-auto m-1">
                <label>Jumlah Pack</label>
              </div>
              <div class="col m-1">
                <input type="number" value="<?= (isset($c['jumlah']) ? $c['jumlah'] : "2") ?>" name="jumlah" class="form-control" value="2" min="2" max="10" required>
              </div>
            </div>
            <div class="row">
              <div class="col m-1">
                <button type="submit" class="btn btn-sm btn-success float-end">
                  Cetak
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (isset($c['pelanggan'])) { ?>
  <div class="content mt-1">
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <div class="card p-2">
            <div id="print" style="width:50mm;background-color:white; padding-bottom:10px">
              <style>
                html .table {
                  font-family: 'Titillium Web', sans-serif;
                }

                html .content {
                  font-family: 'Titillium Web', sans-serif;
                }

                html body {
                  font-family: 'Titillium Web', sans-serif;
                }

                hr {
                  border-top: 1px dashed black;
                }
              </style>
              <?php
              $x = 2;
              while ($x <= $c['jumlah']) {
              ?>
                <table style="width:42mm; margin-top:10px; margin-bottom:10px">
                  <tr>
                    <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px; padding-top:15px">
                      <br>
                      <font size='1'><?= $this->dLaundry['nama_laundry'] ?> [<b><?= $this->dCabang['kode_cabang'] ?></b> ]</font>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                      <font size='5'><b><?= strtoupper($c['pelanggan']) ?></b></font>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="text-align: center; padding-top:6px;padding-bottom:6px;">
                      <font size='7'><b><?= $x . "/" . $c['jumlah'] ?></b></font>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" style="text-align: left; padding-bottom:6px;margin-bottom:20px;">
                    </td>
                  </tr>
                </table>
              <?php $x++;
              }
              ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>

<script>
  $(document).ready(function() {
    $('select.tize').selectize();
    Print();
  });

  function Print() {
    var printContents = document.getElementById("print").innerHTML;
    var originalContents = document.body.innerHTML;
    window.document.body.style = 'margin:0';
    window.document.writeln(printContents);
    window.print();
    window.location.href = "<?= $this->BASE_URL ?>PackLabel";
  }
</script>