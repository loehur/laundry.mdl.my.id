<?php

class Member extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function tambah_paket($get_pelanggan)
   {
      if (isset($get_pelanggan)) {
         $pelanggan = $get_pelanggan;
      } elseif (isset($_POST['p'])) {
         $pelanggan = $_POST['p'];
      } else {
         $pelanggan = 0;
      }

      $this->tampilkanMenu($pelanggan);
   }

   public function tampilkanMenu($pelanggan)
   {
      $view = 'member/memberMenu';
      $data_operasi = ['title' => '(+) Paket Member'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_operasi' => $data_operasi, 'pelanggan' => $pelanggan]);
   }

   public function tampilkan($pelanggan)
   {
      $viewData = 'member/viewData';
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $pelanggan;
      $order = "id_member DESC LIMIT 12";
      $data_manual = $this->model('M_DB_1')->get_where_order('member', $where, $order);
      $notif = array();

      $kas = array();
      if (count($data_manual) > 0) {
         $numbers = array_column($data_manual, 'id_member');
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wCabang . " AND jenis_transaksi = 3 AND (ref_transaksi BETWEEN " . $min . " AND " . $max . ")";
         $kas = $this->model('M_DB_1')->get_where('kas', $where);

         $where = $this->wCabang . " AND tipe = 3 AND no_ref BETWEEN " . $min . " AND " . $max;
         $cols = 'no_ref';
         $notif = $this->model('M_DB_1')->get_cols_where('notif', $cols, $where, 1);
      }

      $this->view($viewData, [
         'data_manual' => $data_manual,
         'pelanggan' => $pelanggan,
         'kas' => $kas,
         'notif' => $notif
      ]);
   }

   public function tampil_rekap()
   {
      $data_operasi = ['title' => 'List Member'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $viewData = 'member/viewRekap';
      $where = $this->wCabang . " AND bin = 0 GROUP BY id_pelanggan, id_harga ORDER BY saldo DESC";
      $cols = "id_pelanggan, id_harga, SUM(qty) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('member', $cols, $where, 1);
      $pakai = array();

      foreach ($data as $a) {
         $idPelanggan = $a['id_pelanggan'];
         $idHarga = $a['id_harga'];
         $saldoPengurangan = 0;
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin  = 0";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->model('M_DB_1')->get_cols_where('penjualan', $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idPelanggan . $idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idPelanggan . $idHarga] = 0;
         }
      }

      $this->view($viewData, ['data' => $data, 'pakai' => $pakai]);
   }

   public function rekapTunggal($pelanggan)
   {
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $pelanggan . " GROUP BY id_harga ORDER BY saldo DESC";
      $cols = "id_pelanggan, id_harga, SUM(qty) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('member', $cols, $where, 1);
      $pakai = array();

      foreach ($data as $a) {
         $idPelanggan = $a['id_pelanggan'];
         $idHarga = $a['id_harga'];
         $saldoPengurangan = 0;
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin  = 0";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->model('M_DB_1')->get_cols_where('penjualan', $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idPelanggan . $idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idPelanggan . $idHarga] = 0;
         }
      }

      $viewData = 'member/viewRekap';
      $this->view($viewData, ['data' => $data, 'pakai' => $pakai, 'id_pelanggan' => $pelanggan]);
   }

   public function restoreRef()
   {
      $id = $_POST['id'];
      $setOne = "id_member = '" . $id . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 0";
      $this->model('M_DB_1')->update("member", $set, $where);
   }

   public function orderPaket($pelanggan, $id_harga)
   {
      if ($id_harga <> 0) {
         $where = $this->wLaundry . " AND id_harga = " . $id_harga;
      } else {
         $where = $this->wLaundry;
      }
      $data['main'] = $this->model('M_DB_1')->get_where('harga_paket', $where);
      $data['pelanggan'] = $pelanggan;
      $this->view('member/formOrder', $data);
   }

   public function deposit($id_pelanggan)
   {
      $id_harga_paket = $_POST['f1'];
      $id_user = $_POST['f2'];
      $where = $this->wLaundry . " AND id_harga_paket = " . $id_harga_paket;
      $data = $this->model('M_DB_1')->get_where_row('harga_paket', $where);
      $id_harga = $data['id_harga'];
      $qty = $data['qty'];
      $harga = $data['harga'];

      foreach ($this->harga as $a) {
         if ($a['id_harga'] == $id_harga) {
            $penjualan_jenis = $a['id_penjualan_jenis'];
         }
      }

      $id_poin = 0;
      $per_poin = 0;
      foreach ($this->setPoin as $a) {
         if (strpos($a['list_penjualan_jenis'], '"' . $penjualan_jenis . '"') !== FALSE) {
            $id_poin = $a['id_poin_set'];
            $per_poin = $a['per_poin'];
         }
      }

      $cols = 'id_cabang, id_pelanggan, id_harga, qty, harga, id_user, id_poin, per_poin';
      $vals = $this->id_cabang . "," . $id_pelanggan . "," . $id_harga . "," . $qty . "," . $harga . "," . $id_user . "," . $id_poin . "," . $per_poin;

      $today = date('Y-m-d');
      $setOne = "id_pelanggan = '" . $id_pelanggan . "' AND id_harga = " . $id_harga . " AND qty = " . $qty . " AND insertTime LIKE '" . $today . "%'";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where("member", $where);

      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols("member", $cols, $vals);
      }
      $this->tambah_paket($id_pelanggan);
   }

   public function cekRekap($idPelanggan)
   {
      $viewData = 'penjualan/viewMember';
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga";
      $cols = "id_harga, SUM(qty) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('member', $cols, $where, 1);
      $pakai = array();

      foreach ($data as $a) {
         $saldoPengurangan = 0;
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND bin = 0 AND id_harga = " . $idHarga . " AND member = 1";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->model('M_DB_1')->get_cols_where('penjualan', $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idHarga] = 0;
         }
      }

      $this->view($viewData, ['data' => $data, 'pakai' => $pakai]);
   }

   public function textSaldo()
   {
      $idPelanggan = $_POST['id'];
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga";
      $cols = "id_harga, SUM(qty) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('member', $cols, $where, 1);

      foreach ($data as $a) {
         $saldoPengurangan = 0;
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin = 0";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->model('M_DB_1')->get_cols_where('penjualan', $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idHarga] = 0;
         }
      } ?>

      <table style="width: 100%; margin:0; font-size:x-small; padding:0; border-collapse: collapse;">
         <?php
         foreach ($data as $z) {
            $id = $z['id_harga'];
            $unit = "";
            if ($z['saldo'] > 0) {
               foreach ($this->harga as $a) {
                  if ($a['id_harga'] == $id) {
                     foreach ($this->dPenjualan as $dp) {
                        if ($dp['id_penjualan_jenis'] == $a['id_penjualan_jenis']) {
                           foreach ($this->dSatuan as $ds) {
                              if ($ds['id_satuan'] == $dp['id_satuan']) {
                                 $unit = $ds['nama_satuan'];
                              }
                           }
                        }
                     }
                     $saldoAwal = $z['saldo'];
                     $saldoAkhir = $saldoAwal - $pakai[$id];
                  }
               }
            }
         ?>
            <tr>
               <td style="padding:0; margin:0">
                  M<?= $id ?>
               </td>
               <td style="text-align: right; padding:0; margin:0">
                  <?= number_format($saldoAkhir, 2) . $unit ?>
               </td>
            </tr>
   <?php
         }
         echo "</table>";
      }

      public function bayar()
      {
         $maxBayar = $_POST['maxBayar'];
         $jumlah = $_POST['f1'];

         if ($jumlah > $maxBayar) {
            $jumlah = $maxBayar;
         }

         $karyawan = $_POST['f2'];
         $ref = $_POST['f3'];
         $metode = $_POST['f4'];
         $idPelanggan = $_POST['idPelanggan'];
         $note = $_POST['noteBayar'];

         if (strlen($note) == 0 && $metode == 2) {
            $note = "Non_Tunai";
         }

         $status_mutasi = 3;
         switch ($metode) {
            case "2":
               $status_mutasi = 2;
               break;
            default:
               $status_mutasi = 3;
               break;
         }

         if ($this->id_privilege == 100 || $this->id_privilege == 101) {
            $status_mutasi = 3;
         }

         $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, ref_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client';
         $vals = $this->id_cabang . ", 1, 3,'" . $ref . "'," . $metode . ",'" . $note . "'," . $status_mutasi . "," . $jumlah . "," . $karyawan . "," . $idPelanggan;

         $setOne = "ref_transaksi = " . $ref . " AND jumlah = " . $jumlah;
         $where = $this->wCabang . " AND " . $setOne;
         $data_main = $this->model('M_DB_1')->count_where('kas', $where);
         if ($data_main < 1) {
            $this->model('M_DB_1')->insertCols('kas', $cols, $vals);
         }
      }

      public function bin($id, $id_pelanggan)
      {

         $set = "bin = 1";
         $setOne = "id_member = '" . $id . "'";
         $where = $this->wCabang . " AND " . $setOne;
         $this->model('M_DB_1')->update("member", $set, $where);
         header("Location:../../tambah_paket/" . $id_pelanggan);
      }

      public function sendNotifDeposit()
      {
         $hp = $_POST['hp'];
         $mode = $_POST['mode'];
         $noref = $_POST['ref'];
         $time =  $_POST['time'];
         $text = $_POST['text'];
         $text = str_replace("<sup>2</sup>", "²", $text);
         $text = str_replace("<sup>3</sup>", "³", $text);

         $cols =  'insertTime, notif_token, id_cabang, no_ref, phone, text, mode, tipe';
         $vals = "'" . $time . "','" . $this->dLaundry['notif_token'] . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "'," . $mode . ",3";

         $setOne = "no_ref = '" . $noref . "' AND tipe = 3";
         $where = $this->wCabang . " AND " . $setOne;
         $data_main = $this->model('M_DB_1')->count_where('notif', $where);
         if ($data_main < 1) {
            $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
         }
      }
   }
