<div class="text-nowrap text-center">
  <b>Server</b><br>
  <?php if (isset($data[0]['status']) && $data[0]['status']) { ?>
    <i class="far fa-check-circle text-success"></i> Whatsapp Connected
  <?php } else if (isset($data[0]['qr_ready']) && $data[0]['qr_ready']) { ?>
    <img src="<?= $data[0]['qr_string'] ?>" alt="loading" id="qrcode" />
  <?php } else if (isset($data[0]['qr_ready']) && $data[0]['qr_ready'] == false) { ?>
    <i class="fas fa-spinner text-warning"></i> Loading...
  <?php } else { ?>
    <i class="far fa-times-circle text-danger"></i> Server Down
  <?php } ?>
</div>

<div class="text-nowrap text-center mt-3">
  <?php $d = $data[1] ?>
  <b>Fonnte</b><br>
  <table class="table table-sm">
    <tr>
      <td class="text-end">Device</td>
      <td class="text-start"><?= $d['device'] ?></td>
    </tr>
    <tr>
      <td class="text-end">Status</td>
      <td class="text-start"><?= $d['device_status'] ?></td>
    </tr>
    <tr>
      <td class="text-end">Expired</td>
      <td class="text-start"><?= $d['expired'] ?></td>
    </tr>
    <tr>
      <td class="text-end">Quota</td>
      <td class="text-start"><?= $d['quota'] ?></td>
    </tr>
  </table>
</div>