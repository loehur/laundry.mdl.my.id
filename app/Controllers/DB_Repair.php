<?php

class DB_Repair extends Controller
{
   public function __construct()
   {
      $this->data();
      $this->table = 'penjualan';
   }

   public function cek()
   {
      $cols = "no_ref, count(no_ref) as c_ref";
      $where = "id_pelanggan <> 0 GROUP BY no_ref";
      $data = $this->model('M_DB_1')->get_cols_where($this->table, $cols, $where, 1);
      foreach ($data as $k => $d) {
         if ($d['c_ref'] == 1) {
            unset($data[$k]);
         }
      }

      foreach ($data as $dn) {
         $where = "no_ref = '" . $dn['no_ref'] . "'";
         $da = $this->model('M_DB_1')->get_where($this->table, $where);

         foreach ($da as $a) {
            $id = $a['id_penjualan'];
            $qty = $a['qty'];
            $harga = $a['harga'];
            $total = $harga * $qty;
            $diskon_qty = $a['diskon_qty'];
            $member = $a['member'];
            $diskon_partner = $a['diskon_partner'];

            if ($member == 0) {
               if ($diskon_qty > 0 && $diskon_partner == 0) {
                  $total = $total - ($total * ($diskon_qty / 100));
               } else if ($diskon_qty == 0 && $diskon_partner > 0) {
                  $total = $total - ($total * ($diskon_partner / 100));
               } else if ($diskon_qty > 0 && $diskon_partner > 0) {
                  $total = $total - ($total * ($diskon_qty / 100));
                  $total = $total - ($total * ($diskon_partner / 100));
               } else {
                  $total = ($harga * $qty);
               }
            } else {
               $total = 0;
            }

            $where_update = "id_penjualan = " . $id;
            $set = "total = " . $total;
            $this->model('M_DB_1')->update($this->table, $set, $where_update);
         }
      }
   }
}
