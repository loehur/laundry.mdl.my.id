<?php

class SetDiskon_Khusus extends Controller
{
   public function __construct()
   {
      $this->session_cek(1);
      $this->operating_data();
      $this->table = 'diskon_khusus';
   }

   // ---------------- INDEX -------------------- //
   public function i()
   {
      $view = 'setHarga/diskon_khusus';
      $where = $this->wCabang;
      $data_main = $this->db(0)->get_where($this->table, $where);
      $data_operasi = ['title' => 'Harga Diskon Khusus'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main]);
   }

   public function insert()
   {
      $cols = 'id_pelanggan, id_harga, diskon';
      $vals = $_POST['pelanggan'] . "," . $_POST['id_harga'] . "," . $_POST['diskon'];

      $where = "id_harga = " . $_POST['id_harga'] . " AND id_pelanggan = " . $_POST['pelanggan'];
      $data_main = $this->db(0)->count_where($this->table, $where);
      if ($data_main < 1) {
         print_r($this->db(0)->insertCols($this->table, $cols, $vals));
         $this->dataSynchrone();
      }
   }

   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $col = "diskon";

      $set = $col . " = '" . $value . "'";
      $where = "id_diskon_khusus = " . $id;
      $this->db(0)->update($this->table, $set, $where);
      $this->dataSynchrone();
   }
}
