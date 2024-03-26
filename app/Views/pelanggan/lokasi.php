<div class="container">
  <form class="orderProses" action="<?= $this->BASE_URL ?>Penjualan/proses" method="POST">
    <div class="row">
      <div class="col m-1">
        <label>Pelanggan</label>
        <select name="f1" class="pelanggan form-control form-control-sm" style="width: 100%;" required>
          <option value="" selected disabled></option>
          <?php foreach ($this->pelanggan as $a) { ?>
            <option id=" <?= $a['id_pelanggan'] ?>" value="<?= $a['id_pelanggan'] ?>"><?= strtoupper($a['nama_pelanggan']) . ", " . $a['nomor_pelanggan']  ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
  </form>
  <hr>
  <div id="contentLok">

  </div>
</div>

<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>
<script>
  function selectList() {
    $('select.pelanggan').select2();
  }

  $('select.pelanggan').on('change', function() {
    var id_pelanggan = $(this).val();
    $("#contentLok").load("<?= $this->BASE_URL ?>Pelanggan_Lokasi/content/" + id_pelanggan);
  });

  $(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
  });

  $(document).ready(function() {
    selectList();
  });
</script>