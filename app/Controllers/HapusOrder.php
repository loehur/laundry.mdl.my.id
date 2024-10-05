<?php

class HapusOrder extends Controller
{
   public function __construct()
   {
      $this->session_cek(1);
      $this->operating_data();
   }

   public function index()
   {
      $viewData = 'hapusOrder/hapus_order_main';

      $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 1 ORDER BY id_penjualan DESC LIMIT 50";
      $data_main = $this->db(1)->get_where('sale_' . $this->id_cabang, $where);

      $operasi = [];
      $kas = [];
      $surcas = [];
      $notifBon = [];
      $notifSelesai = [];

      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_unique(array_column($data_main, 'no_ref'));
      foreach ($numbers as $id) {

         //OPERASI
         $where = $this->wCabang . " AND id_penjualan = " . $id;
         $ops = $this->db(1)->get_where_row('operasi', $where);
         if (count($ops) > 0) {
            array_push($operasi, $ops);
         }
      }

      foreach ($refs as $rf) {
         //KAS
         $where = $this->wCabang . " AND jenis_transaksi = 1 AND ref_transaksi = '" . $rf . "'";
         $ks = $this->db(1)->get_where_row('kas', $where);
         if (count($ks) > 0) {
            array_push($kas, $ks);
         }

         //SURCAS
         $where = $this->wCabang . " AND no_ref = '" . $rf . "'";
         $sc = $this->db(0)->get_where_row('surcas', $where);
         if (count($sc) > 0) {
            array_push($surcas, $sc);
         }

         //NOTIF BON
         $where = $this->wCabang . " AND tipe = 1 AND no_ref = '" . $rf . "'";
         $nf = $this->db(1)->get_where_row('notif_' . $this->id_cabang, $where);
         if (count($nf) > 0) {
            array_push($notifBon, $nf);
         }
      }

      $this->view($viewData, [
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'surcas' => $surcas,
         'notif_bon' => $notifBon,
         'notif_selesai' => $notifSelesai
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
            $this->db(1)->delete_where('kas', $where);

            //NOTIF_BON
            $where = $this->wCabang . " AND no_ref = '" . $a . "' AND tipe = 1";
            $this->db(1)->delete_where('notif_' . $this->id_cabang, $where);

            //SURCHARGE
            $where2 = $this->wCabang . " AND no_ref = '" . $a . "' AND transaksi_jenis = 1";
            $this->db(0)->delete_where("surcas", $where2);
         }
      }
      if (isset($_POST['dataID']) && $transaksi <> 3) {
         $dataID = unserialize($_POST['dataID']);
         foreach ($dataID as $a) {
            $where = $this->wCabang . " AND id_penjualan = " . $a;
            $this->db(1)->delete_where('operasi', $where);

            //NOTIF
            $where = $this->wCabang . " AND no_ref = '" . $a . "' AND tipe = 2";
            $this->db(1)->delete_where('notif_' . $this->id_cabang, $where);
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
            $del = $this->db(1)->delete_where($tableNya, $where);
            if ($del['errno'] <> 0) {
               echo $del['error'];
               exit();
            }
         }
      }
      echo 0;
   }
}
