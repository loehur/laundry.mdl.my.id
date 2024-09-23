<?php $c = $data['cetak']; ?>

<div class="content mt-2">
  <div class="container-fluid">
    <div class="row">
      <div class="col">
        <div class="card p-2">
          <form class="orderProses" action="<?= URL::BASE_URL ?>PackLabel/cetak" method="POST">
            <div class="row">
              <div class="col m-1">
                <label>Label Pelanggan</label>
                <select name="pelanggan" class="tize form-control form-control-sm" style="width: 100%;" required>
                  <option value="" selected></option>
                  <?php foreach ($data['all'] as $a) {
                    $cabang = "";
                    foreach ($this->listCabang as $dc) {
                      if ($dc['id_cabang'] == $a['id_cabang'])
                        $cabang = $dc['kode_cabang'];
                    }
                  ?>
                    <option <?= (isset($c['pelanggan']) && $c['pelanggan'] == strtoupper($a['nama_pelanggan']) ? "selected" : "") ?> value="<?= strtoupper($a['nama_pelanggan']) . "_EXP_" . $cabang ?>"><?= strtoupper($cabang) . " - " . strtoupper($a['nama_pelanggan']) ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col m-1">
                <button type="submit" class="btn btn-sm btn-primary float-right">
                  Cek
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
  <div class="content mt-0">
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <div class="card p-2">
            <div id="print" style="width:50mm;background-color:white; padding-bottom:10px">
              <style>
                @font-face {
                  font-family: "fontku";
                  src: url("<?= $this->ASSETS_URL ?>font/Titillium-Regular.otf");
                }

                html .table {
                  font-family: 'fontku', sans-serif;
                }

                html .content {
                  font-family: 'fontku', sans-serif;
                }

                html body {
                  font-family: 'fontku', sans-serif;
                }

                hr {
                  border-top: 1px dashed black;
                }
              </style>
              <table style="width:42mm; margin-top:10px; margin-bottom:10px" class="ml-auto mr-auto">
                <tr>
                  <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
                    <br>
                    <font size='1'><?= $this->dCabang['nama'] ?> - <b><?= $c['cabang'] ?></b><br>
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
                    <br><br><br><br><br><br>.
                  </td>
                </tr>
                <tr>
                  <td colspan="2">
                    <br>.
                  </td>
                </tr>
              </table>

            </div>
          </div>
        </div>
        <div class="col">
          <button type="submit" onclick="Print()" class="btn btn-sm btn-success">
            Cetak Label
          </button>
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
  });

  function Print() {
    var divContents = document.getElementById("print").innerHTML;
    var a = window.open('');
    a.document.write('<html>');
    a.document.write('<title>Print Page</title>');
    a.document.write('<body>');
    a.document.write(divContents);
    a.document.write('</body></html>');
    var window_width = $(window).width();
    a.print();

    if (window_width > 600) {
      a.close()
    } else {
      setTimeout(function() {
        a.close()
      }, 60000);
    }
  }
</script>