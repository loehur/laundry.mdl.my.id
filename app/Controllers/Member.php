<?php

class Member extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function tambah_paket($get_pelanggan)
   {
      if (isset($get_pelanggan)) {
         $pelanggan = $get_pelanggan;
      } else if (isset($_POST['p'])) {
         $pelanggan = $_POST['p'];
      } else {
         $pelanggan = 0;
      }

      $this->tampilkanMenu($pelanggan);
   }

   public function tampilkanMenu($pelanggan)
   {
      $view = 'member/memberMenu';
      $data_operasi = ['title' => '(+) Deposit Member'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_operasi' => $data_operasi, 'pelanggan' => $pelanggan]);
   }

   public function tampilkan($pelanggan)
   {
      $viewData = 'member/viewData';
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $pelanggan;
      $order = "id_member DESC LIMIT 12";
      $data_manual = $this->db(1)->get_where_order('member', $where, $order);
      $notif = array();

      $kas = array();
      if (count($data_manual) > 0) {
         $numbers = array_column($data_manual, 'id_member');
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wCabang . " AND jenis_transaksi = 3 AND (ref_transaksi BETWEEN " . $min . " AND " . $max . ")";
         $kas = $this->db(1)->get_where('kas', $where);

         //Notif
         $where = $this->wCabang . " AND tipe = 3 AND no_ref BETWEEN " . $min . " AND " . $max;
         $notif = $this->db(1)->get_where('notif_' . $this->id_cabang, $where);
      }

      $sisaSaldo = $this->getSaldoTunai($pelanggan);

      $this->view($viewData, [
         'data_manual' => $data_manual,
         'pelanggan' => $pelanggan,
         'kas' => $kas,
         'notif_member' => $notif,
         'saldoTunai' => $sisaSaldo
      ]);
   }

   function getSaldoTunai($pelanggan)
   {
      //SALDO DEPOSIT
      $saldo = 0;
      $pakai = 0;

      //Kredit
      $where = $this->wCabang . " AND id_client = " . $pelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 AND status_mutasi = 3 GROUP BY id_client ORDER BY saldo DESC";
      $cols = "id_client, SUM(jumlah) as saldo";
      $data = $this->db(1)->get_cols_where('kas', $cols, $where, 1);

      //Debit
      if (count($data) > 0) {
         foreach ($data as $a) {
            $idPelanggan = $a['id_client'];
            $saldo = $a['saldo'];
            $where = $this->wCabang . " AND id_client = " . $idPelanggan . " AND metode_mutasi = 3 AND jenis_mutasi = 2";
            $cols = "SUM(jumlah) as pakai";
            $data2 = $this->db(1)->get_cols_where('kas', $cols, $where, 0);
            if (isset($data2['pakai'])) {
               $pakai = $data2['pakai'];
            }
         }
      }

      $sisaSaldo = $saldo - $pakai;
      return $sisaSaldo;
   }

   public function tampil_rekap()
   {
      $data_operasi = ['title' => 'List Deposit Member'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $viewData = 'member/viewRekap';
      $where = $this->wCabang . " AND bin = 0 GROUP BY id_pelanggan, id_harga ORDER BY saldo DESC";
      $cols = "id_pelanggan, id_harga, SUM(qty) as saldo";
      $data = $this->db(1)->get_cols_where('member', $cols, $where, 1);
      $pakai = array();

      foreach ($data as $a) {
         $idPelanggan = $a['id_pelanggan'];
         $idHarga = $a['id_harga'];
         $saldoPengurangan = 0;
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin  = 0";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->db(1)->get_cols_where('sale_' . $this->id_cabang, $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idPelanggan . $idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idPelanggan . $idHarga] = 0;
         }
      }

      $this->view($viewData, ['data' => $data, 'pakai' => $pakai]);
   }

   public function rekapTunggal($pelanggan)
   {
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $pelanggan . " GROUP BY id_harga ORDER BY saldo DESC";
      $cols = "id_pelanggan, id_harga, SUM(qty) as saldo";
      $data = $this->db(1)->get_cols_where('member', $cols, $where, 1);
      $pakai = array();

      foreach ($data as $a) {
         $idPelanggan = $a['id_pelanggan'];
         $idHarga = $a['id_harga'];
         $saldoPengurangan = 0;
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin  = 0";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->db(1)->get_cols_where('sale_' . $this->id_cabang, $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idPelanggan . $idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idPelanggan . $idHarga] = 0;
         }
      }

      $viewData = 'member/viewRekap';
      $this->view($viewData, ['data' => $data, 'pakai' => $pakai, 'id_pelanggan' => $pelanggan]);
   }

   public function restoreRef()
   {
      $id = $_POST['id'];
      $setOne = "id_member = '" . $id . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 0";
      $this->db(1)->update('member', $set, $where);
   }

   public function orderPaket($pelanggan, $id_harga)
   {
      if ($id_harga <> 0) {
         $where = "id_harga = " . $id_harga;
         $data['main'] = $this->db(0)->get_where('harga_paket', $where);
      } else {
         $data['main'] = $this->db(0)->get('harga_paket');
      }
      $data['pelanggan'] = $pelanggan;
      $this->view('member/formOrder', $data);
   }

   public function deposit($id_pelanggan)
   {
      $id_harga_paket = $_POST['f1'];
      $id_user = $_POST['f2'];
      $where = "id_harga_paket = " . $id_harga_paket;
      $data = $this->db(0)->get_where_row('harga_paket', $where);
      $id_harga = $data['id_harga'];
      $qty = $data['qty'];

      if ($this->mdl_setting['def_price'] == 0) {
         $harga = $data['harga'];
      } else {
         $harga = $data['harga_b'];
         if ($harga == 0) {
            $harga = $data['harga'];
         }
      }

      foreach ($this->harga as $a) {
         if ($a['id_harga'] == $id_harga) {
            $penjualan_jenis = $a['id_penjualan_jenis'];
         }
      }

      $id_poin = 0;
      $per_poin = 0;
      foreach ($this->setPoin as $a) {
         if (strpos($a['list_penjualan_jenis'], '"' . $penjualan_jenis . '"') !== FALSE) {
            $id_poin = $a['id_poin_set'];
            $per_poin = $a['per_poin'];
         }
      }

      $cols = 'id_cabang, id_pelanggan, id_harga, qty, harga, id_user, id_poin, per_poin';
      $id_cabang = $this->db(0)->get_where_row('pelanggan', 'id_pelanggan = ' . $id_pelanggan)['id_cabang'];
      $vals = $id_cabang . "," . $id_pelanggan . "," . $id_harga . "," . $qty . "," . $harga . "," . $id_user . "," . $id_poin . "," . $per_poin;

      $today = date('Y-m-d');
      $setOne = "id_pelanggan = '" . $id_pelanggan . "' AND id_harga = " . $id_harga . " AND qty = " . $qty . " AND insertTime LIKE '" . $today . "%'";
      $where = "id_cabang = " . $id_cabang . " AND " . $setOne;
      $data_main = $this->db(1)->count_where('member', $where);

      if ($data_main < 1) {
         $do = $this->db(1)->insertCols('member', $cols, $vals);
         if ($do['errno'] <> 0) {
            $this->model('Log')->write($do['error']);
         }
      }
      $this->tambah_paket($id_pelanggan);
   }

   public function cekRekap($idPelanggan)
   {
      $viewData = 'penjualan/viewMember';
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga";
      $cols = "id_harga, SUM(qty) as saldo";
      $data = $this->db(1)->get_cols_where('member', $cols, $where, 1);
      $pakai = array();

      foreach ($data as $a) {
         $saldoPengurangan = 0;
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND bin = 0 AND id_harga = " . $idHarga . " AND member = 1";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->db(1)->get_cols_where('sale_' . $this->id_cabang, $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idHarga] = 0;
         }
      }

      $this->view($viewData, ['data' => $data, 'pakai' => $pakai]);
   }

   public function textSaldo()
   {
      $idPelanggan = $_POST['id'];
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga";
      $cols = "id_harga, SUM(qty) as saldo";
      $data = $this->db(1)->get_cols_where('member', $cols, $where, 1);

      foreach ($data as $a) {
         $saldoPengurangan = 0;
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin = 0";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->db(1)->get_cols_where('sale_' . $this->id_cabang, $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idHarga] = 0;
         }
      } ?>

      <table style="width: 100%; margin:0; font-size:x-small; padding:0; border-collapse: collapse;">
         <?php
         foreach ($data as $z) {
            $id = $z['id_harga'];
            $unit = "";
            if ($z['saldo'] > 0) {
               foreach ($this->harga as $a) {
                  if ($a['id_harga'] == $id) {
                     foreach ($this->dPenjualan as $dp) {
                        if ($dp['id_penjualan_jenis'] == $a['id_penjualan_jenis']) {
                           foreach ($this->dSatuan as $ds) {
                              if ($ds['id_satuan'] == $dp['id_satuan']) {
                                 $unit = $ds['nama_satuan'];
                              }
                           }
                        }
                     }
                     $saldoAwal = $z['saldo'];
                     $saldoAkhir = $saldoAwal - $pakai[$id];
                  }
               }
            }
         ?>
            <tr>
               <td style="padding:0; margin:0">
                  M<?= $id ?>
               </td>
               <td style="text-align: right; padding:0; margin:0">
                  <?= number_format($saldoAkhir, 2) . $unit ?>
               </td>
            </tr>
   <?php
         }
         echo "</table>";
      }

      public function bin()
      {
         $id = $_POST['id'];
         $set = "bin = 1";
         $setOne = "id_member = '" . $id . "'";
         $where = $this->wCabang . " AND " . $setOne;
         $do = $this->db(1)->update('member', $set, $where);
         if ($do['errno'] <> 0) {
            $this->model('Log')->write($do['error']);
         } else {
            echo 0;
         }
      }

      public function sendNotifDeposit($id_member)
      {
         $d = $this->db(1)->get_where_row('member', "id_member = " . $id_member);
         $cabangKode = $this->db(0)->get_cols_where('cabang', 'kode_cabang', 'id_cabang = ' . $d['id_cabang'], 0)['kode_cabang'];
         $pelanggan = $this->db(0)->get_cols_where('pelanggan', 'nama_pelanggan, nomor_pelanggan', 'id_pelanggan = ' . $d['id_pelanggan'], 0);

         $layanan = '';
         foreach ($this->harga as $a) {
            if ($a['id_harga'] == $d['id_harga']) {
               foreach ($this->dPenjualan as $dp) {
                  if ($dp['id_penjualan_jenis'] == $a['id_penjualan_jenis']) {
                     foreach ($this->dSatuan as $ds) {
                        if ($ds['id_satuan'] == $dp['id_satuan']) {
                           $unit = $ds['nama_satuan'];
                        }
                     }
                  }
               }
               foreach (unserialize($a['list_layanan']) as $b) {
                  foreach ($this->dLayanan as $c) {
                     if ($b == $c['id_layanan']) {
                        $layanan .= $c['layanan'] . " ";
                     }
                  }
               }
               foreach ($this->dDurasi as $c) {
                  if ($a['id_durasi'] == $c['id_durasi']) {
                     $durasi = $c['durasi'];
                  }
               }

               foreach ($this->itemGroup as $c) {
                  if ($a['id_item_group'] == $c['id_item_group']) {
                     $kategori = $c['item_kategori'];
                  }
               }
            }
         }


         $where = $this->wCabang . " AND jenis_transaksi = 3 AND ref_transaksi = '" . $id_member . "' AND status_mutasi = 3";
         $totalBayar = $this->db(1)->sum_col_where('kas', 'jumlah', $where);
         $text_bayar = "Bayar Rp" . number_format($totalBayar);

         if ($totalBayar >= $d['harga']) {
            $text_bayar = "LUNAS";
         }

         $text = strtoupper($pelanggan['nama_pelanggan']) . " _#" . $cabangKode . "_ \n#" . $id_member . " Topup Paket M" . $d['id_harga'] . "\n" . $kategori . " " . $d['qty'] . $unit . "\n" . $layanan . $durasi . "\n*Total Rp" . number_format($d['harga']) . ". " . $text_bayar . "* \n" . $this->HOST_URL . "/I/m/" . $d['id_pelanggan'] . "/" . $d['id_harga'];
         $text = str_replace("<sup>2</sup>", "²", $text);
         $text = str_replace("<sup>3</sup>", "³", $text);
         $cols =  'insertTime, id_cabang, no_ref, phone, text, id_api, proses, tipe';
         $hp = $pelanggan['nomor_pelanggan'];
         $res = $this->model("M_WA")->send($hp, $text, URL::WA_TOKEN);
         $time = $d['insertTime'];
         $noref = $id_member;

         $setOne = "no_ref = '" . $noref . "' AND tipe = 3";
         $where = $this->wCabang . " AND " . $setOne;
         $data_main = $this->db(1)->count_where('notif_' . $this->id_cabang, $where);

         if (isset($res["id"])) {
            foreach ($res["id"] as $k => $v) {
               $status = $res["process"];
               $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "','" . $v . "','" . $status . "',3";
            }
         } else if (isset($res['reason'])) {
            $status = $res['reason'];
            $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "','','" . $status . "',3";
         } else {
            $cols =  'insertTime, id_cabang, no_ref, phone, text, tipe';
            $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "',3";
         }

         if ($data_main < 1) {
            $this->db(1)->insertCols('notif_' . $d['id_cabang'], $cols, $vals);
         }
      }
   }
