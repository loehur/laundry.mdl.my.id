<?php

class Kinerja extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function index($mode = 1)
   {
      $operasi = [];
      $dataTanggal = [];
      $data_main = [];
      $data_terima = [];
      $data_kembali = [];

      if ($mode == 1) {
         $data_operasi = ['title' => 'Kinerja Bulanan'];
         $view = "bulanan";
      } else {
         $data_operasi = ['title' => 'Kinerja Harian'];
         $view = "harian";
      }

      //KINERJA
      if (isset($_POST['m'])) {
         if ($mode == 1) {
            $date = $_POST['Y'] . "-" . $_POST['m'];
            $dataTanggal = array('bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
         } else {
            $date = $_POST['Y'] . "-" . $_POST['m'] . "-" . $_POST['d'];
            $dataTanggal = array('tanggal' => $_POST['d'], 'bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
         }
      } else {
         if ($mode == 1) {
            $date = date('Y-m');
         } else {
            $date = date('Y-m-d');
         }
      }

      foreach (URL::cabang_list_id as $cbi) {
         //OPERASI
         $join_where = "operasi.id_penjualan = sale_" . $cbi . ".id_penjualan";
         $where = "sale_" . $cbi . ".bin = 0 AND operasi.insertTime LIKE '" . $date . "%'";
         $data_lain1 = $this->db(1)->innerJoin1_where('operasi', 'sale_' . $cbi, $join_where, $where);
         foreach ($data_lain1 as $dl1) {
            array_push($data_main, $dl1);
         }

         //PENERIMAAN
         $cols = "id_user, id_cabang, COUNT(id_user) as terima";
         $where = "insertTime LIKE '" . $date . "%' GROUP BY id_user, id_cabang";
         $data_lain2 = $this->db(1)->get_cols_where('sale_' . $cbi, $cols, $where, 1);
         foreach ($data_lain2 as $dl2) {
            array_push($data_terima, $dl2);
         }

         //PENGAMBILAN
         $cols = "id_user_ambil, id_cabang, COUNT(id_user_ambil) as kembali";
         $where = "tgl_ambil LIKE '" . $date . "%' GROUP BY id_user_ambil, id_cabang";
         $data_lain3 = $this->db(1)->get_cols_where('sale_' . $cbi, $cols, $where, 1);
         foreach ($data_lain3 as $dl3) {
            array_push($data_kembali, $dl3);
         }
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('kinerja/' . $view, [
         'data_main' => $data_main,
         'operasi' => $operasi,
         'dataTanggal' => $dataTanggal,
         'dTerima' => $data_terima,
         'dKembali' => $data_kembali,
      ]);
   }
}
