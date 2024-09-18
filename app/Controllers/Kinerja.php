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
      $operasi = array();
      $dataTanggal = array();
      $data_main = array();
      $data_terima = array();
      $data_kembali = array();

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

      //OPERASI
      $join_where = "operasi.id_penjualan = sale_" . $this->id_cabang . ".id_penjualan";
      $where = "sale_" . $this->id_cabang . ".bin = 0 AND operasi.insertTime LIKE '" . $date . "%'";
      $data_main = $this->db(1)->innerJoin1_where('operasi', 'sale_' . $this->id_cabang, $join_where, $where);

      //PENERIMAAN
      $cols = "id_user, id_cabang, COUNT(id_user) as terima";
      $where = "insertTime LIKE '" . $date . "%' GROUP BY id_user, id_cabang";
      $data_terima = $this->db(1)->get_cols_where('sale_' . $this->id_cabang, $cols, $where, 1);

      //PENGAMBILAN
      $cols = "id_user_ambil, id_cabang, COUNT(id_user_ambil) as kembali";
      $where = "tgl_ambil LIKE '" . $date . "%' GROUP BY id_user_ambil, id_cabang";
      $data_kembali = $this->db(1)->get_cols_where('sale_' . $this->id_cabang, $cols, $where, 1);

      //CABANG LAIN
      foreach (DBC::cabang_list_id as $cbi) {
         //OPERASI
         $join_where = "operasi.id_penjualan = sale_" . $cbi . ".id_penjualan";
         $where = "sale_" . $cbi . ".bin = 0 AND operasi.insertTime LIKE '" . $date . "%'";
         $data_lain = $this->db(1)->innerJoin1_where('operasi', 'sale_' . $cbi, $join_where, $where);
         foreach ($data_lain as $dl) {
            array_push($data_main, $dl);
         }

         //PENERIMAAN
         $cols = "id_user, id_cabang, COUNT(id_user) as terima";
         $where = "insertTime LIKE '" . $date . "%' GROUP BY id_user, id_cabang";
         $data_lain = $this->db(1)->get_cols_where('sale_' . $cbi, $cols, $where, 1);
         foreach ($data_lain as $dl) {
            array_push($data_terima, $dl);
         }

         //PENGAMBILAN
         $cols = "id_user_ambil, id_cabang, COUNT(id_user_ambil) as kembali";
         $where = "tgl_ambil LIKE '" . $date . "%' GROUP BY id_user_ambil, id_cabang";
         $data_lain = $this->db(1)->get_cols_where('sale_' . $cbi, $cols, $where, 1);
         foreach ($data_lain as $dl) {
            array_push($data_kembali, $dl);
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
