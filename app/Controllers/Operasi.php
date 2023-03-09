<?php

class Operasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'penjualan';
   }

   public function i($modeOperasi, $getPelanggan, $getTahun)
   {
      switch ($modeOperasi) {
         case 1:
            //DALAM PROSES
            $data_operasi = ['title' => 'Operasi Order Proses'];
            $viewData = 'operasi/form_proses';
            $formData = array('id_pelanggan' => $getPelanggan);
            break;
         case 2:
            //TUNTAS
            $data_operasi = ['title' => 'Operasi Order Tuntas'];
            $viewData = 'operasi/form_tuntas';
            $formData = array('tahun' => $getTahun, 'id_pelanggan' => $getPelanggan);
            break;
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData, ['formData' => $formData]);
   }

   public function loadData($getPelanggan, $getTahun)
   {
      $operasi = array();
      $kas = array();
      $notif = array();
      $notifPenjualan = array();
      $formData = array();
      $data_main = array();
      $idOperan = "";
      $surcas = array();
      $modeView = 1;

      $thisMonth = $getTahun;
      $pelanggan = $getPelanggan;

      if ($getTahun <> 0) {
         $where = $this->wCabang . " AND id_pelanggan = $pelanggan AND bin = 0 AND tuntas = 1 AND insertTime LIKE '" . $thisMonth . "%' ORDER BY id_penjualan DESC";
         $modeView = 2;
      } else {
         $where = $this->wCabang . " AND id_pelanggan = $pelanggan AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      }
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);

      $viewData = 'operasi/view_load';
      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_column($data_main, 'no_ref');

      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wLaundry . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->model('M_DB_1')->get_where('operasi', $where);

         //NOTIF SELESAI
         $where = $this->wCabang . " AND tipe = 2 AND no_ref BETWEEN " . $min . " AND " . $max;
         $notifPenjualan = $this->model('M_DB_1')->get_where('notif', $where);
      }
      if (count($refs) > 0) {
         //KAS
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = $this->wCabang . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $kas = $this->model('M_DB_1')->get_where('kas', $where);

         //NOTIF BON
         $where = $this->wCabang . " AND tipe = 1 AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $notif = $this->model('M_DB_1')->get_where('notif', $where);

         //SURCAS
         $where = $this->wCabang . " AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $surcas = $this->model('M_DB_1')->get_where('surcas', $where);
      }

      //MEMBER
      $data_member = array();
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $pelanggan;
      $order = "id_member DESC LIMIT 12";
      $data_member = $this->model('M_DB_1')->get_where_order('member', $where, $order);
      $notif_member = array();

      $kas_member = array();
      if (count($data_member) > 0) {
         $numbers = array_column($data_member, 'id_member');
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wCabang . " AND jenis_transaksi = 3 AND (ref_transaksi BETWEEN " . $min . " AND " . $max . ")";
         $kas_member = $this->model('M_DB_1')->get_where('kas', $where);

         $where = $this->wCabang . " AND tipe = 3 AND no_ref BETWEEN " . $min . " AND " . $max;
         $cols = 'no_ref';
         $notif_member = $this->model('M_DB_1')->get_cols_where('notif', $cols, $where, 1);
      }

      //SALDO TUNAI
      $sisaSaldo = $this->getSaldoTunai($pelanggan);

      $this->view($viewData, [
         'modeView' => $modeView,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'notif' => $notif,
         'notif_penjualan' => $notifPenjualan,
         'formData' => $formData,
         'idOperan' => $idOperan,
         'surcas' => $surcas,
         'pelanggan' => $pelanggan,
         'data_member' => $data_member,
         'pelanggan' => $pelanggan,
         'kas_member' => $kas_member,
         'notif_member' => $notif_member,
         'saldoTunai' => $sisaSaldo
      ]);
   }

   function getSaldoTunai($pelanggan)
   {
      //SALDO TUNAI
      $saldo = 0;
      $pakai = 0;

      //Kredit
      $where = $this->wCabang . " AND id_client = " . $pelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 AND status_mutasi = 3 GROUP BY id_client ORDER BY saldo DESC";
      $cols = "id_client, SUM(jumlah) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 1);

      //Debit
      if (count($data) > 0) {
         foreach ($data as $a) {
            $idPelanggan = $a['id_client'];
            $saldo = $a['saldo'];
            $where = $this->wCabang . " AND id_client = " . $idPelanggan . " AND metode_mutasi = 3 AND jenis_mutasi = 2";
            $cols = "SUM(jumlah) as pakai";
            $data2 = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 0);
            if (isset($data2['pakai'])) {
               $pakai = $data2['pakai'];
            }
         }
      }

      $sisaSaldo = $saldo - $pakai;
      return $sisaSaldo;
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

      if (strlen($note) == 0) {
         switch ($metode) {
            case 2:
               $note = "Non_Tunai";
               break;
            case 3:
               $note = "Saldo_Tunai";
               break;
            default:
               $note = "";
               break;
         }
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

      $jenis_mutasi = 1;
      if ($metode == 3) {
         $sisaSaldo = $this->getSaldoTunai($idPelanggan);
         if ($jumlah > $sisaSaldo) {
            $jumlah = $sisaSaldo;
         }
         $jenis_mutasi = 2;
      }

      if ($jumlah <= 0) {
         exit();
      }

      $today = date('Y-m-d');

      $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, ref_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client';
      $vals = $this->id_cabang . ", " . $jenis_mutasi . ", 1,'" . $ref . "'," . $metode . ",'" . $note . "'," . $status_mutasi . "," . $jumlah . "," . $karyawan . "," . $idPelanggan;

      $setOne = 'ref_transaksi = ' . $ref . ' AND jumlah = ' . $jumlah . " AND insertTime LIKE '" . $today . "%'";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('kas', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('kas', $cols, $vals);
      }
   }

   public function bayarMulti($karyawan, $idPelanggan, $metode, $note)
   {
      $today = date('Y-m-d');

      $data = $_POST['rekap'][0];
      if (count($data) == 0) {
         exit();
      }

      $dibayar = $_POST['dibayar'];
      $note = str_replace("_SPACE_", " ", $note);

      if (strlen($note) == 0) {
         switch ($metode) {
            case 2:
               $note = "Non_Tunai";
               break;
            case 3:
               $note = "Saldo_Tunai";
               break;
            default:
               $note = "";
               break;
         }
      }

      ksort($data);
      foreach ($data as $key => $value) {
         if ($dibayar == 0) {
            exit();
         }

         $xNoref = $key;
         $jumlah = $value;
         $ref = substr($xNoref, 2);
         $tipe = substr($xNoref, 0, 1);

         if ($dibayar < $jumlah) {
            $jumlah = $dibayar;
         }

         $jenis_mutasi = 1;
         if ($metode == 3) {
            $sisaSaldo = $this->getSaldoTunai($idPelanggan);
            if ($sisaSaldo > 0) {
               if ($jumlah > $sisaSaldo) {
                  $jumlah = $sisaSaldo;
               }
            } else {
               exit();
            }
            $jenis_mutasi = 2;
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

         switch ($tipe) {
            case "U":
               $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, ref_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client';
               $vals = $this->id_cabang . ", " . $jenis_mutasi . ", 1,'" . $ref . "'," . $metode . ",'" . $note . "'," . $status_mutasi . "," . $jumlah . "," . $karyawan . "," . $idPelanggan;

               $setOne = 'ref_transaksi = ' . $ref . ' AND jumlah = ' . $jumlah . " AND insertTime LIKE '" . $today . "%'";
               $where = $this->wCabang . " AND " . $setOne;
               $data_main = $this->model('M_DB_1')->count_where('kas', $where);
               if ($data_main < 1) {
                  $this->model('M_DB_1')->insertCols('kas', $cols, $vals);
                  $dibayar -= $jumlah;
               }
               break;
            case "M":
               $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, ref_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client';
               $vals = $this->id_cabang . ", " . $jenis_mutasi . ", 3,'" . $ref . "'," . $metode . ",'" . $note . "'," . $status_mutasi . "," . $jumlah . "," . $karyawan . "," . $idPelanggan;

               $setOne = "ref_transaksi = " . $ref . " AND jumlah = " . $jumlah . " AND insertTime LIKE '" . $today . "%'";
               $where = $this->wCabang . " AND " . $setOne;
               $data_main = $this->model('M_DB_1')->count_where('kas', $where);
               if ($data_main < 1) {
                  $this->model('M_DB_1')->insertCols('kas', $cols, $vals);
                  $dibayar -= $jumlah;
               }
               break;
         }
      }
   }
}
