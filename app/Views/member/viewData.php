<?php
$pelanggan = $data['pelanggan'];
$nama_pelanggan = "";
foreach ($this->pelanggan as $dp) {
  if ($dp['id_pelanggan'] == $pelanggan) {
    $nama_pelanggan = $dp['nama_pelanggan'];
    $no_pelanggan = $dp['nomor_pelanggan'];
  }
}
?>
<div class="row pl-1">
  <div class="col-auto">
    <span data-id_harga='0' class="btn btn-sm btn-primary m-2 mt-0 pl-1 pr-1 pt-0 pb-0 float-right buttonTambah" data-bs-toggle="modal" data-bs-target="#exampleModal">
      (+) Tambah Paket | <b><?= strtoupper($nama_pelanggan) ?></b>
    </span>
  </div>
</div>
<div class="row pl-3">
  <?php
  $cols = 0;
  foreach ($data['data_manual'] as $z) {
    $cols += 1;
    $id = $z['id_member'];
    $id_harga = $z['id_harga'];
    $harga = $z['harga'];
    $id_user = $z['id_user'];
    $kategori = "";
    $layanan = "";
    $durasi = "";
    $unit = "";
    $idPoin = $z['id_poin'];
    $perPoin = $z['per_poin'];
    $timeRef = $z['insertTime'];

    $gPoin = 0;
    $gPoinShow = "";
    if ($idPoin > 0) {
      $gPoin = floor($harga / $perPoin);
      $gPoinShow = "<small class='text-success'>(+" . $gPoin . ")</small>";
    }

    $showMutasi = "";
    $userKas = "";
    foreach ($data['kas'] as $ka) {
      if ($ka['ref_transaksi'] == $id) {
        foreach ($this->userMerge as $usKas) {
          if ($usKas['id_user'] == $ka['id_user']) {
            $userKas = $usKas['nama_user'];
          }
        }

        $stBayar = "";
        foreach ($this->dStatusMutasi as $st) {
          if ($ka['status_mutasi'] == $st['id_status_mutasi']) {
            $stBayar = $st['status_mutasi'];
          }
        }

        $notenya = strtoupper($ka['note']);
        $st_mutasi = $ka['status_mutasi'];

        switch ($st_mutasi) {
          case '2':
            $statusM = "<span class='text-info'>" . $stBayar . " <b>(" . $notenya . ")</b></span> - ";
            break;
          case '3':
            $statusM = "<b><i class='fas fa-check-circle text-success'></i></b> " . $notenya . " ";
            break;
          case '4':
            $statusM = "<span class='text-danger text-bold'><i class='fas fa-times-circle'></i> " . $stBayar . " <b>(" . $notenya . ")</b></span> - ";
            break;
          default:
            $statusM = "Non Status - ";
            break;
        }

        if ($st_mutasi == 4) {
          $nominal = "<s>-Rp" . number_format($ka['jumlah']) . "</s>";
        } else {
          $nominal = "-Rp" . number_format($ka['jumlah']);
        }

        $showMutasi = $showMutasi . "<small>" . $statusM . "<b>#" . $ka['id_kas'] . " " . $userKas . "</b> " . substr($ka['insertTime'], 5, 11) . " " . $nominal . "</small><br>";
      }
    }

    foreach ($this->harga as $a) {
      if ($a['id_harga'] == $z['id_harga']) {
        foreach ($this->dPenjualan as $dp) {
          if ($dp['id_penjualan_jenis'] == $a['id_penjualan_jenis']) {
            foreach ($this->dSatuan as $ds) {
              if ($ds['id_satuan'] == $dp['id_satuan']) {
                $unit = $ds['nama_satuan'];
              }
            }
          }
        }
        foreach (unserialize($a['list_layanan']) as $b) {
          foreach ($this->dLayanan as $c) {
            if ($b == $c['id_layanan']) {
              $layanan = $layanan . " " . $c['layanan'];
            }
          }
        }
        foreach ($this->dDurasi as $c) {
          if ($a['id_durasi'] == $c['id_durasi']) {
            $durasi = $durasi . " " . $c['durasi'];
          }
        }

        foreach ($this->itemGroup as $c) {
          if ($a['id_item_group'] == $c['id_item_group']) {
            $kategori = $kategori . " " . $c['item_kategori'];
          }
        }
      }
    }
    $adaBayar = false;
    $historyBayar = array();
    foreach ($data['kas'] as $k) {
      if ($k['ref_transaksi'] == $id && $k['status_mutasi'] == 3) {
        array_push($historyBayar, $k['jumlah']);
      }
      if ($k['ref_transaksi'] == $id) {
        $adaBayar = true;
      }
    }

    $statusBayar = "";
    $totalBayar = array_sum($historyBayar);
    $showSisa = "";
    $sisa = $harga;
    $lunas = false;
    $enHapus = true;
    if ($totalBayar > 0) {
      $enHapus = false;
      if ($totalBayar >= $harga) {
        $lunas = true;
        $statusBayar = "<b><i class='fas fa-check-circle text-success'></i></b>";
        $sisa = 0;
      } else {
        $sisa = $harga - $totalBayar;
        $showSisa = "<b><i class='fas fa-exclamation-circle'></i> Sisa Rp" . number_format($sisa) . "</b>";
        $lunas = false;
      }
    } else {
      $lunas = false;
    }
    $buttonBayar = "<button data-ref='" . $id . "' data-harga='" . $sisa . "' data-idPelanggan='" . $pelanggan . "' class='btn badge badge-danger bayar' data-bs-toggle='modal' data-bs-target='#exampleModal2'>Bayar</button>";
    if ($lunas == true) {
      $buttonBayar = "";
    }

    $cs = "";
    foreach ($this->userMerge as $uM) {
      if ($uM['id_user'] == $id_user) {
        $cs = $uM['nama_user'];
      }
    }

    if ($enHapus == true || $this->id_privilege >= 100) {
      $buttonHapus = "<small><a href='" . $this->BASE_URL . "Member/bin/" . $id . "' data-ref='" . $id . "' class='hapusRef ml-4 text-danger'><i class='fas fa-trash-alt'></i></a></small> ";
    } else {
      $buttonHapus = "";
    }

    $modeNotifShow = "NONE";
    foreach ($this->pelanggan as $c) {
      if ($c['id_pelanggan'] == $pelanggan) {
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

    $buttonNotif = "<a href='#' data-hp='" . $no_pelanggan . "' data-mode='" . $modeNotif . "' data-ref='" . $id . "' data-time='" . $timeRef . "' class='text-dark sendNotif'><i class='far fa-paper-plane'></i> " . $modeNotifShow . "</a> <span id='notif" . $id . "'></span>";
    foreach ($data['notif'] as $notif) {
      if ($notif['no_ref'] == $id) {
        $buttonNotif = "<span>" . $modeNotifShow . " <i class='fas fa-check-circle text-success'></i></span>";
      }
    }

    $cabangKode = $this->dCabang['kode_cabang'];
  ?>

    <div class="col p-0 m-1 mb-0 rounded" style='max-width:400px;'>
      <div class="bg-white rounded">
        <table class="table table-sm w-100 pb-0 mb-0">
          <tbody>
            <span class="d-none" id="text<?= $id ?>">Deposit Member [<?= $cabangKode . "-" . $id ?>], Paket [M<?= $id_harga ?>]<?= $kategori ?><?= $layanan ?><?= $durasi ?>, <?= $z['qty'] . $unit; ?>, Berhasil. Total Rp<?= number_format($harga) ?>. Bayar Rp<?= number_format($totalBayar) ?>. laundry.mdl.my.id/I/m/<?= $this->id_laundry ?>/<?= $pelanggan ?>/<?= $id_harga ?></span>
            <tr>
              <td nowrap>
                <a href='#' class='mb-1 text-dark' onclick='Print("<?= $id ?>")'><i class='fas fa-print'></i></a>
                <?= "[" . $id . "] " . $z['insertTime'] ?>
                <small><span class='rounded border border-success pr-1 pl-1 buttonNotif'><?= $buttonNotif ?></span></small><br>
                <b>[M<?= $id_harga ?>]</b> <?= $kategori ?> * <?= $layanan ?> * <?= $durasi ?>
              </td>
              <td nowrap class="text-right">CS: <?= $cs ?><br><b><?= $z['qty'] . $unit ?></b></td>
            </tr>
            <tr>
              <td class="text-right">
                <?php if ($adaBayar == false || $this->id_privilege >= 100) { ?>
                  <span class="float-right"><?= $buttonHapus ?></span>
                <?php } ?>

                <?php if ($lunas == false) { ?>
                  <span class="float-left"><?= $buttonBayar ?></span>
                <?php } ?>
              </td>
              <td nowrap class="text-right"><span id="statusBayar<?= $id ?>"><?= $statusBayar ?></span>&nbsp;
                <span class="float-right"><?= $gPoinShow ?> <b>Rp<?= number_format($harga) ?></b></span>
              </td>
            </tr>
            <?php if ($adaBayar == true) { ?>
              <tr>
                <td colspan="2" align="right"><span id="historyBayar<?= $id ?>"><?= $showMutasi ?></span>
                  </span><span id="sisa<?= $id ?>" class="text-danger"><?= $showSisa ?></span></td>
              </tr>
            <?php
            }
            ?>
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
        <td style="margin: 0;">Deposit Paket Member <b>M<?= $id_harga ?></b><br><?= $kategori ?>, <?= $layanan ?>, <?= $durasi ?>, <?= $z['qty'] . $unit ?></td>
        <tr>
          <td colspan="2" style="border-bottom:1px dashed black;"></td>
        </tr>
        <tr>
          <td>
            Total
          </td>
          <td style="text-align: right;">
            <?= "Rp" . number_format($harga) ?>
          </td>
        </tr>
        <tr>
          <td>
            Bayar
          </td>
          <td style="text-align: right;">
            Rp<?= number_format($totalBayar) ?>
          </td>
        </tr>
        <tr>
          <td>
            Sisa
          </td>
          <td style="text-align: right;">
            Rp<?= number_format($sisa) ?>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="border-bottom:1px dashed black;"></td>
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

<form class="ajax" action="<?= $this->BASE_URL; ?>Member/bayar" method="POST">
  <div class="modal" id="exampleModal2">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pembayaran Deposit Member</h5>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Jumlah (Rp)</label>
                  <input type="number" name="maxBayar" class="form-control float jumlahBayar" id="exampleInputEmail1" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Bayar (Rp) <a class="btn badge badge-primary bayarPas">Bayar Pas (Click)</a></label>
                  <input type="number" name="f1" class="form-control dibayar" id="exampleInputEmail1" required>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Kembalian (Rp)</label>
                  <input type="number" class="form-control float kembalian" id="exampleInputEmail1" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Metode</label>
                  <select name="f4" class="form-control form-control-sm metodeBayar" style="width: 100%;" required>
                    <?php foreach ($this->dMetodeMutasi as $a) { ?>
                      <option value="<?= $a['id_metode_mutasi'] ?>"><?= $a['metode_mutasi'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Penerima</label>
                  <select name="f2" class="form-control form-control-sm tize" style="width: 100%;" required>
                    <option value="" selected disabled></option>
                    <optgroup label="<?= $this->dLaundry['nama_laundry'] ?> [<?= $this->dCabang['kode_cabang'] ?>]">
                      <?php foreach ($this->user as $a) { ?>
                        <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                      <?php } ?>
                    </optgroup>
                    <?php if (count($this->userCabang) > 0) { ?>
                      <optgroup label="----- Cabang Lain -----">
                        <?php foreach ($this->userCabang as $a) { ?>
                          <option id="<?= $a['id_user'] ?>" value="<?= $a['id_user'] ?>"><?= $a['id_user'] . "-" . strtoupper($a['nama_user']) ?></option>
                        <?php } ?>
                      </optgroup>
                    <?php } ?>
                  </select>
                  <input type="hidden" class="idItem" name="f3" value="" required>
                  <input type="hidden" class="idPelanggan" name="idPelanggan" value="" required>
                </div>
              </div>
            </div>
            <div class="row" id="nTunai">
              <div class="col-sm-12">
                <div class="form-group">
                  <div class="form-group">
                    <label for="exampleInputEmail1" class="text-danger">Catatan Non Tunai <small>(Contoh: BRI)</small></label>
                    <input type="text" name="noteBayar" maxlength="10" class="form-control border-danger" id="exampleInputEmail1" placeholder="" style="text-transform:uppercase">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-primary">Bayar</button>
        </div>
      </div>
    </div>
  </div>
</form>

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

  $("a.sendNotif").on('click', function(e) {
    e.preventDefault();
    var hpNya = $(this).attr('data-hp');
    var modeNya = $(this).attr('data-mode');
    var refNya = $(this).attr('data-ref');
    var timeNya = $(this).attr('data-time');
    var textNya = $("span#text" + refNya).html();
    $.ajax({
      url: '<?= $this->BASE_URL ?>Member/sendNotifDeposit',
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
        $("span#notif" + refNya).html("<i class='fas fa-check-circle text-success'></i>")
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
    $('div.tambahPaket').load("<?= $this->BASE_URL ?>Member/orderPaket/<?= $pelanggan ?>/" + id_harga);
  });

  $("button.bayar").on('click', function(e) {
    e.preventDefault();
    var refNya = $(this).attr('data-ref');
    var bayarNya = $(this).attr('data-harga');
    var id_pelanggan = $(this).attr('data-idPelanggan');
    $("input.idPelanggan").val(id_pelanggan);
    $("input.idItem").val(refNya);
    $("input.jumlahBayar").val(bayarNya);
    $("input.jumlahBayar").attr({
      'max': bayarNya
    });
  });

  $("a.bayarPas").on('click', function(e) {
    e.preventDefault();
    var jumlahPas = $("input.jumlahBayar").val();
    $("input.dibayar").val(jumlahPas);
    diBayar = $("input.dibayar").val();
  });

  $("input.dibayar").on("keyup change", function() {
    diBayar = 0;
    diBayar = $(this).val();
    var kembalian = $(this).val() - $('input.jumlahBayar').val()
    if (kembalian > 0) {
      $('input.kembalian').val(kembalian);
    } else {
      $('input.kembalian').val(0);
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