<?php

class SetDelivery extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'harga';
   }

   // ---------------- INDEX -------------------- //
   public function i($page)
   {
      $z = array();
      $data_main = array();

      $view = 'setHarga/delivery';
      $data_operasi = ['title' => 'Tarif Delivery'];
      $z = array('unit' => 'km', 'set' => 'Delivery', 'page' => $page);
      $where = 'id_penjualan_jenis = ' . $page;
      $data_main = $this->db(0)->get_where($this->table, $where);

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main, 'z' => $z]);
   }

   public function insert($page)
   {
      $layanan = serialize($_POST['f1']);
      $harga = $_POST['f2'];

      $cols = 'id_penjualan_jenis, list_layanan, harga';
      $vals = $page . ",'" . $layanan . "'," . $harga;

      $setOne = 'id_penjualan_jenis = ' . $page;
      $where = $setOne . " AND list_layanan = '$layanan'";
      $data_main = $this->db(0)->count_where($this->table, $where);
      if ($data_main < 1) {
         $this->db(0)->insertCols($this->table, $cols, $vals);
      }
   }

   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];

      if ($mode == 1) {
         $col = "harga";
      }

      $set = "$col = '$value'";
      $where = "id_harga = $id";
      $this->db(0)->update($this->table, $set, $where);
   }
}
