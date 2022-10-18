<?php

class HapusOrder extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'penjualan';
   }

   public function index()
   {
      $viewData = 'hapusOrder/hapus_order_main';
      $operasi =  array();
      $kas = array();
      $surcas = array();

      $data_operasi = ['title' => 'Approval Data Hapus'];
      $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 1 ORDER BY id_penjualan DESC LIMIT 300";
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);

      $numbers = array_column($data_main, 'id_penjualan');
      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wCabang . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->model('M_DB_1')->get_where('operasi', $where);
      }

      $refs = array_column($data_main, 'no_ref');
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = $this->wCabang . " AND ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref;
         $kas = $this->model('M_DB_1')->get_where('kas', $where);

         //SURCAS
         $where = $this->wCabang . " AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $surcas = $this->model('M_DB_1')->get_where('surcas', $where);
      }

      $this->view($viewData, [
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'surcas' => $surcas
      ]);
   }

   public function hapusRelated()
   {
      $transaksi = $_POST['transaksi'];

      if (isset($_POST['dataRef'])) {
         $dataRef = unserialize($_POST['dataRef']);
         foreach ($dataRef as $a) {

            //KAS
            $where = $this->wCabang . " AND ref_transaksi = '" . $a . "' AND jenis_transaksi = " . $transaksi;
            $this->model('M_DB_1')->delete_where("kas", $where);

            //SURCHARGE
            $where2 = $this->wCabang . " AND no_ref = '" . $a . "' AND transaksi_jenis = 1";
            $this->model('M_DB_1')->delete_where("surcas", $where2);
         }
      }
      if (isset($_POST['dataID']) && $transaksi <> 3) {
         $dataID = unserialize($_POST['dataID']);
         foreach ($dataID as $a) {
            $where = $this->wCabang . " AND id_penjualan = " . $a;
            $this->model('M_DB_1')->delete_where("operasi", $where);
         }
      }
   }
   public function hapusID()
   {
      $tableNya = $_POST['table'];
      $kolomID =  $_POST['kolomID'];
      if (isset($_POST['dataID'])) {
         $dataID = unserialize($_POST['dataID']);
         foreach ($dataID as $a) {
            $where = $this->wCabang . " AND " . $kolomID . " = " . $a;
            $this->model('M_DB_1')->delete_where($tableNya, $where);
         }
      }
   }
}
