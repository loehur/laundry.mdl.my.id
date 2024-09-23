<div class="col-auto">
  <div class="card">
    <div class="card-body p-1">
      <table class="table table-sm" id="dtTable">
        <thead>
          <tr>
            <th>Riwayat Pembelian</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $modal = "data-bs-toggle='modal' data-bs-target='#exampleModal'";
          $no = 0;
          foreach ($data as $a) {
            $id = $a['id'];
            $code = $a['product_code'];
            $customer_id = $a['customer_id'];
            $message = $a['message'];
            $sn = $a['sn'];
            $ref_id = $a['ref_id'];
            $date = substr($a['insertTime'], 0, 10);
            $tr_status = $a['tr_status'];
            $icon_status = '<i class="fas fa-circle text-warning"></i>';
            if ($tr_status == 1) {
              $icon_status = '<i class="fas fa-check-circle text-success"></i>';
            } else if ($tr_status == 2) {
              $icon_status = '<i class="fas fa-times-circle text-danger"></i>';
            }

            echo "<tr>";
            echo "<td>#" . $id . " " . $date . " ";
            echo $customer_id . " " . $icon_status . "<br>";
            echo strtoupper($code) . ", " . $message . "<br>";
            if ($tr_status == 0) { ?>
              <span data-ref="<?= $ref_id ?>" class="text-primary fw-bold" id="cek_status" style="cursor: pointer;">Cek Status</span>
            <?php
            } else if ($tr_status == 2) { ?>
              <span class="text-danger">TRANSACTION FAILED</span>
          <?php } else {
              echo "<span class='text-success'>" . $sn . "</span></td>";
            }
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<!-- SCRIPT -->
<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>

<script>
  $("#cek_status").on("click", function(e) {
    var ref = $(this).attr('data-ref');
    $("#spinner").show();
    e.preventDefault();
    $.ajax({
      url: '<?= URL::BASE_URL ?>Prepaid/cek_status',
      data: {
        ref_id: ref
      },
      type: 'POST',
      success: function(res) {
        $("#spinner").fadeOut();
        if (res == 0) {
          load_data();
        } else {
          $(this).html(res);
        }
      },
    });
  });
</script>