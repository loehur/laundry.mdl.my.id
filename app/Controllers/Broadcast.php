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
         $data_operasi = ['title' => 'Broadcast PDP', 'vLaundry' => true];
         if (isset($_POST['d'])) {
            $where = $this->wLaundry . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(insertTime) >= '" . $dateFrom . "' AND DATE(insertTime) <= '" . $dateTo . "' ORDER BY id_penjualan DESC";
            $data = $this->model('M_DB_1')->get_where('penjualan', $where);
         }
      } elseif ($mode == 3) {
         $data_operasi = ['title' => 'Broadcast Semua Pelanggan', 'vLaundry' => true];
         if (isset($_POST['d'])) {
            $where = $this->wLaundry . " AND id_pelanggan <> 0 AND bin = 0 AND DATE(insertTime) >= '" . $dateFrom . "' AND DATE(insertTime) <= '" . $dateTo . "' ORDER BY id_penjualan DESC";
            $data = $this->model('M_DB_1')->get_where('penjualan', $where);
         }
      } else {
         $data_operasi = ['title' => 'Broadcast PNP', 'vLaundry' => true];
         if (isset($_POST['d'])) {
            $where = $this->wLaundry . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 1 AND DATE(insertTime) >= '" . $dateFrom . "' AND DATE(insertTime) <= '" . $dateTo . "' ORDER BY id_penjualan DESC";
            $data = $this->model('M_DB_1')->get_where('penjualan', $where);
         }
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('broadcast/main', ['data' => $data, 'mode' => $mode, 'dateF' => $dateF, 'dateT' => $dateT]);
   }

   public function insert()
   {
      $text_ori = $_POST['text'];
      $broad = json_decode($_POST['broad'], JSON_PRETTY_PRINT);
      $cols = 'insertTime, notif_token, id_cabang, no_ref, phone, text, mode, status, tipe';

      foreach ($broad as $k => $v) {
         $text = $text_ori . " [" . $k . "]";
         $time = date('Y-m-d H:i:s');
         $ref = $k;
         $no = $v['no'];
         $cab = $v['cab'];
         $mode_notif = $v['mode'];
         $vals = "'" . $time . "','" . $this->dLaundry['notif_token'] . "'," . $cab . ",'" . $ref . "','" . $no . "','" . $text . "'," . $mode_notif . ",1,5";
         $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
      }
   }
}
