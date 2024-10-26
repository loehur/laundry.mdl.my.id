<?php

class SetDiskon_Khusus extends Controller
{
   public $table;
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
      $data_main = $this->db(0)->get_where($this->table, 'id_cabang = ' . $_SESSION['user']['id_cabang'] . ' ORDER BY id_diskon_khusus DESC');
      $data_operasi = ['title' => 'Harga Diskon Khusus'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main]);
   }

   public function insert()
   {
      $cols = 'id_pelanggan, id_harga, diskon, id_cabang';
      $vals = $_POST['pelanggan'] . "," . $_POST['id_harga'] . "," . $_POST['diskon'] . "," . $_SESSION['user']['id_cabang'];

      $where = "id_harga = " . $_POST['id_harga'] . " AND id_pelanggan = " . $_POST['pelanggan'];
      $data_main = $this->db(0)->count_where($this->table, $where);
      if ($data_main < 1) {
         $do = $this->db(0)->insertCols($this->table, $cols, $vals);
         if ($do['errno'] == 0) {
            echo 0;
            $this->dataSynchrone($_SESSION['user']['id_user']);
         } else {
            echo $do['error'];
         }
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
      $this->dataSynchrone($_SESSION['user']['id_user']);
   }
}
