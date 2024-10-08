<?php
if (isset($data['status']) && $data['status']) { ?>
  <img src="<?= $this->ASSETS_URL ?>wa/check.svg" alt="loading" id="qrcode" />
<?php } else if (isset($data['qr_ready']) && $data['qr_ready']) { ?>
  <img src="<?= $data['qr_string'] ?>" alt="loading" id="qrcode" />
<?php } else if (isset($data['qr_ready']) && $data['qr_ready'] == false) { ?>
  <img src="<?= $this->ASSETS_URL ?>wa/loader.svg" alt="loading" id="qrcode" />
<?php } else { ?>
  <img src="<?= $this->ASSETS_URL ?>wa/cross.svg" alt="loading" id="qrcode" />
<?php } ?>