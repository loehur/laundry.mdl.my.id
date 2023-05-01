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
      if ($mode == 1) {
         $data_operasi = ['title' => 'Broadcast TDP'];
      }

      $table = 'cabang';
      $where = "cabang." . $this->wLaundry;
      $data_cabang = $this->model('M_DB_1')->get_where($table, $where);
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('broadcast/main', ['data_cabang' => $data_cabang, 'mode' => $mode]);
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
