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

  @media print {
    p div {
      font-family: 'fontku', sans-serif;
      font-size: 14px;
    }
  }

  hr {
    border-top: 1px dashed black;
  }
</style>

<table style="width:42mm; font-size:x-small; margin-top:10px; margin-bottom:10px">
  <tr>
    <td style="text-align: center;border-bottom:1px dashed black; padding:6px;">
      <h3 style="margin: 0;">Jumlah Bayar</h3>
      <h1 style="margin: 0;">Rp<?= number_format($data['jumlah']) ?></h1>
      <b></b>
    </td>
  </tr>
  <tr>
    <td style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
      <h3 style="text-align: center; margin:0">MADINAH LAUNDRY</h3>
      <img style="display:block;" src="<?= $data['qr_link'] ?>" width="100%" alt="Girl in a jacket">
    </td>
  </tr>
  <tr>
    <td>.<br>.<br></td>
  </tr>
</table>

<script>
  var window_width = window.innerWidth;
  window.onafterprint = window.close;
  window.print();
</script>