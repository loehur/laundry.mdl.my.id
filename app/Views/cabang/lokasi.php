<div class="container">
  <div id="contentLok">
  </div>
</div>

<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $("#contentLok").load("<?= URL::BASE_URL ?>Cabang_Lokasi/content");
  });
</script>