<?php $pelanggan = $data['pelanggan'] ?>
<div class="row ml-2 pl-1 mb-1">
  <div class="col">
    <select name="p" class="pelanggan" required>
      <option value="" selected disabled>...</option>
      <?php foreach ($this->pelanggan as $a) { ?>
        <option id="<?= $a['id_pelanggan'] ?>" value="<?= $a['id_pelanggan'] ?>" <?= ($pelanggan == $a['id_pelanggan']) ? 'selected' : '' ?>><?= strtoupper($a['nama_pelanggan']) . " | " . $a['nomor_pelanggan']  ?></option>
      <?php } ?>
    </select>
    <button id="cekR" class="btn btn-sm btn-primary ml-2 pl-1 pr-1 pt-0 pb-0">
      Cek Data
    </button>
  </div>
</div>
<div class="row ml-2" id="saldoRekap"></div>
<div class="row ml-2" id="riwayat"></div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('select.pelanggan').select2({
      theme: "classic"
    });

    var pelanggan = <?= $pelanggan ?>;
    if (pelanggan > 0) {
      $('div#saldoRekap').load('<?= URL::BASE_URL ?>SaldoTunai/tampil_rekap/0/' + pelanggan);
      $('div#riwayat').load('<?= URL::BASE_URL ?>SaldoTunai/tampilkan/' + pelanggan);
    }
  });

  $("button#cekR").click(function() {
    var pelanggan = $("select[name=p]").val();
    $('div#saldoRekap').load('<?= URL::BASE_URL ?>SaldoTunai/tampil_rekap/0/' + pelanggan);
    $('div#riwayat').load('<?= URL::BASE_URL ?>SaldoTunai/tampilkan/' + pelanggan);
  })

  $("select[name=p]").change(function() {
    $("button#cekR").click();
  });
</script>