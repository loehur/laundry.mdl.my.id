<?php

class AdminApproval extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function index($mode)
   {
      $data_operasi = ['title' => 'Approval'];

      //SETORAN
      $setoran = array();
      $where = $this->wCabang . " AND jenis_mutasi = 2 AND status_mutasi = 2 AND metode_mutasi = 1 AND jenis_transaksi = 2 ORDER BY id_kas DESC LIMIT 20";
      $setoran = $this->model('M_DB_1')->get_where('kas', $where);

      //NON TUNAI
      $nonTunai = array();
      $where = $this->wCabang . " AND metode_mutasi = 2 AND status_mutasi = 2 ORDER BY id_kas DESC LIMIT 20";
      $nonTunai = $this->model('M_DB_1')->get_where('kas', $where);

      $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 1 ORDER BY id_penjualan DESC LIMIT 300";
      $hapusOrder = $this->model('M_DB_1')->get_where('penjualan', $where);

      //DEPOSIT MEMBER HAPUS
      $depositHapus = array();
      $where = $this->wCabang . " AND bin = 1";
      $order = "id_member DESC";
      $depositHapus = $this->model('M_DB_1')->get_where_order('member', $where, $order);

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view(
         'admin_approval/admin_approval_main',
         [
            'Setoran' => $setoran,
            'NonTunai' => $nonTunai,
            'HapusOrder' => $hapusOrder,
            'HapusDeposit' => $depositHapus,
            'mode' => $mode
         ]
      );
   }
}
