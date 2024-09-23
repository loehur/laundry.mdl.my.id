<?php

class Export extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function index()
   {
      $data_operasi = ['title' => "Data Export - Rekap"];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view(__CLASS__ . '/content');
   }

   public function export()
   {
      $month = $_POST['month'];
      $delimiter = ",";
      $filename = "SALES-" . $month . ".csv";
      $f = fopen('php://memory', 'w');

      $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND insertTime LIKE '" . $month . "%'";
      $data = $this->db(1)->get_where('sale_' . $this->id_cabang, $where);
      $fields = array('ID', 'TANGGAL', 'PELANGGAN', 'JENIS', 'ITEM', 'QTY', 'TOTAL');
      fputcsv($f, $fields, $delimiter);
      foreach ($data as $a) {
         $id = $a['id_penjualan'];
         $tgl_order = substr($a['insertTime'], 0, 10);
         $pelanggan = strtoupper($a['pelanggan']);

         $jenis_ = $a['id_penjualan_jenis'];
         foreach ($this->dPenjualan as $pj) {
            if ($pj['id_penjualan_jenis'] == $jenis_) {
               $jenis = strtoupper($pj['penjualan_jenis']);
            }
         }

         $item_ = $a['id_item_group'];
         foreach ($this->itemGroup as $ig) {
            if ($ig['id_item_group'] == $item_) {
               $item = strtoupper($ig['item_kategori']);
            }
         }

         $jumlah = $a['qty'];
         $total = $a['total'];
         $lineData = array($id, $tgl_order, $pelanggan, $jenis, $item, $jumlah, $total);
         fputcsv($f, $lineData, $delimiter);
      }

      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $filename . '";');
      fpassthru($f);
   }
}
