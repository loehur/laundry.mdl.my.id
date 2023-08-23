<?php

class Operan extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'penjualan';
   }

   public function index()
   {
      $idOperan = "";
      $data_operasi = ['title' => 'Operan'];
      $viewData = 'operan/form';
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData, ['idOperan' => $idOperan]);
   }

   public function load($idOperan)
   {
      $operasi = array();
      $id_penjualan = $idOperan;
      $where = $this->wLaundry . " AND id_penjualan = " . $id_penjualan . " AND bin = 0 AND id_cabang <> " . $this->id_cabang;
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
      $idOperan = $id_penjualan;

      $numbers = array_column($data_main, 'id_penjualan');
      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wLaundry . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->model('M_DB_1')->get_where('operasi', $where);
      }

      $viewData = 'operan/content';
      $this->view($viewData, [
         'data_main' => $data_main,
         'operasi' => $operasi,
         'idOperan' => $idOperan
      ]);
   }

   public function operasiOperan()
   {

      $hp = $_POST['hp'];
      $text = $_POST['text'];
      $karyawan = $_POST['f1'];
      $penjualan = $_POST['f2'];
      $operasi = $_POST['f3'];
      $idCabang = $_POST['idCabang'];

      if ($idCabang == 0 || strlen($hp) == 0) {
         exit;
      };

      $cols = 'id_laundry, id_cabang, id_penjualan, jenis_operasi, id_user_operasi';
      $vals = $this->id_laundry . "," . $idCabang . "," . $penjualan . "," . $operasi . "," . $karyawan;
      $setOne = 'id_penjualan = ' . $penjualan . " AND jenis_operasi = " . $operasi;
      $where = "id_cabang = " . $idCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('operasi', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('operasi', $cols, $vals);
      }

      //INSERT NOTIF SELESAI TAPI NOT READY
      $time = date('Y-m-d H:i:s');
      $cols = 'insertTime, id_cabang, no_ref, phone, text, status, tipe';
      $vals = "'" . $time . "'," . $idCabang . "," . $penjualan . ",'" . $hp . "','" . $text . "',5,2";

      $setOne = "no_ref = '" . $penjualan . "' AND tipe = 2";
      $where = "id_cabang = " . $idCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
      }
   }
}
