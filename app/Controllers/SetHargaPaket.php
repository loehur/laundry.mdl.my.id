<?php

class SetHargaPaket extends Controller
{
   public function __construct()
   {
      $this->session_cek(1);
      $this->data();
      $this->table = 'harga_paket';
   }

   public function index()
   {
      $view = 'setHargaPaket/harga_paket_main';
      $data_operasi = ['title' => 'Harga Paket'];
      $order = "id_harga ASC, qty ASC";
      $data_main = $this->db(0)->get_order($this->table, $order);
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main]);
   }

   public function form($id_penjualan)
   {
      $this->view('setHargaPaket/formOrder', $id_penjualan);
   }

   public function cart()
   {
      $viewData = 'setHargaPaket/cart';
      $order = "id_harga ASC, qty ASC";
      $data_main = $this->db(0)->get_order($this->table, $order);
      $this->view($viewData, ['data_main' => $data_main]);
   }

   public function insert()
   {
      $id_harga = $_POST['f1'];
      $qty = $_POST['f2'];
      $harga = $_POST['f3'];
      $keterangan = $_POST['f4'];

      $cols = 'id_harga, qty, harga, keterangan';
      $vals = $id_harga . "," . $qty . "," . $harga . ",'" . $keterangan . "'";

      $where = "id_harga = " . $id_harga . " AND qty = " . $qty;
      $data_main = $this->db(0)->count_where($this->table, $where);
      if ($data_main < 1) {
         print_r($this->db(0)->insertCols($this->table, $cols, $vals));
      }
   }

   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];

      if ($mode == 'a') {
         $col = 'harga';
      } else {
         $col = 'harga_b';
      }
      $set = $col . " = '" . $value . "'";
      $where = "id_harga_paket = " . $id;
      $query = $this->db(0)->update('harga_paket', $set, $where);
      if ($query['errno'] == 0) {
         $this->dataSynchrone();
      }
   }
}
