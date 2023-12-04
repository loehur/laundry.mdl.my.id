<?php

class NonTunai extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'kas';
   }

   public function index()
   {
      $view = 'non_tunai/nt_main';
      $cols = "ref_finance, note, id_user, id_client, status_mutasi, jenis_transaksi, SUM(jumlah) as total";
      $where = $this->wCabang . " AND metode_mutasi = 2 AND status_mutasi = 2 AND ref_finance <> '' GROUP BY ref_finance ORDER BY ref_finance DESC LIMIT 20";
      $list['cek'] = $this->model('M_DB_1')->get_cols_where($this->table, $cols, $where, 1);

      $where = $this->wCabang . " AND metode_mutasi = 2 AND status_mutasi <> 2 AND ref_finance <> '' GROUP BY ref_finance ORDER BY ref_finance DESC LIMIT 20";
      $list['done'] = $this->model('M_DB_1')->get_cols_where($this->table, $cols, $where, 1);

      $this->view($view, $list);
   }

   public function operasi($tipe)
   {
      $id = $_POST['id'];
      $set = "status_mutasi = '" . $tipe . "'";
      $where = $this->wCabang . " AND ref_finance = '" . $id . "'";
      $this->model('M_DB_1')->update($this->table, $set, $where);
   }
}
