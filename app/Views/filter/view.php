<div id="load" class="content"></div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>

<script>
  var mode = "<?= $data['modeView'] ?>"

  $(document).ready(function() {
    loadContent();
  });

  $("body").dblclick(function() {
    (".modal").hide();
  })

  function loadContent() {
    $(".loaderDiv").fadeIn("fast");
    $("div#load").load("<?= $this->BASE_URL ?>Filter/loadList/" + <?= $data['modeView'] ?>);
    $(".loaderDiv").fadeOut("slow");
  }
</script>