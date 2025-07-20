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

      if ($idCabang == $_SESSION[URL::SESSID]['user']['id_cabang']) {
         echo "ID Outlet Operan harus berbeda dengan ID Outlet saat ini";
         exit();
      }

      if (strlen($idOperan) < 3) {
         echo "<div class='card py-3 px-3 mx-3'>";
         echo "Minimal 3 Digit";
         echo "</div>";
         exit();
      }

      $id_penjualan = $idOperan;
      $where = "id_penjualan LIKE '%" . $id_penjualan . "' AND tuntas = 0 AND bin = 0 AND id_cabang = " . $idCabang;
      $data_main = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where('sale', $where);
      $idOperan = $id_penjualan;

      if (count($data_main) == 0) {
         echo "Data tidak ditemukan";
         exit();
      }

      $numbers = array_column($data_main, 'id_penjualan');

      $operasi = [];
      foreach ($numbers as $id) {

         //OPERASI
         $where = "id_cabang = " . $idCabang . " AND id_penjualan = " . $id;
         $ops = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where('operasi', $where);
         if (count($ops) > 0) {
            foreach ($ops as $opsv) {
               array_push($operasi, $opsv);
            }
         }
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

      $karyawan = $_POST['f1'];
      $users = $this->db(0)->get_where_row("user", "id_user = " . $karyawan);
      $nm_karyawan = $users['nama_user'];
      $karyawan_code = strtoupper(substr($nm_karyawan, 0, 2)) . substr($karyawan, -1);

      $text = $_POST['text'];
      $text = str_replace("|STAFF|", $karyawan_code, $text);

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
      $data_main = $this->db(date('Y'))->count_where('operasi', $where);
      if ($data_main < 1) {
         $in = $this->db(date('Y'))->insertCols('operasi', $cols, $vals);
         if ($in['errno'] <> 0) {
            echo $in['error'];
            exit();
         }

         $set = "pack = " . $pack . ", hanger = " . $hanger;
         $where = "id_cabang = " . $idCabang . " AND id_penjualan = " . $penjualan;
         $up = $this->db($_SESSION[URL::SESSID]['user']['book'])->update('sale', $set, $where);
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         }
      }

      //INSERT NOTIF SELESAI TAPI NOT READY
      $time = date('Y-m-d H:i:s');
      $cols = 'insertTime, id_cabang, no_ref, phone, text, status, tipe';
      $vals = "'" . $time . "'," . $idCabang . "," . $penjualan . ",'" . $hp . "','" . $text . "',5,2";

      $setOne = "no_ref = '" . $penjualan . "' AND tipe = 2";
      $where = "id_cabang = " . $idCabang . " AND " . $setOne;
      $data_main = $this->db(date('Y'))->count_where('notif', $where);
      if ($data_main < 1) {
         $in = $this->db(date('Y'))->insertCols('notif', $cols, $vals);
         if ($up['errno'] <> 0) {
            echo $up['error'];
            exit();
         }
      }
   }
}
