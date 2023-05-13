<?php

class Broadcast extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }
   public function i($mode = 1)
   {

      $dateT = [];
      $dateF = [];
      $data = [];

      if (isset($_POST['d'])) {
         $dateFrom = $_POST['Y'] . "-" . $_POST['m'] . "-" . $_POST['d'];
         $dateTo = $_POST['Yt'] . "-" . $_POST['mt'] . "-" . $_POST['dt'];
         $dateF['d'] = $_POST['d'];
         $dateF['m'] = $_POST['m'];
         $dateF['Y'] = $_POST['Y'];
         $dateT['d'] = $_POST['dt'];
         $dateT['m'] = $_POST['mt'];
         $dateT['Y'] = $_POST['Yt'];
      }

      if ($mode == 1) {
         $data_operasi = ['title' => 'Broadcast Dalam Proses'];
         if (isset($_POST['d'])) {
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(insertTime) >= '" . $dateFrom . "' AND DATE(insertTime) <= '" . $dateTo . "' ORDER BY id_penjualan DESC";
            $data = $this->model('M_DB_1')->get_where('penjualan', $where);
         }
      } else {
         $data_operasi = ['title' => 'Broadcast Non Proses'];
         if (isset($_POST['d'])) {
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 1 AND insertTime >= '" . $dateFrom . "' AND insertTime <= '" . $dateFrom . "' ORDER BY id_penjualan DESC";
            $data = $this->model('M_DB_1')->get_where('penjualan', $where);
         }
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('broadcast/main', ['data' => $data, 'mode' => $mode, 'dateF' => $dateF, 'dateT' => $dateT]);
   }

   public function insert($mode = 1)
   {
      $text = $_POST['text'];
      $time = date('Y-m-d H:i:s');
      $ref = date('Ymd_His') . "_" . $mode;
      $cols = 'insertTime, notif_token, id_cabang, no_ref, phone, text, mode, status, tipe';

      $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) <= (insertTime + INTERVAL 11 DAY) ORDER BY id_penjualan DESC";
      $data_main = $this->model('M_DB_1')->get_where("penjualan", $where);

      $no_pelanggan = "";
      foreach ($data_main as $a) {
         foreach ($this->pelanggan as $dp) {
            if ($dp['id_pelanggan'] == $a['id_pelanggan']) {
               $no_pelanggan = $dp['no_pelanggan'];
               $mode = $_POST['mode'];
               break;
            }
         }

         $vals = "'" . $time . "','" . $this->dLaundry['notif_token'] . "'," . $this->id_cabang . "," . $ref . ",'" . $no_pelanggan . "','" . $text . "'," . $mode . ",1,5";
         $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
      }
   }
}
