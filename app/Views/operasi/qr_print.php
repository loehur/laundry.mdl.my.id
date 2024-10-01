<script src="<?= $this->ASSETS_URL ?>js/jquery.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/qrcode.js"></script>

<style>
  hr {
    border-top: 1px dashed black;
  }
</style>

<table style="width:42mm; font-size:x-small; margin-top:10px; margin-bottom:10px">
  <tr>
    <td style="text-align: center;border-bottom:1px dashed black; padding:6px;">
      <h1 style="margin: 0;">Rp<?= number_format($data['jumlah']) ?></h1>
    </td>
  </tr>
  <tr>
    <td style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
      <h3 style="text-align: center; margin:0">TokoPay</h3>
      <p style="margin: 0;text-align: center;">MADINAH LAUNDRY</p>
      <div id="qrcode"></div>
    </td>
  </tr>
</table>


<script type="text/javascript">
  var qrcode = new QRCode(document.getElementById("qrcode"), {
    width: 100,
    height: 100
  });

  function makeCode() {
    qrcode.makeCode("<?= $data['qr_string'] ?>");
  }

  makeCode();

  var window_width = window.innerWidth;
  window.onafterprint = window.close;
  window.print();
</script>