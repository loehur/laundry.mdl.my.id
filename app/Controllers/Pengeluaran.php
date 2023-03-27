<?php

class Pengeluaran extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'kas';
   }

   public function index()
   {
      $view = 'admin_approval/pengeluaran';
      $where = $this->wCabang . " AND jenis_mutasi = 2 AND metode_mutasi = 1 AND jenis_transaksi = 4 ORDER BY id_kas DESC LIMIT 20";
      $list = $this->model('M_DB_1')->get_where($this->table, $where);
      $this->view($view, ['list' => $list]);
   }

   public function operasi($tipe)
   {
      $id = $_POST['id'];
      $set = "status_mutasi = '" . $tipe . "'";
      $where = $this->wCabang . " AND id_kas = " . $id;
      $this->model('M_DB_1')->update($this->table, $set, $where);
   }
}
