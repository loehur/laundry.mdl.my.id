<?php

class SetDiskon extends Controller
{
   public function __construct()
   {
      $this->session_cek(1);
      $this->data();
      $this->table = 'diskon_qty';
   }

   // ---------------- INDEX -------------------- //
   public function i()
   {
      $view = 'setHarga/diskon';
      $data_main = $this->db(0)->get($this->table);
      $data_operasi = ['title' => 'Harga Diskon Kuantitas'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main]);
   }

   public function insert()
   {
      $cols = 'id_penjualan_jenis, qty_disc, disc_qty, combo';
      $vals = $_POST['f1'] . "," . $_POST['f3'] . "," . $_POST['f4'] . "," . $_POST['combo'];

      $where = 'id_penjualan_jenis = ' . $_POST['f1'];
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
      $mode = $_POST['mode'];

      if ($mode == 2) {
         $col = "qty_disc";
      } elseif ($mode == 3) {
         $col = "disc_qty";
      } elseif ($mode == 4) {
         $col = "disc_partner";
      }

      $set = $col . " = '" . $value . "'";
      $where = "id_diskon = " . $id;
      $this->db(0)->update($this->table, $set, $where);
      $this->dataSynchrone();
   }

   public function updateCell_s()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $col = "combo";

      $set = $col . " = '" . $value . "'";
      $where = "id_diskon = " . $id;
      $this->db(0)->update($this->table, $set, $where);
      $this->dataSynchrone();
   }
}
