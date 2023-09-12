<?php

class SetHargaPaket extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'harga_paket';
   }

   public function index()
   {
      $view = 'setHargaPaket/harga_paket_main';
      $data_operasi = ['title' => 'Harga Paket'];
      $where = $this->wLaundry . " ORDER BY id_harga ASC, qty ASC";
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
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
      $where = $this->wLaundry . " ORDER BY id_harga ASC, qty ASC";
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
      $this->view($viewData, ['data_main' => $data_main]);
   }

   public function insert()
   {
      $id_harga = $_POST['f1'];
      $qty = $_POST['f2'];
      $harga = $_POST['f3'];
      $keterangan = $_POST['f4'];

      $cols = 'id_laundry, id_harga, qty, harga, keterangan';
      $vals = $this->id_laundry . "," . $id_harga . "," . $qty . "," . $harga . ",'" . $keterangan . "'";

      $setOne = "id_harga = " . $id_harga . " AND qty = " . $qty;
      $where = $this->wLaundry . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where($this->table, $where);
      if ($data_main < 1) {
         print_r($this->model('M_DB_1')->insertCols($this->table, $cols, $vals));
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
      $where = $this->wLaundry . " AND id_harga_paket = " . $id;
      $query = $this->model('M_DB_1')->update('harga_paket', $set, $where);
      if ($query) {
         $this->dataSynchrone();
      }
   }
}
