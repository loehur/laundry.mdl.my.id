<table class="table m-0 table-borderless table-sm w-100" style="background-color: lightcyan;">
  <?php
  foreach ($data['data'] as $a) {
    $kategori = "";
    $layanan = "";
    $durasi = "";

    $id = $a['id_harga'];

    foreach ($this->dPenjualan as $dp) {
      if ($dp['id_penjualan_jenis'] == $a['id_penjualan_jenis']) {
        $id_penjualan = $a['id_penjualan_jenis'];
        $jenis = $dp['penjualan_jenis'];
        foreach ($this->dSatuan as $ds) {
          if ($ds['id_satuan'] == $dp['id_satuan']) {
            $unit = $ds['nama_satuan'];
          }
        }
      }
    }
    foreach (unserialize($a['list_layanan']) as $b) {
      foreach ($this->dLayanan as $c) {
        if ($b == $c['id_layanan']) {
          $layanan = $layanan . " " . $c['layanan'];
        }
      }
    }
    foreach ($this->dDurasi as $c) {
      if ($a['id_durasi'] == $c['id_durasi']) {
        $durasi = $durasi . " " . $c['durasi'];
      }
    }

    foreach ($this->itemGroup as $c) {
      if ($a['id_item_group'] == $c['id_item_group']) {
        $kategori = $kategori . " " . $c['item_kategori'];
      }
    }
  ?>
    <tr>
      <td></b> <span class="text-dark"><b><?= $jenis ?></b></span> <?= $kategori ?> * <?= $layanan ?> * <?= $durasi ?></td>
      <td></td>
      <td class="text-right pt-2"><span style="width: 50px;" data-bs-toggle="modal" data-bs-target="#exampleModal" id="pilih_sering" data-id_penjualan="<?= $id_penjualan ?>" data-id_harga="<?= $id ?>" class="btn btn-sm btn-success">Pilih</span></td>
    </tr>
  <?php
  } ?>
</table>

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>

<script>
  $("span#pilih_sering").click(function() {
    var id_harga = $(this).attr("data-id_harga");
    var id_penjualan = $(this).attr('data-id_penjualan');
    var saldo = 0;
    $('div.orderPenjualanForm').load('<?= $this->BASE_URL ?>Penjualan/orderPenjualanForm/' + id_penjualan + '/' + id_harga + "/" + saldo);
  })

  $('span#pilih_sering').each(function() {
    var elem = $(this);
    elem.fadeOut(150)
      .fadeIn(150)
      .fadeOut(150)
      .fadeIn(150)
  });
</script>