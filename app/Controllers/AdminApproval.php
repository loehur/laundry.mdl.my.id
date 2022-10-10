<?php

class AdminApproval extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }
   public function index()
   {
      $data_operasi = ['title' => 'Admin Approval'];

      //SETORAN
      $setoran = array();
      $where = $this->wCabang . " AND jenis_mutasi = 2 AND status_mutasi = 2 AND metode_mutasi = 1 AND jenis_transaksi = 2 ORDER BY id_kas DESC LIMIT 20";
      $setoran = $this->model('M_DB_1')->get_where('kas', $where);

      //NON TUNAI
      $nonTunai = array();
      $where = $this->wCabang . " AND metode_mutasi <> 1 AND status_mutasi = 2 ORDER BY id_kas DESC LIMIT 20";
      $nonTunai = $this->model('M_DB_1')->get_where('kas', $where);

      //HAPUS ORDER
      $operasi_order =  array();
      $kas_order = array();
      $surcas_order = array();

      $data_operasi = ['title' => 'Approval Data Hapus'];
      $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 1 ORDER BY id_penjualan DESC LIMIT 300";
      $hapusOrder = $this->model('M_DB_1')->get_where('penjualan', $where);

      $numbers = array_column($hapusOrder, 'id_penjualan');
      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wCabang . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi_order = $this->model('M_DB_1')->get_where('operasi', $where);
      }

      $refs = array_column($hapusOrder, 'no_ref');
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = $this->wCabang . " AND ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref;
         $kas_order = $this->model('M_DB_1')->get_where('kas', $where);

         //SURCAS
         $where = $this->wCabang . " AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $surcas_order = $this->model('M_DB_1')->get_where('surcas', $where);
      }

      //DEPOSIT MEMBER HAPUS
      $depositHapus = array();
      $where = $this->wCabang . " AND bin = 1";
      $order = "id_member DESC";
      $depositHapus = $this->model('M_DB_1')->get_where_order('member', $where, $order);

      $kas_hapus = array();
      if (count($depositHapus) > 0) {
         $numbers = array_column($depositHapus, 'id_member');
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wCabang . " AND jenis_transaksi = 3 AND (ref_transaksi BETWEEN " . $min . " AND " . $max . ")";
         $kas_hapus = $this->model('M_DB_1')->get_where('kas', $where);
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view(
         'admin_approval/admin_approval_main',
         [
            'setoran' => $setoran,
            'nonTunai' => $nonTunai,
            'hapusOrder' => $hapusOrder,
            'operasi_order' => $operasi_order,
            'kas_order' => $kas_order,
            'surcas_order' => $surcas_order,
            'depositHapus' => $depositHapus,
            'kas_hapus' => $kas_hapus
         ]
      );
   }
}
