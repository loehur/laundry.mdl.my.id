<?php

class SaldoTunai extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function tampil_rekap($all = true, $id_client = 0)
   {
      $data_operasi = ['title' => 'List Deposit Tunai'];
      $viewData = 'saldoTunai/viewRekap';

      if ($all == true) {
         $this->view('layout', ['data_operasi' => $data_operasi]);
         $where = $this->wCabang . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 GROUP BY id_client ORDER BY saldo DESC";
      } else {
         $where = $this->wCabang . " AND id_client = " . $id_client . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 GROUP BY id_client ORDER BY saldo DESC";
      }

      $cols = "id_client, SUM(jumlah) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 1);

      $saldo = [];
      $pakai = [];

      foreach ($data as $a) {
         $idPelanggan = $a['id_client'];
         $saldo[$idPelanggan] = $a['saldo'];
         $where = $this->wCabang . " AND id_client = " . $idPelanggan . " AND metode_mutasi = 3 AND jenis_mutasi = 2";
         $cols = "SUM(jumlah) as pakai";
         $data2 = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 0);
         if (isset($data2['pakai'])) {
            $saldoPengurangan = $data2['pakai'];
            $pakai[$idPelanggan] = $saldoPengurangan;
         } else {
            $pakai[$idPelanggan] = 0;
         }
      }

      $this->view($viewData, ['saldo' => $saldo, 'pakai' => $pakai, 'client' => $id_client]);
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

   public function tampilkan($id_client)
   {
      $viewData = 'saldoTunai/viewData';
      $where = $this->wCabang . " AND id_client = " . $id_client . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 ORDER BY id_kas DESC";
      $cols = "id_kas, id_client, id_user, jumlah, metode_mutasi, note, insertTime";
      $data = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 1);
      $notif = array();

      if (count($data) > 0) {
         $numbers = array_column($data, 'id_kas');
         $min = min($numbers);
         $max = max($numbers);

         $where = $this->wCabang . " AND tipe = 4 AND no_ref BETWEEN " . $min . " AND " . $max;
         $cols = 'no_ref, status';
         $notif = $this->model('M_DB_1')->get_cols_where('notif', $cols, $where, 1);
      }

      $this->view($viewData, [
         'data_' => $data,
         'pelanggan' => $id_client,
         'notif' => $notif
      ]);
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
      $setOne = "id_client = '" . $id_pelanggan . "' AND jumlah = " . $jumlah . " AND jenis_transaksi = 6 AND insertTime LIKE '" . $today . "%'";
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

      $cols =  'insertTime, notif_token, id_cabang, no_ref, phone, text, mode, tipe';
      $vals = "'" . $time . "','" . $this->dLaundry['notif_token'] . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "'," . $mode . ",4";

      $setOne = "no_ref = '" . $noref . "' AND tipe = 4";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
      }
   }
}
