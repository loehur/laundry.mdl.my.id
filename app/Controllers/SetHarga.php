<?php

class SetHarga extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'harga';
   }

   public function i($page)
   {
      $view = 'setHarga/all';
      foreach ($this->dPenjualan as $a) {
         if ($page == $a['id_penjualan_jenis']) {
            $penjualan = $a['penjualan_jenis'];
            $data_operasi = ['title' => 'Harga ' . $penjualan];
            $z = array('unit' => $a['id_satuan'], 'set' => $penjualan, 'page' => $page);
         }
      }

      $setOne = 'id_penjualan_jenis = ' . $page;
      $where = $this->wLaundry . " AND " . $setOne;
      $d2 = $this->model('M_DB_1')->get_where('item_group', $where);
      $where = $this->table . "." . $this->wLaundry . " AND " . $this->table . "." . $setOne . " ORDER BY id_item_group ASC, list_layanan ASC, id_durasi ASC";
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main, 'd2' => $d2, 'z' => $z]);
   }

   public function insert($page)
   {
      $layanan = serialize($_POST['f2']);
      $durasi = $_POST['f3'];
      $item_group = $_POST['f1'];
      $cols = 'id_laundry, id_penjualan_jenis, id_item_group, list_layanan, id_durasi, harga, min_order';
      $vals = $this->id_laundry . "," . $page . "," . $item_group . ",'" . $layanan . "'," . $durasi . "," . $_POST['f4'] . "," . $_POST['f5'];
      $setOne = 'id_penjualan_jenis = ' . $page;
      $where = $this->wLaundry . " AND " . $setOne . " AND list_layanan = '$layanan' AND id_durasi = $durasi AND id_item_group = $item_group";
      $data_main = $this->model('M_DB_1')->count_where($this->table, $where);
      if ($data_main < 1) {
         $query = $this->model('M_DB_1')->insertCols($this->table, $cols, $vals);
         if ($query) {
            $this->dataSynchrone();
         }
      }
   }

   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];

      switch ($mode) {
         case "1":
            $col = "harga";
            break;
         case "6":
            $col = "harga_b";
            break;
         case "2":
            $col = "hari";
            break;
         case "3":
            $col = "jam";
            break;
         case "4":
            $col = "sort";
            break;
         case "5":
            $col = "min_order";
            break;
      }

      $set = $col . " = '" . $value . "'";
      $where = $this->wLaundry . " AND id_harga = " . $id;
      $query = $this->model('M_DB_1')->update($this->table, $set, $where);
      if ($query) {
         $this->dataSynchrone();
      }
   }

   public function removeRow()
   {
      $id = $_POST['id'];
      $where = $this->wLaundry . " AND id_harga = " . $id;
      $query = $this->model('M_DB_1')->delete_where($this->table, $where);
      if ($query) {
         $this->dataSynchrone();
      }
   }
}
