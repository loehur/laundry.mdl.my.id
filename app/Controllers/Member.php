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
      $data_manual = $this->db(0)->get_where_order('member', $where, $order);

      $notif = [];
      $kas = [];

      foreach ($data_manual as $dme) {

         //KAS
         $where = $this->wCabang . " AND jenis_transaksi = 3 AND ref_transaksi = '" . $dme['id_member'] . "'";
         $ks = $this->db($_SESSION['user']['book'])->get_where('kas', $where);
         if (count($ks) > 0) {
            foreach ($ks as $ksv) {
               array_push($kas, $ksv);
            }
         }

         //NOTIF BON
         $where = $this->wCabang . " AND tipe = 3 AND no_ref = '" . $dme['id_member'] . "'";
         $nm = $this->db($_SESSION['user']['book'])->get_where_row("notif", $where);
         if (count($nm) > 0) {
            array_push($notif, $nm);
         }
      }

      $sisaSaldo = $this->data('Saldo')->getSaldoTunai($pelanggan);

      $this->view($viewData, [
         'data_manual' => $data_manual,
         'pelanggan' => $pelanggan,
         'kas' => $kas,
         'notif_member' => $notif,
         'saldoTunai' => $sisaSaldo
      ]);
   }

   public function tampil_rekap()
   {
      $data_operasi = ['title' => 'List Deposit Member'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $viewData = 'member/viewRekap';
      $where = $this->wCabang . " AND bin = 0 GROUP BY id_pelanggan, id_harga ORDER BY saldo DESC";
      $cols = "id_pelanggan, id_harga, SUM(qty) as saldo";
      $data = $this->db(0)->get_cols_where('member', $cols, $where, 1);
      $pakai = array();

      foreach ($data as $a) {
         $idPelanggan = $a['id_pelanggan'];
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin  = 0";

         $pakai[$idPelanggan . $idHarga] = 0;
         $pakai[$idPelanggan . $idHarga] = 0;

         $cols = "SUM(qty) as saldo";
         for ($y = 2021; $y <= date('Y'); $y++) {
            $data2 = $this->db($y)->get_cols_where('sale', $cols, $where, 0);
            if (isset($data2['saldo'])) {
               $pakai[$idPelanggan . $idHarga] += $data2['saldo'];
            }
         }
      }

      $this->view($viewData, ['data' => $data, 'pakai' => $pakai]);
   }

   public function rekapTunggal($pelanggan)
   {
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $pelanggan . " GROUP BY id_harga ORDER BY saldo DESC";
      $cols = "id_pelanggan, id_harga, SUM(qty) as saldo";
      $data = $this->db(0)->get_cols_where('member', $cols, $where, 1);
      $pakai = array();

      foreach ($data as $a) {
         $idPelanggan = $a['id_pelanggan'];
         $idHarga = $a['id_harga'];
         $saldoPengurangan = 0;
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin  = 0";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->db($_SESSION['user']['book'])->get_cols_where('sale', $cols, $where, 0);

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
      $this->db($_SESSION['user']['book'])->update('member', $set, $where);
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
      $data_main = $this->db(0)->count_where('member', $where);

      if ($data_main < 1) {
         $do = $this->db(0)->insertCols('member', $cols, $vals);
         if ($do['errno'] <> 0) {
            $this->model('Log')->write($do['error']);
         }
      }
      $this->tambah_paket($id_pelanggan);
   }

   public function cekRekap($idPelanggan)
   {
      $viewData = 'penjualan/viewMember';
      $data = [];
      $where = "bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga";
      $cols = "id_harga, SUM(qty) as saldo";

      $data_ = $this->db(0)->get_cols_where('member', $cols, $where, 1);
      if (count($data_) > 0) {
         foreach ($data_ as $dm) {
            array_push($data, $dm);
         }
      }
      $pakai = array();

      foreach ($data as $a) {
         $saldoPengurangan = 0;
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND bin = 0 AND id_harga = " . $idHarga . " AND member = 1";
         $cols = "SUM(qty) as saldo";
         $pakai[$idHarga] = 0;
         for ($y = 2021; $y <= date('Y'); $y++) {
            $data2 = $this->db($y)->get_cols_where('sale', $cols, $where, 0);
            if (isset($data2['saldo'])) {
               $saldoPengurangan = $data2['saldo'];
               $pakai[$idHarga] += $saldoPengurangan;
            }
         }
      }

      $this->view($viewData, ['data' => $data, 'pakai' => $pakai]);
   }

   public function textSaldo()
   {
      $idPelanggan = $_POST['id'];
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga";
      $cols = "id_harga, SUM(qty) as saldo";
      $data = $this->db($_SESSION['user']['book'])->get_cols_where('member', $cols, $where, 1);

      foreach ($data as $a) {
         $saldoPengurangan = 0;
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1 AND bin = 0";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->db($_SESSION['user']['book'])->get_cols_where('sale', $cols, $where, 0);

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
         $do = $this->db($_SESSION['user']['book'])->update('member', $set, $where);
         if ($do['errno'] <> 0) {
            $this->model('Log')->write($do['error']);
         } else {
            echo 0;
         }
      }

      public function sendNotifDeposit($id_member)
      {
         $d = $this->db(0)->get_where_row('member', "id_member = " . $id_member);
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
         $totalBayar = $this->db($_SESSION['user']['book'])->sum_col_where('kas', 'jumlah', $where);
         $text_bayar = "Bayar Rp" . number_format($totalBayar);

         if ($totalBayar >= $d['harga']) {
            $text_bayar = "LUNAS";
         }

         $text = strtoupper($pelanggan['nama_pelanggan']) . " _#" . $cabangKode . "_ \n#" . $id_member . " Topup Paket M" . $d['id_harga'] . "\n" . $kategori . " " . $d['qty'] . $unit . "\n" . $layanan . $durasi . "\n*Total Rp" . number_format($d['harga']) . ". " . $text_bayar . "* \n" . $this->HOST_URL . "/I/m/" . $d['id_pelanggan'] . "/" . $d['id_harga'];
         $text = str_replace("<sup>2</sup>", "²", $text);
         $text = str_replace("<sup>3</sup>", "³", $text);
         $cols =  'insertTime, id_cabang, no_ref, phone, text, id_api, proses, tipe';
         $hp = $pelanggan['nomor_pelanggan'];
         $res = $this->model(URL::WA_API[0])->send($hp, $text, URL::WA_TOKEN[0]);
         if ($res['forward']) {
            //ALTERNATIF WHATSAPP
            $res = $this->model(URL::WA_API[1])->send($hp, $text, URL::WA_TOKEN[1]);
         }
         $time = $d['insertTime'];
         $noref = $id_member;

         $setOne = "no_ref = '" . $noref . "' AND tipe = 3";
         $where = $this->wCabang . " AND " . $setOne;
         $data_main = $this->db(date('Y'))->count_where("notif", $where);

         if ($res['status']) {
            $status = $res['data']['status'];
            $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "','" . $res['data']['id'] . "','" . $status . "',3";
         } else {
            $status = $res['data']['status'];
            $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "','','" . $status . "',3";
         }

         if ($data_main < 1) {
            $this->db(date('Y'))->insertCols('notif', $cols, $vals);
         }
      }
   }
