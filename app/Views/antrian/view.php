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

<div id="load">

  <div class="content w-100 sticky-top" style="max-width:823px">
    <header>
      <div class="container-fluid">
        <div class="bg-white p-1 rounded border">
          <div id="waitReady" class="d-flex align-items-start align-items-end">

            <div class="p-1" style="width: 175px;">
              <div class="skeleton skeleton-text" style="height: 2rem;"></div><br>
              <div class="skeleton skeleton-text"></div>
            </div>
            <div class="p-1 mr-auto">
              <div class="skeleton skeleton-text"></div>
            </div>
            <div class="p-1">
              <div class="skeleton skeleton-text" style="width: 3rem;">&nbsp;</div>
            </div>
          </div>
        </div>
    </header>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row p-1">
        <?php
        $cols = 0;
        for ($x = 0; $x <= 30; $x++) {
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
    $("div#load").load("<?= $this->BASE_URL ?>Antrian/load/" + <?= $data['modeView'] ?>);
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