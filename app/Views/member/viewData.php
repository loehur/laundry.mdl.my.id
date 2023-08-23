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
    $buttonBayar = "<a href='#' data-ref='" . $id . "' data-harga='" . $sisa . "' data-idPelanggan='" . $id_pelanggan . "' class='bayarMember border border-danger pr-1 pl-1 rounded' data-bs-toggle='modal' data-bs-target='#exampleModal2'>Bayar</a>";
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
      $buttonHapus = "<small><a href='" . $this->BASE_URL . "Member/bin/" . $id . "/" . $id_pelanggan . "' class='hapusRef text-dark'><i class='fas fa-trash-alt'></i></a></small> ";
    } else {
      $buttonHapus = "";
    }

    foreach ($this->pelanggan as $c) {
      if ($c['id_pelanggan'] == $id_pelanggan) {
        $no_pelanggan = $c['nomor_pelanggan'];
      }
    }

    //BUTTON NOTIF MEMBER
    $buttonNotif = "<a href='#' data-hp='" . $no_pelanggan . "' data-ref='" . $id . "' data-time='" . $timeRef . "' class='text-dark sendNotifMember bg-white rounded col pl-2 pr-2'><i class='fab fa-whatsapp'></i></a> <span id='notif" . $id . "'></span>";
    foreach ($data['notif'] as $notif) {
      if ($notif['no_ref'] == $id) {
        $buttonNotif = "<span class='bg-white rounded col pl-2 pr-2'><i class='fab fa-whatsapp'></i></span> " . ucwords($notif['proses']);
      }
    }

    $cabangKode = $this->dCabang['kode_cabang'];
  ?>

    <div class="col p-0 m-1 mb-0 rounded" style='max-width:400px;'>
      <div class="bg-white rounded">
        <table class="table table-sm w-100 pb-0 mb-0">
          <tbody>
            <tr class="d-none">
              <td>
                <span class="d-none" id="text<?= $id ?>">Deposit Member [<?= $cabangKode . "-" . $id ?>], Paket [M<?= $id_harga ?>]<?= $kategori ?><?= $layanan ?><?= $durasi ?>, <?= $z['qty'] . $unit; ?>, Berhasil. Total Rp<?= number_format($harga) ?>. Bayar Rp<?= number_format($totalBayar) ?>. laundry.mdl.my.id/I/m/<?= $this->id_laundry ?>/<?= $id_pelanggan ?>/<?= $id_harga ?></span>
              </td>
            </tr>
            <tr class="table-info">
              <td><a href='#' class='ml-1' onclick='Print("<?= $id ?>")'><i class='text-dark fas fa-print'></i></a></td>
              <td><b><?= strtoupper($nama_pelanggan) ?></b></td>
              <td class="text-right">
                <small><span class='buttonNotif'><?= $buttonNotif ?></span></small>
                <small><span class='rounded bg-white border pr-1 pl-1 buttonNotif'>CS: <?= $cs ?></span></small>
              </td>
            </tr>

            <tr>
              <td class="text-center">
                <?php if ($adaBayar == false || $this->id_privilege >= 100) { ?>
                  <span><?= $buttonHapus ?></span>
                <?php } ?>
              </td>
              <td nowrap>
                <?= "#" . $id . " " ?> <?= $z['insertTime'] ?><br>
                <b>[M<?= $id_harga ?>]</b> <?= $kategori ?> * <?= $layanan ?> * <?= $durasi ?>
              </td>
              <td nowrap class="text-right"><br><b><?= $z['qty'] . $unit ?></b></td>
            </tr>
            <tr>
              <td></td>
              <td class="text-right">
                <?php if ($lunas == false) { ?>
                  <span class="float-left"><small><b><?= $buttonBayar ?></b></small></span>
                <?php } ?>
              </td>
              <td nowrap class="text-right"><span id="statusBayar<?= $id ?>"><?= $statusBayar ?></span>&nbsp;
                <span class="float-right"><?= $gPoinShow ?> <b>Rp<?= number_format($harga) ?></b></span>
              </td>
            </tr>
            <?php if ($adaBayar == true) { ?>
              <tr>
                <td></td>
                <td colspan="2" class="text-right"><span id="historyBayar<?= $id ?>"><?= $showMutasi ?></span>
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
                  <input type="number" name="maxBayar" class="form-control float jumlahBayarMember" id="exampleInputEmail1" readonly>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Saldo Tunai (Rp)</label>
                  <input type="number" value="<?= $data['saldoTunai'] ?>" name="saldoTunai" class="form-control float" id="exampleInputEmail1" style="background-color: lightgreen;" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Bayar (Rp) <a class="btn badge badge-primary bayarPasMember">Bayar Pas (Click)</a></label>
                  <input type="number" name="f1" class="form-control dibayarMember" id="exampleInputEmail1" required>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Kembalian (Rp)</label>
                  <input type="number" class="form-control float kembalianMember" id="exampleInputEmail1" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="exampleInputEmail1">Metode</label>
                  <select name="f4" class="form-control form-control-sm metodeBayar" style="width: 100%;" required>
                    <?php foreach ($this->dMetodeMutasi as $a) {
                      if ($data['saldoTunai'] <= 0 && $a['id_metode_mutasi'] == 3) {
                        continue;
                      }
                    ?>
                      <option value="<?= $a['id_metode_mutasi'] ?>"><?= $a['metode_mutasi'] ?> <?= ($a['id_metode_mutasi'] == 3) ? "[ Rp" . number_format($data['saldoTunai']) . " ]" : "" ?></option>
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
                  <input type="hidden" class="idItemMember" name="f3" value="" required>
                  <input type="hidden" class="idPelangganMember" name="idPelanggan" value="" required>
                </div>
              </div>
            </div>
            <div class="row" id="nTunai">
              <div class="col-sm-12">
                <div class="form-group">
                  <div class="form-group">
                    <label for="exampleInputEmail1" class="text-success">
                      <span class="nonTunaiMetod border rounded pr-1 pl-1" style="cursor: pointer;">QRIS</span>
                      <span class="nonTunaiMetod border rounded pr-1 pl-1" style="cursor: pointer;">BCA</span>
                      <span class="nonTunaiMetod border rounded pr-1 pl-1" style="cursor: pointer;">BRI</span>
                      <span class="nonTunaiMetod border rounded pr-1 pl-1" style="cursor: pointer;">MANDIRI</span>
                      <span class="nonTunaiMetod border rounded pr-1 pl-1" style="cursor: pointer;">BNI</span>
                      <span class="nonTunaiMetod border rounded pr-1 pl-1" style="cursor: pointer;">BSI</span>
                    </label>
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

  $("span.nonTunaiMetod").click(function() {
    $("input[name=noteBayar]").val($(this).html());
  })

  $("a.sendNotifMember").on('click', function(e) {
    e.preventDefault();
    var hpNya = $(this).attr('data-hp');
    var refNya = $(this).attr('data-ref');
    var timeNya = $(this).attr('data-time');
    var textNya = $("span#text" + refNya).html();
    $.ajax({
      url: '<?= $this->BASE_URL ?>Member/sendNotifDeposit',
      data: {
        hp: hpNya,
        text: textNya,
        ref: refNya,
        time: timeNya,
      },
      type: "POST",
      success: function() {
        $("span#notif" + refNya).hide();
        $("span#notif" + refNya).html("<i class='fas fa-circle'></i>")
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
    $('div.tambahPaket').load("<?= $this->BASE_URL ?>Member/orderPaket/<?= $id_pelanggan ?>/" + id_harga);
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