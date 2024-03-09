<?php

class SetDiskon_Khusus extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'diskon_khusus';
   }

   // ---------------- INDEX -------------------- //
   public function i()
   {
      $view = 'setHarga/diskon_khusus';
      $where = $this->wCabang;
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
      $data_operasi = ['title' => 'Harga Diskon Khusus'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main]);
   }

   public function insert()
   {
      $cols = 'id_laundry, id_cabang, id_pelanggan, id_harga, diskon';
      $vals = $this->id_laundry . "," . $this->id_cabang . "," . $_POST['pelanggan'] . "," . $_POST['id_harga'] . "," . $_POST['diskon'];

      $setOne = "id_harga = " . $_POST['id_harga'] . " AND id_pelanggan = " . $_POST['pelanggan'];
      $where = $this->wLaundry . " AND " . $setOne;

      $data_main = $this->model('M_DB_1')->count_where($this->table, $where);
      if ($data_main < 1) {
         print_r($this->model('M_DB_1')->insertCols($this->table, $cols, $vals));
         $this->dataSynchrone();
      }
   }

   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $col = "diskon";

      $set = $col . " = '" . $value . "'";
      $where = $this->wLaundry . " AND id_diskon_khusus = " . $id;
      $this->model('M_DB_1')->update($this->table, $set, $where);
      $this->dataSynchrone();
   }
}
