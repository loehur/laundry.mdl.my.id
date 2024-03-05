<?php

class SetGroup extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'item_group';
   }

   public function i($page)
   {
      $data_main = array();
      $view = 'setGroup/all';
      $setOne = 'id_penjualan_jenis = ' . $page;
      foreach ($this->dPenjualan as $a) {
         if ($page == $a['id_penjualan_jenis']) {
            $penjualan = $a['penjualan_jenis'];
            $z = ['title' => 'Produk ' . $penjualan, 'page' => $page];
            $data_operasi = ['title' => 'Produk ' . $penjualan];
         }
      }
      $where = $this->wLaundry . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
      $d2 = $this->model('M_DB_1')->get_where('item', $this->wLaundry);
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main, 'd2' => $d2, 'z' => $z]);
   }

   public function insert($page)
   {
      $item_list = serialize($_POST['f1']);
      $cols = 'id_laundry, id_penjualan_jenis, item_kategori, item_list';
      $vals = $this->id_laundry . "," . $page . ",'" . $_POST['f2'] . "','" . $item_list . "'";
      $where = $this->wLaundry . " AND item_kategori = '" . $_POST['f2'] . "' AND id_penjualan_jenis = $page";
      $data_main = $this->model('M_DB_1')->count_where($this->table, $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols($this->table, $cols, $vals);
         $this->dataSynchrone();
      }
   }

   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];
      if ($mode == 1) {
         $col = "item_kategori";
      }
      $set = "$col = '$value'";
      $where = $this->wLaundry . " AND id_item_group = $id";
      $this->model('M_DB_1')->update($this->table, $set, $where);
      $this->dataSynchrone();
   }

   public function removeItem()
   {
      $id = $_POST['id'];
      $id_item = $_POST['id_item'];
      $value = $_POST['value'];
      $serVal = unserialize($value);
      $newVal = array_diff($serVal, array($id_item));
      $value = serialize($newVal);
      $set = "item_list = '$value'";
      $where = $this->wLaundry . " AND id_item_group = $id";
      $this->model('M_DB_1')->update($this->table, $set, $where);
      $this->dataSynchrone();
   }

   public function addItem($page)
   {
      $setOne = 'id_penjualan_jenis = ' . $page;
      $id = $_POST['f2'];
      $value = $_POST['f3'];
      $serVal = unserialize($value);
      $add = $_POST['f1'];
      $add_ = '"' . $add . '"';
      $where = $this->wLaundry . " AND " . $setOne . " AND item_list LIKE '%$add_%'";
      $data_main = $this->model('M_DB_1')->count_where($this->table, $where);
      if ($data_main < 1) {
         array_push($serVal, "$add");
         $value = serialize($serVal);
         $set = "item_list = '$value'";
         $where = $this->wLaundry . " AND id_item_group = $id";
      }
      $this->model('M_DB_1')->update($this->table, $set, $where);
      $this->dataSynchrone();
   }
}
