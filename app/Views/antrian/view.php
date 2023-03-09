<div id="load" class="content"></div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>

<script>
  var mode = "<?= $data['modeView'] ?>"

  $(document).ready(function() {
    mode = "<?= $data['modeView'] ?>"
    if (mode == 2) {
      loadDiv();
    } else {
      loadContent();
    }
  });

  $("body").dblclick(function() {
    (".modal").hide();
  })

  function loadDiv() {
    $(".loaderDiv").fadeIn("fast");
    $("div#load").load("<?= $this->BASE_URL ?>Antrian/load/" + <?= $data['modeView'] ?>);
    $(".loaderDiv").fadeOut("slow");
  }

  function loadContent() {
    $(".loaderDiv").fadeIn("fast");
    $("div#load").load("<?= $this->BASE_URL ?>Antrian/loadList/" + <?= $data['modeView'] ?>);
    $(".loaderDiv").fadeOut("slow");
  }


  $('span.clearTuntas').click(function() {
    $("div.backShow").removeClass('d-none');
  });
</script>