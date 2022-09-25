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
         'notif_member' => $notif_member

      ]);
   }

   public function bayarMulti($karyawan, $idPelanggan, $metode, $note)
   {
      $data = $_POST['rekap'][0];
      if (count($data) == 0) {
         exit();
      }

      $note = str_replace("_SPACE_", " ", $note);

      foreach ($data as $key => $value) {
         $xNoref = $key;
         $jumlah = $value;
         $ref = substr($xNoref, 2);
         $tipe = substr($xNoref, 0, 1);

         switch ($tipe) {
            case "U":
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
               $vals = $this->id_cabang . ", 1, 1,'" . $ref . "'," . $metode . ",'" . $note . "'," . $status_mutasi . "," . $jumlah . "," . $karyawan . "," . $idPelanggan;

               $setOne = 'ref_transaksi = ' . $ref . ' AND jumlah = ' . $jumlah;
               $where = $this->wCabang . " AND " . $setOne;
               $data_main = $this->model('M_DB_1')->count_where('kas', $where);
               if ($data_main < 1) {
                  $this->model('M_DB_1')->insertCols('kas', $cols, $vals);
               }
               break;
            case "M";
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
               break;
         }
      }
   }
}
