<?php
$id_pelanggan = $data['pelanggan'];
$nama_pelanggan = "";
foreach ($this->pelanggan as $dp) {
  if ($dp['id_pelanggan'] == $id_pelanggan) {
    $nama_pelanggan = $dp['nama_pelanggan'];
    $no_pelanggan = $dp['nomor_pelanggan'];
  }
}
?>
<div class="row pl-1">
  <div class="col-auto">
    <span data-id_harga='0' class="btn btn-sm btn-primary m-2 mt-0 pl-1 pr-1 pt-0 pb-0 float-right buttonTambah" data-bs-toggle="modal" data-bs-target="#exampleModal">
      (+) Tambah Saldo Tunai | <b><?= strtoupper($nama_pelanggan) ?></b>
    </span>
  </div>
</div>

<div class="row pl-3">
  <?php
  $cols = 0;
  foreach ($data['data_'] as $z) {
    $cols += 1;
    $id = $z['id_kas'];
    $id_user = $z['id_user'];
    $timeRef = $z['insertTime'];
    $userKas = "";
    $jumlah = $z['jumlah'];
    $note = $z['note'];
    $metode = $z['metode_mutasi'];

    $cs = "";
    foreach ($this->userMerge as $uM) {
      if ($uM['id_user'] == $id_user) {
        $cs = $uM['nama_user'];
      }
    }

    $buttonHapus = "";

    // if ($this->id_privilege >= 100) {
    //   $buttonHapus = "<small><a href='" . $this->BASE_URL . "Member/bin/" . $id . "/" . $id_pelanggan . "' class='hapusRef text-dark'><i class='fas fa-trash-alt'></i></a></small> ";
    // } else {
    //   $buttonHapus = "";
    // }

    $modeNotifShow = "NONE";
    foreach ($this->pelanggan as $c) {
      if ($c['id_pelanggan'] == $id_pelanggan) {
        $no_pelanggan = $c['nomor_pelanggan'];
        $modeNotif = $c['id_notif_mode'];
        foreach ($this->dNotifMode as $a) {
          if ($modeNotif == $a['id_notif_mode']) {
            $modeNotifShow  = $a['notif_mode'];
          }
        }
      }
    }

    if ($modeNotifShow == "Whatsapp") {
      $modeNotifShow = "WA";
    }

    //BUTTON NOTIF
    $buttonNotif = "<a href='#' data-hp='" . $no_pelanggan . "' data-mode='" . $modeNotif . "' data-ref='" . $id . "' data-time='" . $timeRef . "' class='text-dark sendNotifMember'>" . $modeNotifShow . " </a> <span id='notif" . $id . "'><i class='far fa-paper-plane'></i></span>";
    foreach ($data['notif'] as $notif) {
      if ($notif['no_ref'] == $id) {
        $stGet = $notif['status'];
        switch ($stGet) {
          case 1:
          case 5:
            $buttonNotif = "<span>" . $modeNotifShow . " <i class='fas fa-circle text-warning'></i></span>";
            break;
          case 2:
            $buttonNotif = "<span>" . $modeNotifShow . " <i class='fas fa-check-circle text-success'></i></span>";
            break;
          default:
            $stNotif = "<i class='fas fa-exclamation-circle text-danger'></i>";
            break;
        }
      }
    }

    $cabangKode = $this->dCabang['kode_cabang'];

    $st_mutasi = $z['status_mutasi'];

    $stBayar = "";
    foreach ($this->dStatusMutasi as $st) {
      if ($st_mutasi == $st['id_status_mutasi']) {
        $stBayar = ($st['status_mutasi']);
      }
    }

    switch ($st_mutasi) {
      case '2':
        $statusM = "<span class='text-info'>" . $stBayar . " <b>(" . strtoupper($note) . ")</b></span>";
        break;
      case '3':
        $statusM = "<b><i class='fas fa-check-circle text-success'></i></b> " . strtoupper($note) . " ";
        break;
      case '4':
        $statusM = "<span class='text-danger text-bold'><i class='fas fa-times-circle'></i> " . $stBayar . " <b>(" . strtoupper($note) . ")</b></span> - ";
        break;
      default:
        $statusM = "Non Status - ";
        break;
    }

  ?>

    <div class="col p-0 m-1 mb-0 rounded" style='max-width:400px;'>
      <div class="bg-white rounded">
        <table class="table table-sm w-100 pb-0 mb-0 rounded">
          <tbody>
            <tr class="d-none">
              <td>
                <span class="d-none" id="text<?= $id ?>">Deposit Saldo Tunai [<?= $cabangKode . "-" . $id ?>] Rp<?= number_format($jumlah) ?> Berhasil. [Note: <?= $note ?>]. laundry.mdl.my.id/I/s/<?= $this->id_laundry ?>/<?= $id ?></span>
              </td>
            </tr>
            <tr class="table-info">
              <td><a href='#' class='ml-1' onclick='Print("<?= $id ?>")'><i class='text-dark fas fa-print'></i></a></td>
              <td><b><?= strtoupper($nama_pelanggan) ?></b></td>
              <td class="text-right">
                <small><span class='rounded bg-white border pr-1 pl-1 buttonNotif'><?= $buttonNotif ?></span></small>
                <small><span class='rounded bg-white border pr-1 pl-1 buttonNotif'>CS: <?= $cs ?></span></small>
              </td>
            </tr>
            <tr>
              <td class="text-center">
                <?php if ($this->id_privilege >= 100) { ?>
                  <span><?= $buttonHapus ?></span>
                <?php } ?>
              </td>
              <td nowrap>
                <?= $z['insertTime'] ?><br><?= "#" . $id . " " ?>
              </td>
              <td nowrap class="text-right"><b><?= number_format($jumlah) ?></b><br><small><?= $statusM ?></small></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <span class="d-none">
      <span id="<?= $id ?>">Pak/Bu <?= strtoupper($nama_pelanggan) ?>,</span>
    </span>

    <span class="d-none" id="print<?= $id ?>" style="width:50mm;background-color:white; padding-bottom:10px">
      <style>
        html .table {
          font-family: 'Titillium Web', sans-serif;
        }

        html .content {
          font-family: 'Titillium Web', sans-serif;
        }

        html body {
          font-family: 'Titillium Web', sans-serif;
        }

        hr {
          border-top: 1px dashed black;
        }
      </style>
      <table style="width:42mm; font-size:x-small; margin-top:10px; margin-bottom:10px">
        <tr>
          <td colspan="2" style="text-align: center;border-bottom:1px dashed black; padding:6px;">
            <b> <?= $this->dLaundry['nama_laundry'] ?> [ <?= $this->dCabang['kode_cabang'] ?></b> ]<br>
            <?= $this->dCabang['alamat'] ?>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="border-bottom:1px dashed black; padding-top:6px;padding-bottom:6px;">
            <font size='2'><b><?= strtoupper($nama_pelanggan) ?></b></font><br>
            ID Trx. <?= $id ?><br>
            <?= $z['insertTime'] ?>
          </td>
        </tr>
        <td style="margin: 0;">Deposit Saldo Tunai</td>
        <td align="right"><?= number_format($jumlah) ?></td>
        <tr>
          <td colspan="2" style="border-bottom:1px dashed black;"></td>
        </tr>
        <tr>
          <td colspan="2"><br><br><br><br>.</td>
        </tr>
      </table>
    </span>
  <?php
    if ($cols == 2) {
      echo '<div class="w-100"></div>';
      $cols = 0;
    }
  } ?>
</div>

<div class="modal" id="exampleModal">
  <div class="modal-dialog">
    <div class="modal-content tambahPaket">

    </div>
  </div>
</div>

<script src="<?= $this->ASSETS_URL ?>js/jquery-3.6.0.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/popper.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.bundle.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>plugins/select2/select2.min.js"></script>
<script src="<?= $this->ASSETS_URL ?>js/selectize.min.js"></script>
<script>
  $(document).ready(function() {
    $("div#nTunai").hide();
    $("input#searchInput").addClass("d-none");
    $('select.tize').selectize();
    $("td#btnTambah").removeClass("d-none");

    $('td#btnTambah').each(function() {
      var elem = $(this);
      elem.fadeOut(150)
        .fadeIn(150)
        .fadeOut(150)
        .fadeIn(150)
    });
  });

  $("a.sendNotifMember").on('click', function(e) {
    e.preventDefault();
    var hpNya = $(this).attr('data-hp');
    var modeNya = $(this).attr('data-mode');
    var refNya = $(this).attr('data-ref');
    var timeNya = $(this).attr('data-time');
    var textNya = $("span#text" + refNya).html();
    $.ajax({
      url: '<?= $this->BASE_URL ?>SaldoTunai/sendNotifDeposit',
      data: {
        hp: hpNya,
        text: textNya,
        mode: modeNya,
        ref: refNya,
        time: timeNya,
      },
      type: "POST",
      success: function() {
        $("span#notif" + refNya).hide();
        $("span#notif" + refNya).html("<i class='fas fa-circle text-warning'></i>")
        $("span#notif" + refNya).fadeIn('slow');
      },
    });
  });


  $("select.metodeBayar").on("keyup change", function() {
    if ($(this).val() == 2) {
      $("div#nTunai").show();
    } else {
      $("div#nTunai").hide();
    }
  });


  $("form.ajax").on("submit", function(e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      data: $(this).serialize(),
      type: $(this).attr("method"),
      success: function() {
        location.reload(true);
      },
    });
  });

  $("span.buttonTambah").on("click", function(e) {
    var id_harga = $(this).attr("data-id_harga");
    $('div.tambahPaket').load("<?= $this->BASE_URL ?>SaldoTunai/orderPaket/<?= $id_pelanggan ?>/" + id_harga);
  });

  $("a.bayarMember").on('click', function(e) {
    e.preventDefault();
    var refNya = $(this).attr('data-ref');
    var bayarNya = $(this).attr('data-harga');
    var id_pelanggan = $(this).attr('data-idPelanggan');
    $("input.idItemMember").val(refNya);
    $("input.jumlahBayarMember").val(bayarNya);
    $("input.idPelangganMember").val(id_pelanggan);
    $("input.jumlahBayarMember").attr({
      'max': bayarNya
    });
  });

  $("a.bayarPasMember").on('click', function(e) {
    e.preventDefault();
    var jumlahPas = $("input.jumlahBayarMember").val();
    $("input.dibayarMember").val(jumlahPas);
    diBayar = $("input.dibayarMember").val();
  });

  $("input.dibayarMember").on("keyup change", function() {
    diBayar = 0;
    diBayar = $(this).val();
    var kembalian = $(this).val() - $('input.jumlahBayarMember').val()
    if (kembalian > 0) {
      $('input.kembalianMember').val(kembalian);
    } else {
      $('input.kembalianMember').val(0);
    }
  });

  function Print(id) {
    var printContents = document.getElementById("print" + id).innerHTML;
    var originalContents = document.body.innerHTML;
    window.document.body.style = 'margin:0';
    window.document.writeln(printContents);
    window.print();
    location.reload(true);
  }
</script>