<?php

class Penjualan extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'penjualan';
   }

   public function index()
   {
      $viewData = 'penjualan/penjualan_main';
      $data_operasi = ['title' => 'Buka Order'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData);
   }

   public function cart()
   {
      $viewData = 'penjualan/cart';
      $where = $this->wCabang . " AND id_pelanggan = 0";
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
      $this->view($viewData, ['data_main' => $data_main]);
   }

   public function insert($page)
   {
      $id_harga = $_POST['f1'];
      $qty = $_POST['f2'];
      $note = $_POST['f3'];

      foreach ($this->harga as $a) {
         if ($a['id_harga'] == $id_harga) {
            $durasi = $a['id_durasi'];
            $hari = $a['hari'];
            $jam = $a['jam'];
            $item_group = $a['id_item_group'];

            if ($this->mdl_setting['def_price'] == 0) {
               $harga = $a['harga'];
            } else {
               $harga = $a['harga_b'];
               if ($harga == 0) {
                  $harga = $a['harga'];
               }
            }

            $layanan = $a['list_layanan'];
            $minOrder = $a['min_order'];
         }
      }

      $diskon_qty = 0;
      foreach ($this->diskon as $a) {
         if ($a['id_penjualan_jenis'] == $page && $a['qty_disc'] > 0) {
            if ($qty >= $a['qty_disc']) {
               $diskon_qty = $a['disc_qty'];
            }
         }
      }

      $id_poin = 0;
      $per_poin = 0;
      foreach ($this->setPoin as $a) {
         if (strpos($a['list_penjualan_jenis'], '"' . $page . '"') !== FALSE) {
            $id_poin = $a['id_poin_set'];
            $per_poin = $a['per_poin'];
         }
      }

      $cols = 'id_laundry, id_cabang, id_item_group, id_penjualan_jenis, id_durasi, hari, jam, harga, qty, note, id_poin, per_poin, list_layanan, diskon_qty, min_order, id_harga, insertTime';
      $vals = $this->id_laundry . "," . $this->id_cabang . "," . $item_group . "," . $page . "," . $durasi . "," . $hari . "," . $jam . "," . $harga . "," . $qty . ",'" . $note . "'," . $id_poin . "," . $per_poin . ",'" . $layanan . "'," . $diskon_qty . "," . $minOrder . "," . $id_harga . ",'" . $GLOBALS['now'] . "'";
      $this->model('M_DB_1')->insertCols($this->table, $cols, $vals);

      $set = "sort = sort+1";
      $whereSort = "id_harga = " . $id_harga;
      $this->model('M_DB_1')->update("harga", $set, $whereSort);
   }

   public function proses()
   {
      $no_ref = $this->id_cabang . date("YmdHis");
      $where = $this->wCabang . " AND id_pelanggan = 0";
      $data = $this->model('M_DB_1')->get_where($this->table, $where);
      $pelanggan = $_POST['f1'];

      $disc_p = 0;

      $nama_pelanggan = "";
      foreach ($this->pelanggan as $dp) {
         if ($dp['id_pelanggan'] == $pelanggan) {
            $disc_p = $dp['disc'];
            $nama_pelanggan = $dp['nama_pelanggan'];
            break;
         }
      }

      $saldo = 0;
      foreach ($data as $a) {
         $saldo = 0;
         $id = $a['id_penjualan'];
         $id_jenis = $a['id_penjualan_jenis'];
         $idHarga = $a['id_harga'];
         $qty = $a['qty'];

         $harga = $a['harga'];
         $total = $harga * $qty;
         $diskon_qty = $a['diskon_qty'];
         $member = $a['member'];

         //CEK JIKA DISKON KHUSUS
         $where_dk = $this->wCabang . " AND id_pelanggan = " . $pelanggan . " AND id_harga = " . $idHarga;
         $diskon_k = $this->model('M_DB_1')->get_where_row("diskon_khusus", $where_dk);
         if (isset($diskon_k['diskon'])) {
            if ($diskon_k['diskon'] > 0) {
               $disc_p = $diskon_k['diskon'];
            }
         }

         $diskon_partner = $disc_p;

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

         $saldo = $this->saldoMember($pelanggan, $idHarga);
         if ($saldo >= $qty) {
            $set = "id_pelanggan = " . $pelanggan . ", no_ref = " . $no_ref . ", pelanggan = '" . $nama_pelanggan . "', member = 1, id_poin = 0, per_poin = 0, diskon_partner = " . $disc_p . ", total = " . $total . ", id_user = " . $_POST['f2'];
            $whereSet = "id_penjualan = " . $id;
            $this->model('M_DB_1')->update($this->table, $set, $whereSet);
         }

         $reset_diskon = "";
         if ($diskon_qty > 0 && $diskon_partner > 0) {
            foreach ($this->diskon as $a) {
               if ($a['id_penjualan_jenis'] == $id_jenis) {
                  if ($a['combo'] == 0) {
                     $reset_diskon = "diskon_qty = 0, ";
                  }
               }
            }
         }
         $where_update = "id_penjualan = " . $id;
         $set = $reset_diskon . "id_pelanggan = " . $pelanggan . ", pelanggan = '" . $nama_pelanggan . "', diskon_partner = " . $disc_p . ", total = " . $total . ", no_ref = " . $no_ref . ", id_user = " . $_POST['f2'];
         $this->model('M_DB_1')->update($this->table, $set, $where_update);
      }

      $set = "sort = sort+1";
      $whereSort = "id_pelanggan = " . $pelanggan;
      $this->model('M_DB_1')->update("pelanggan", $set, $whereSort);
   }

   public function saldoMember($idPelanggan, $idHarga)
   {
      //SALDO
      $saldo = 0;
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga;
      $cols = "SUM(qty) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('member', $cols, $where, 0);
      $saldoManual = $data['saldo'];

      //DIPAKAI
      $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND member = 1 AND bin = 0 AND id_harga = " . $idHarga;
      $cols = "SUM(qty) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('penjualan', $cols, $where, 0);
      $saldoPengurangan = $data['saldo'];

      $saldo = $saldoManual - $saldoPengurangan;
      return round($saldo, 2);
   }

   public function updateCell()
   {
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];

      if ($mode == 1) {
         $col = "hari";
      } elseif ($mode == 2) {
         $col = "jam";
      }

      $set = $col . " = '" . $value . "'";
      $where = $this->wLaundry . " AND id_durasi_client  = " . $id;
      $this->model('M_DB_1')->update($this->table, $set, $where);
   }

   public function removeRow()
   {
      $id = $_POST['id'];
      $where = $this->wCabang . " AND id_penjualan = '" . $id . "'";
      $this->model('M_DB_1')->delete_where($this->table, $where);
   }

   public function addItemForm($data)
   {
      $data = explode("|", $data);
      $b = $this->model('M_DB_1')->get_where_row("item_group", "id_laundry = " . $this->id_laundry . " AND id_item_group = " . $data[0])['item_list'];
      $c = $data[1];
      $this->view('penjualan/formItemAdd', ['data' => $b, 'id' => $c]);
   }

   public function orderPenjualanForm($id_penjualan, $id_harga, $saldo = false)
   {
      $data[1] = $id_penjualan;
      $data[2] = $id_harga;
      $data[3] = $saldo;
      $this->view('penjualan/formOrder', $data);
   }

   public function addItem($id)
   {
      $f1 = $_POST['f1'];
      $f2 = $_POST['f2'];
      $newItem = array($f1 => $f2);
      $item_list =  $this->model('M_DB_1')->get_where_row("penjualan", $this->wCabang . " AND id_penjualan  = " . $id)['list_item'];
      if (strlen($item_list) == 0) {
         $value = serialize($newItem);
      } else {
         $arrItemList = unserialize($item_list);
         $arrItemList[$f1] = $f2;
         $value = serialize($arrItemList);
      }
      $set = "list_item = '" . $value . "'";
      $where = $this->wCabang . " AND id_penjualan = " . $id;
      $this->model('M_DB_1')->update($this->table, $set, $where);
   }

   public function removeItem()
   {
      $id = $_POST['id'];
      $key = $_POST['key'];
      $item_list =  $this->model('M_DB_1')->get_where_row("penjualan", $this->wCabang . " AND id_penjualan  = " . $id)['list_item'];
      $arrItemList = unserialize($item_list);
      unset($arrItemList[$key]);
      $value = serialize($arrItemList);
      $set = "list_item = '" . $value . "'";
      $where = $this->wCabang . " AND id_penjualan = " . $id;
      $this->model('M_DB_1')->update($this->table, $set, $where);
   }

   public function sering($idPelanggan)
   {
      $viewData = 'penjualan/viewSering';
      $where = $this->wCabang . " AND id_harga <> 0 AND bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga, id_penjualan_jenis, id_item_group, list_layanan, id_durasi ORDER BY count(id_penjualan) DESC limit 2";
      $cols = "id_harga, id_penjualan_jenis, id_item_group, list_layanan, id_durasi, count(id_penjualan)";
      $data = $this->model('M_DB_1')->get_cols_where('penjualan', $cols, $where, 1);
      $this->view($viewData, ['data' => $data]);
   }

   function loadPelanggan()
   {
      $z = array('page' => "pelanggan");
      $view = 'data_list/pelanggan';
      $where = $this->wCabang;
      $order = 'id_pelanggan DESC';
      $data_main = $this->model('M_DB_1')->get_where_order("pelanggan", $where, $order);
      $this->view($view, ['data_main' => $data_main, 'z' => $z]);
   }
}
