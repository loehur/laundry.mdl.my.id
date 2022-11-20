<?php $c = $data['cetak']; ?>

<div class="content mt-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="card p-2">
          <form class="orderProses" action="<?= $this->BASE_URL ?>PackLabel/cetak" method="POST">
            <div class="row">
              <div class="col m-1">
                <label>Pelanggan</label>
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
              <div class="col m-1">
                <button type="submit" class="btn btn-sm btn-success float-right">
                  Cetak Label
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
              <table style="width:42mm; margin-top:10px; margin-bottom:10px">
                <tr>
                  <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                    <br>
                    <font size='1'><?= $this->dLaundry['nama_laundry'] ?> [<b><?= $c['cabang'] ?></b>]<br>
                      <?= date('Y-m-d h:i:s') ?></font>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                    <font size='5'><b><?= strtoupper($c['pelanggan']) ?></b></font>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" style="text-align: left;border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
                    .<br>.<br>.<br>.<br>.<br>.<br>
                  </td>
                </tr>
              </table>

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