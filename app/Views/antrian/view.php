<style>
  .skeleton {
    opacity: .7;
    animation: skeleton-loading 1s linear infinite alternate;
  }

  .skeleton-text {
    width: 100%;
    height: 1rem;
    margin: 0;
    border-radius: .125rem;
  }

  .skeleton-text:last-child {
    margin-bottom: 0;
    width: 80%;
  }

  @keyframes skeleton-loading {
    0% {
      background-color: hsl(200, 20%, 70%);
    }

    100% {
      background-color: hsl(200, 20%, 95%);
    }
  }
</style>

<div id="load" class="content">
  <div class="container-fluid">
    <div class="row p-1">
      <?php
      $cols = 0;
      for ($x = 0; $x <= 12; $x++) {
        $cols++ ?>
        <div class='col p-0 m-1 rounded' style='max-width:400px;'>
          <div class='bg-white rounded'>
            <table class='table table-sm m-0 rounded w-100 bg-white'>
              <tr class=' " . $classHead . " row" . $noref . "' id='tr" . $id . "'>
                <td class='text-center' style="width: 2rem;">
                  <div class="skeleton skeleton-text"></div>
                </td>
                <td colspan='2'>
                  <div class="skeleton skeleton-text"></div>
                </td>
                <td class="text-right">
                  <div style="width: 100%;" class="skeleton skeleton-text"></div>
                </td>
              </tr>
              <tr class='table-borderless'>
                <td class='text-center'><a href='#' class='mb-1 text-secondary'>
                    <div class="skeleton skeleton-text mb-1"></div>
                    <div class="skeleton skeleton-text mb-1"></div>
                    <div class="skeleton skeleton-text"></div>
                </td>
                <td class='text-center'><a href='#' class='mb-1 text-secondary'>
                    <div style="width: 100px;" class="skeleton skeleton-text mb-1"></div>
                    <div class="skeleton skeleton-text mb-1"></div>
                    <div class="skeleton skeleton-text"></div>
                </td>
                <td class='text-center'><a href='#' class='mb-1 text-secondary'>
                    <div style="width: 100px;" class="skeleton skeleton-text mb-1"></div>
                    <div class="skeleton skeleton-text mb-1"></div>
                    <div class="skeleton skeleton-text"></div>
                </td>
                <td class='text-right'><a href='#' class='mb-1 text-secondary'>
                    <div style="width: 100px;" class="skeleton skeleton-text mb-1"></div>
                </td>
              </tr>
              <tr>
                <td class='text-center'><a href='#' class='mb-1 text-secondary'>
                    <div class="skeleton skeleton-text mb-1"></div>
                </td>
                <td colspan="2" class='text-center'><a href='#' class='mb-1 text-secondary'>
                    <div class="skeleton skeleton-text mb-1"></div>
                </td>
                <td class='text-center'><a href='#' class='mb-1 text-secondary'>
                    <div class="skeleton skeleton-text mb-1"></div>
                </td>
              </tr>
            </table>
          </div>
        </div>
      <?php
        if ($cols == 2) {
          echo '<div class="w-100"></div>';
          $cols = 0;
        }
      } ?>

    </div>
  </div>
</div>

<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>

<script>
  $(document).ready(function() {
    loadDiv();
  });

  $("body").dblclick(function() {
    (".modal").hide();
  })

  function loadDiv() {
    $(".loaderDiv").fadeIn("fast");
    $("div#load").load("<?= $this->BASE_URL ?>Antrian/load/" + <?= $data['modeView'] ?>);
    $(".loaderDiv").fadeOut("slow");
  }

  var time = new Date().getTime();
  $(document.body).bind("mousemove keypress", function(e) {
    time = new Date().getTime();
  });

  function clearTuntas() {
    if (new Date().getTime() - time >= 60000)
      $('span.clearTuntas').click();
    else
      setTimeout(clearTuntas, 10000);
  }

  setTimeout(clearTuntas, 10000);
</script>