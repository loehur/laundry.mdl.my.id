<?php
$z = $data['data'] ?>
<table style="margin: auto; margin-top:50px">
  <tbody>
    <?php
    $id = $z['id_trx'];
    $dibuat = substr($z['insertTime'], 8, 2) . "-" . substr($z['insertTime'], 5, 2) . "-" . substr($z['insertTime'], 0, 4)                ?>
    <tr">
      <td><small>Tanggal</small><br><?= $dibuat ?></td>
      <td><small>Jumlah/Status</small><br><b><?= number_format($z['jumlah'] + $id) ?></td>
      <td><small>Metode Bayar</small><br><?= strtoupper($z['bank']) ?></td>
      </tr>
      <tr>
        <td><a href="<?= $this->BASE_URL ?>Subscription/confirm/<?= $id ?>/3"><button>Verify</button></a></td>
        <td></td>
        <td><a href="<?= $this->BASE_URL ?>Subscription/confirm/<?= $id ?>/4"><button>Reject</button></a></td>
      </tr>
  </tbody>
</table>