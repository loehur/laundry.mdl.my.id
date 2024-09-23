<?php

class Operan extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function index()
   {
      $idOperan = "";
      $idCabang = "";
      $data_operasi = ['title' => 'Operan'];
      $viewData = 'operan/form';
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData, ['idOperan' => $idOperan, 'idCabang' => $idCabang]);
   }

   public function load($idOperan, $idCabang)
   {
      if (strlen($idOperan) < 3) {
         echo "<div class='card py-3 px-3 mx-3'>";
         echo "Minimal 3 Digit";
         echo "</div>";
         exit();
      }

      $operasi = array();
      $id_penjualan = $idOperan;
      $where = "id_penjualan LIKE '%" . $id_penjualan . "' AND tuntas = 0 AND bin = 0 AND id_cabang = " . $idCabang;
      $data_main = $this->db(1)->get_where('sale_' . $idCabang, $where);
      $idOperan = $id_penjualan;

      if (count($data_main) == 0) {
         echo "Data tidak ditemukan";
         exit();
      }

      $numbers = array_column($data_main, 'id_penjualan');
      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);
         $where = "id_cabang = " . $idCabang . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->db(1)->get_where('operasi', $where);
      }

      $viewData = 'operan/content';
      $this->view($viewData, [
         'data_main' => $data_main,
         'operasi' => $operasi,
         'idOperan' => $idOperan,
         'idCabang' => $idCabang
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

      $pack = $_POST['pack'];
      $hanger = $_POST['hanger'];

      if ($idCabang == 0 || strlen($hp) == 0) {
         echo "ID Cabang atau No HP Pelanggan Error";
         exit();
      };

      $cols = 'id_cabang, id_penjualan, jenis_operasi, id_user_operasi, insertTime';
      $vals = $idCabang . "," . $penjualan . "," . $operasi . "," . $karyawan . ", '" . $GLOBALS['now'] . "'";
      $setOne = 'id_penjualan = ' . $penjualan . " AND jenis_operasi = " . $operasi;
      $where = "id_cabang = " . $idCabang . " AND " . $setOne;
      $data_main = $this->db(1)->count_where('operasi', $where);
      if ($data_main < 1) {
         $in = $this->db(1)->insertCols('operasi', $cols, $vals);
         if ($in['errno'] <> 0) {
            echo $in['error'];
            exit();
         }

         $set = "pack = " . $pack . ", hanger = " . $hanger;
         $where = "id_cabang = " . $idCabang . " AND id_penjualan = " . $penjualan;
         $up = $this->db(1)->update('sale_' . $idCabang, $set, $where);
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         }
      }

      //INSERT NOTIF SELESAI TAPI NOT READY
      $time = date('Y-m-d H:i:s');
      $cols = 'insertTime, id_cabang, no_ref, phone, text, status, tipe, token';
      $vals = "'" . $time . "'," . $idCabang . "," . $penjualan . ",'" . $hp . "','" . $text . "',5,2,'" . URL::WA_TOKEN . "'";

      $setOne = "no_ref = '" . $penjualan . "' AND tipe = 2";
      $where = "id_cabang = " . $idCabang . " AND " . $setOne;
      $data_main = $this->db(1)->count_where('notif_' . $idCabang, $where);
      if ($data_main < 1) {
         $in = $this->db(1)->insertCols('notif_' . $idCabang, $cols, $vals);
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         }
      }
   }
}
