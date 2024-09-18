<?php

class SetPoin extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'poin_set';
   }

   public function i()
   {
      $data_main = array();
      $view = 'setGroup/poin_set';
      $z = ['title' => 'Poin Set'];
      $data_operasi = ['title' => 'Poin Set'];
      $data_main = $this->db(0)->get($this->table);
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main, 'z' => $z]);
   }

   public function insert()
   {
      $list1 = serialize($_POST['f1']);
      $cols = 'list_penjualan_jenis, per_poin';
      $vals = "'" . $list1 . "'," . $_POST['f2'];
      $where = "list_penjualan_jenis = '" . $list1 . "'";
      $data_main = $this->db(0)->count_where($this->table, $where);
      if ($data_main < 1) {
         $this->db(0)->insertCols($this->table, $cols, $vals);
         $this->dataSynchrone();
      }
   }

   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];
      if ($mode == 1) {
         $col = "per_poin";
      }
      $set = "$col = '$value'";
      $where = "id_poin_set = $id";
      $this->db(0)->update($this->table, $set, $where);
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
      $set = "list_penjualan_jenis = '$value'";
      $where = "id_poin_set = $id";
      $this->db(0)->update($this->table, $set, $where);
      $this->dataSynchrone();
   }

   public function addItem()
   {
      $id = $_POST['f2'];
      $value = $_POST['f3'];
      $serVal = unserialize($value);
      $add = $_POST['f1'];
      array_push($serVal, "$add");
      $value = serialize($serVal);
      $setOne = "list_penjualan_jenis = '" . $value . "'";
      $where = $setOne . " AND id_poin_set = " . $id;
      $data_main = $this->db(0)->count_where($this->table, $where);
      if ($data_main < 1) {
         $set = "list_penjualan_jenis = '$value'";
         $where = "id_poin_set = " . $id;;
      }
      $this->db(0)->update($this->table, $set, $where);
      $this->dataSynchrone();
   }
}
