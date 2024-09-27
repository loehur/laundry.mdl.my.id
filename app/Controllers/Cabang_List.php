<?php

class Cabang_List extends Controller
{

   public function __construct()
   {
      $this->session_cek(1);
      $this->operating_data();
   }
   public function index()
   {
      $data_operasi = ['title' => 'Data Cabang'];

      $table = 'cabang';
      $data_cabang = $this->db(0)->get($table);

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('data_list/cabang', ['data_cabang' => $data_cabang]);
   }

   public function insert()
   {
      $table  = 'cabang';
      $columns = ' id_kota, alamat, kode_cabang';
      $values = "'" . $_POST["kota"] . "','" . $_POST["alamat"] . "','" . $_POST["kode_cabang"] . "'";
      $this->db(0)->insertCols($table, $columns, $values);
      $this->dataSynchrone();
   }

   public function selectCabang()
   {
      $id_cabang = $_POST['id'];
      $table  = 'user';
      $set = "id_cabang = " . $id_cabang;
      $where = "id_user = " . $this->id_user;
      $this->db(0)->update($table, $set, $where);
      $this->dataSynchrone();
   }

   public function update()
   {
      $table  = 'cabang';
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];

      if ($mode == 1) {
         $kolom = "kode_cabang";
      } else if ($mode == 2) {
         $kolom = "alamat";
      } else {
         $kolom = "id_kota";
      }
      $set = "$kolom = '$value'";
      $where = "id_cabang = $id";
      $this->db(0)->update($table, $set, $where);
      $this->dataSynchrone();
   }
}
