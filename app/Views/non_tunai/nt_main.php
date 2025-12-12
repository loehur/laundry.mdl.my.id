<div class="row mx-0">
  <?php
  $book = $_SESSION[URL::SESSID]['user']['book'];

  $selisih_book = date("Y") - URL::DB_START;
  $long_char = strlen($selisih_book);

  $no = 0;
  $cols = 0;
  foreach ($data['cek'] as $a) {
    $sts = $a['status_mutasi'];
    $cols++;
    foreach ($this->dStatusMutasi as $st) {
      if ($sts == $st['id_status_mutasi']) {
        $stBayar = $st['status_mutasi'];
      }
    }

    $id = $a['ref_finance'];
    $f1 = substr($a['ref_finance'], $long_char + 2, 2) . "-" . substr($a['ref_finance'], $long_char, 2);
    $f2 = $a['note'];
    $f3 = $a['id_user'];
    $f4 = $a['total'];
    $f17 = $a['id_client'];
    $jenisT = $a['jenis_transaksi'];

    $karyawan = '';
    foreach ($this->userMerge as $c) {
      if ($c['id_user'] == $f3) {
        $karyawan = $c['nama_user'];
      }
    }


    $pelanggan = $f17;
    $jenis_bill = '';
    switch ($jenisT) {
      case 1:
        $jenis_bill = "Laundry";
        foreach ($this->pelanggan as $c) {
          if ($c['id_pelanggan'] == $f17) {
            $pelanggan = $c['nama_pelanggan'];
          }
        }
        break;
      case 3:
        $jenis_bill = "Member";
        foreach ($this->pelanggan as $c) {
          if ($c['id_pelanggan'] == $f17) {
            $pelanggan = $c['nama_pelanggan'];
          }
        }
        break;
      case 5:
        $jenis_bill = "Kasbon<br>";
        foreach ($this->user as $c) {
          if ($c['id_user'] == $f17) {
            $pelanggan = $c['nama_user'];
          }
        }
        break;
      case 6:
        $jenis_bill = "Saldo Deposit";
        foreach ($this->pelanggan as $c) {
          if ($c['id_pelanggan'] == $f17) {
            $pelanggan = $c['nama_pelanggan'];
          }
        }
        break;
    } ?>
    <div class="col px-1 mb-2" style="min-width: 300px;">
      <div class='bg-white rounded border'>
        <table class="table m-0 table-sm">
          <?php
          echo "<tr class='table-info'>";
          echo "<td class='' colspan=2><a class='text-dark' href='" . URL::BASE_URL . "I/i/" . $f17 . "' target='_blank'><i class='fas fa-file-invoice'></i> <b>" . strtoupper($pelanggan) . "</b></a>, " . $jenis_bill . ", " . $f1 . "</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td colspan=2>#" . $id . ", " . $karyawan . "</span></td>";
          echo "</tr>";
          echo "<td class='' colspan=2><span class='text-primary'>" . strtoupper($f2) . "</span> <span class='float-end'>" . number_format($f4) . "</span></td>";
          echo "</tr>";
          ?>
          <tr>
            <td>
              <span class="btn btn-sm text-danger nTunai w-100" data-id="<?= $id ?>" data-target="<?= URL::BASE_URL; ?>NonTunai/operasi/4">Tolak</span>
            </td>
            <td class='text-right'>
              <span class="btn btn-sm text-success nTunai w-100" data-id="<?= $id ?>" data-target="<?= URL::BASE_URL; ?>NonTunai/operasi/3">Terima</span>
            </td>
          </tr>
        </table>
      </div>
    </div>
  <?php
    if ($cols == 4) {
      echo '<div class="w-100"></div>';
      $cols = 0;
    }
  } ?>
</div>
<div class="row mx-0">
  <?php
  $no = 0;
  $cols = 0;
  foreach ($data['done'] as $a) {
    $sts = $a['status_mutasi'];
    $cols++;
    foreach ($this->dStatusMutasi as $st) {
      if ($sts == $st['id_status_mutasi']) {
        $stBayar = $st['status_mutasi'];
      }
    }

    switch ($sts) {
      case "3":
        $cls = "text-success";
        break;
      case "4";
        $cls = "text-danger";
        break;
    }

    $id = $a['ref_finance'];
    $f1 = substr($a['ref_finance'], 0, 4) . "-" . substr($a['ref_finance'], 4, 2) . "-" . substr($a['ref_finance'], 6, 2);
    $f2 = $a['note'];
    $f3 = $a['id_user'];
    $f4 = $a['total'];
    $f17 = $a['id_client'];
    $jenisT = $a['jenis_transaksi'];

    $karyawan = '';
    foreach ($this->userMerge as $c) {
      if ($c['id_user'] == $f3) {
        $karyawan = $c['nama_user'];
      }
    }

    $pelanggan = '';
    $jenis_bill = '';
    switch ($jenisT) {
      case 1:
        $jenis_bill = "Laundry";
        foreach ($this->pelanggan as $c) {
          if ($c['id_pelanggan'] == $f17) {
            $pelanggan = $c['nama_pelanggan'];
          }
        }
        break;
      case 3:
        $jenis_bill = "Member";
        foreach ($this->pelanggan as $c) {
          if ($c['id_pelanggan'] == $f17) {
            $pelanggan = $c['nama_pelanggan'];
          }
        }
        break;
      case 5:
        $jenis_bill = "Kasbon<br>";
        foreach ($this->user as $c) {
          if ($c['id_user'] == $f17) {
            $pelanggan = $c['nama_user'];
          }
        }
        break;
      case 6:
        $jenis_bill = "Saldo Deposit";
        foreach ($this->pelanggan as $c) {
          if ($c['id_pelanggan'] == $f17) {
            $pelanggan = $c['nama_pelanggan'];
          }
        }
        break;
    }

  ?>
    <div class="col px-1 mb-2" style="min-width: 300px;">
      <div class='bg-white rounded'>
        <table class="table m-0 p-0 table-sm mb-1">
          <?php
          echo "<tr class='table-secondary'>";
          echo "<td class='' colspan=2><a class='text-dark' href='" . URL::BASE_URL . "I/i/" . $f17 . "' target='_blank'><i class='fas fa-file-invoice'></i> <b>" . strtoupper($pelanggan) . "</b></a>, " . $jenis_bill . ", " . $f1 . "</td>";
          echo "</tr>";
          echo "<tr>";
          echo "<td colspan=2>#" . $id . ", " . $karyawan . "</span></td>";
          echo "</tr>";
          echo "<td class='' colspan=2><span class='text-primary'>" . strtoupper($f2) . "</span> <span class='float-end'>" . number_format($f4) . "</span>  <span class='" . $cls . "'>" . $stBayar . "</span> </td>";
          echo "</tr>";
          ?>
        </table>
      </div>
    </div>
  <?php
    if ($cols == 4) {
      echo '<div class="w-100"></div>';
      $cols = 0;
    }
  } ?>
</div>

<!-- SCRIPT -->
<script src="<?= URL::EX_ASSETS ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= URL::EX_ASSETS ?>js/popper.min.js"></script>
<script src="<?= URL::EX_ASSETS ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= URL::EX_ASSETS ?>plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= URL::EX_ASSETS ?>plugins/datatables/jquery.dataTables.min.js"></script>

<script>
  $("span.nTunai").on("click", function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr("data-target"),
      data: {
        id: $(this).attr('data-id'),
      },
      type: "POST",
      success: function(response) {
        location.reload(true);
      },
    });
  });
</script>