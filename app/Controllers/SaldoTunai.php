<?php

class SaldoTunai extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function tampil_rekap()
   {
      $data_operasi = ['title' => 'List Deposit Tunai'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $viewData = 'saldoTunai/viewRekap';
      $where = $this->wCabang . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 GROUP BY id_client ORDER BY saldo DESC";
      $cols = "id_client, SUM(jumlah) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 1);

      $saldo = [];
      $pakai = [];

      foreach ($data as $a) {
         $idPelanggan = $a['id_client'];
         $saldo[$idPelanggan] = $a['saldo'];
         $where = $this->wCabang . " AND id_client = " . $idPelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 2";
         $cols = "SUM(total) as pakai";
         $data2 = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 0);

         if (isset($data2['pakai'])) {
            $saldoPengurangan = $data2['pakai'];
            $pakai[$idPelanggan] = $saldoPengurangan;
         } else {
            $pakai[$idPelanggan] = 0;
         }
      }

      $this->view($viewData, ['saldo' => $saldo, 'pakai' => $pakai]);
   }

   public function tambah($get_pelanggan = 0)
   {
      if ($get_pelanggan <> 0) {
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
      $view = 'saldoTunai/memberMenu';
      $data_operasi = ['title' => '(+) Deposit Tunai'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_operasi' => $data_operasi, 'pelanggan' => $pelanggan]);
   }

   public function tampilkan($pelanggan)
   {
      $viewData = 'saldoTunai/viewData';
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

      $viewData = 'saldoTunai/viewRekap';
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
      $this->view('saldoTunai/formOrder', $data);
   }

   public function deposit($id_pelanggan)
   {
      $jumlah = $_POST['jumlah'];
      $id_user = $_POST['staf'];
      $metode = $_POST['metode'];
      $note = $_POST['noteBayar'];

      $today = date('Y-m-d');
      $setOne = "id_client = '" . $id_pelanggan . "' AND jumlah = " . $jumlah . " AND insertTime LIKE '" . $today . "%'";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where("kas", $where);

      $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client';
      $vals = $this->id_cabang . ", 1, 6," . $metode . ",'" . $note . "',3," . $jumlah . "," . $id_user . "," . $id_pelanggan;

      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols("kas", $cols, $vals);
      }
      $this->tambah($id_pelanggan);
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
