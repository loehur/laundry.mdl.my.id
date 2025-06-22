<?php

class Filter extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function i($filter)
   {
      $kas = array();
      $notif = array();
      $notifPenjualan = array();
      $data_main = array();
      $surcas = array();

      switch ($filter) {
         case 1:
            //PENGAMBILAN
            $data_operasi = ['title' => 'Order Filter Pengambilan'];
            $viewData = 'filter/view';
            break;
         case 2:
            //PENGANTARAN
            $data_operasi = ['title' => 'Order Filter Pengantaran'];
            $viewData = 'filter/view';
            break;
         default:
            break;
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('filter/form', [
         'modeView' => $filter,
      ]);
      $this->view($viewData, [
         'modeView' => $filter,
         'data_main' => $data_main,
         'kas' => $kas,
         "notif" => $notif,
         'notif_penjualan' => $notifPenjualan,
         "surcas" => $surcas,
      ]);
   }

   public function loadList($filter, $from = "", $to = "")
   {
      $data_main = array();
      $viewData = 'filter/view_content';

      switch ($filter) {
         case 1:
            //PENGAMBILAN
            if ($from <> "") {
               $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND SUBSTRING(tgl_ambil, 1, 10) >= '$from' AND SUBSTRING(tgl_ambil, 1, 10) <= '$to' ORDER BY id_penjualan DESC";
               $data_main = $this->db($_SESSION['user']['book'])->get_where('sale', $where);
            }
            break;
         case 2:
            //PENGANTARAN
            if ($from <> "") {
               $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND SUBSTRING(insertTime, 1, 10) >= '$from' AND SUBSTRING(insertTime, 1, 10) <= '$to' ORDER BY id_penjualan DESC";
               $data_main = $this->db($_SESSION['user']['book'])->get_where('sale', $where);
            }
            break;
         default:
            break;
      }

      $operasi = [];
      $kas = [];
      $surcas = [];
      $notif = [];

      if ($from <> "") {

         $numbers = array_column($data_main, 'id_penjualan');
         $refs = array_unique(array_column($data_main, 'no_ref'));

         foreach ($numbers as $id) {
            //OPERASI
            $where = $this->wCabang . " AND id_penjualan = " . $id;
            $ops = $this->db($_SESSION['user']['book'])->get_where('operasi', $where);
            if (count($ops) > 0) {
               foreach ($ops as $opsv) {
                  array_push($operasi, $opsv);
               }
            }
         }

         foreach ($refs as $rf) {
            //KAS
            $where = $this->wCabang . " AND jenis_transaksi = 1 AND ref_transaksi = '" . $rf . "'";
            $ks = $this->db($_SESSION['user']['book'])->get_where_row('kas', $where);
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
            $nf = $this->db($_SESSION['user']['book'])->get_where_row('notif', $where);
            if (count($nf) > 0) {
               array_push($notif, $nf);
            }
         }
      }

      $this->view($viewData, [
         'modeView' => $filter,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         "surcas" => $surcas,
         'notif_bon' => $notif,
      ]);
   }

   public function clearTuntas()
   {
      if (isset($_POST['data'])) {
         $data = unserialize($_POST['data']);
         foreach ($data as $a) {
            $this->tuntasOrder($a);
         }
      }
   }

   public function surcas()
   {
      $jenis = $_POST['surcas'];
      $jumlah = $_POST['jumlah'];
      $user = $_POST['user'];
      $id_transaksi = $_POST['no_ref'];

      $cols =  'id_cabang, transaksi_jenis, id_jenis_surcas, jumlah, id_user, no_ref';
      $vals = $this->id_cabang . ",1," . $jenis . "," . $jumlah . "," . $user . "," . $id_transaksi;

      $setOne = "transaksi_jenis = 1 AND no_ref = " . $id_transaksi . " AND id_jenis_surcas = " . $jenis;
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->db(0)->count_where('surcas', $where);
      if ($data_main < 1) {
         $this->db(0)->insertCols('surcas', $cols, $vals);
      }
   }

   public function updateRak()
   {
      $rak = $_POST['value'];
      $id = $_POST['id'];

      $set = "letak = '" . $rak . "'";
      $where = $this->wCabang . " AND id_penjualan = " . $id;
      $this->db($_SESSION['user']['book'])->update('sale', $set, $where);

      //CEK SUDAH TERKIRIM BELUM
      $setOne = "no_ref = '" . $id . "' AND proses <> '' AND tipe = 2";
      $where = $setOne;
      $data_main = $this->db(date('Y'))->count_where('notif', $where);
      if ($data_main < 1) {
         $this->notifReadySend($id);
      }
   }

   public function tuntasOrder($ref)
   {
      $set = "tuntas = 1";
      $where = $this->wCabang . " AND no_ref = " . $ref;
      $this->db($_SESSION['user']['book'])->update('sale', $set, $where);
   }

   public function notifReadySend($idPenjualan)
   {
      $setOne = "no_ref = '" . $idPenjualan . "' AND tipe = 2";
      $where = $this->wCabang . " AND " . $setOne;
      $dm = $this->db(0)->get_where_row('notif', $where);
      $hp = $dm['phone'];
      $text = $dm['text'];
      $res = $this->model(URL::WA_API[0])->send($hp, $text, URL::WA_TOKEN[0]);
      if ($res['forward']) {
         //ALTERNATIF WHATSAPP
         $res = $this->model(URL::WA_API[1])->send($hp, $text, URL::WA_TOKEN[1]);
      }
      foreach ($res["id"] as $k => $v) {
         $status = $res['data']['status'];
         $set = "status = 1, proses = '" . $status . "', id_api = '" . $res['data']['id'] . "'";
         $where2 = $this->wCabang . " AND no_ref = '" . $idPenjualan . "' AND tipe = 2";
         $this->db($_SESSION['user']['book'])->update('notif', $set, $where2);
      }
   }

   public function directWA($countMember)
   {
      $noref = $_POST['ref'];
      $text = $_POST['text'];
      $idPelanggan = $_POST['idPelanggan'];

      if ($countMember > 0) {
         $textMember = $this->textSaldoNotif($idPelanggan);
         $text = $text . $textMember;
      }

      $set = "direct_wa = 1";
      $setOne = "no_ref = '" . $noref . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $this->db($_SESSION['user']['book'])->update('sale', $set, $where);

      echo $text;
   }

   public function textSaldoNotif($idPelanggan)
   {
      $textSaldo = "";
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga";
      $cols = "id_harga, SUM(qty) as saldo";
      $data = $this->db($_SESSION['user']['book'])->get_cols_where('member', $cols, $where, 1);

      foreach ($data as $a) {
         $saldoPengurangan = 0;
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->db($_SESSION['user']['book'])->get_cols_where('sale', $cols, $where, 0);

         if (isset($data2['saldo'])) {
            $saldoPengurangan = $data2['saldo'];
            $pakai[$idHarga] = $saldoPengurangan;
         } else {
            $pakai[$idHarga] = 0;
         }
      }
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
         $textSaldo = $textSaldo . " | M" . $id . " " . number_format($saldoAkhir, 2) . $unit;
      }
      return $textSaldo;
   }

   public function ambil()
   {
      $karyawan = $_POST['f1'];
      $id = $_POST['f2'];
      $dateNow = date('Y-m-d H:i:s');
      $set = "tgl_ambil = '" . $dateNow . "', id_user_ambil = " . $karyawan;
      $setOne = "id_penjualan = '" . $id . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $this->db($_SESSION['user']['book'])->update('sale', $set, $where);
   }

   public function hapusRef()
   {
      $ref = $_POST['ref'];
      $note = $_POST['note'];
      $setOne = "no_ref = '" . $ref . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 1, bin_note = '" . $note . "'";
      $this->db($_SESSION['user']['book'])->update('sale', $set, $where);
   }

   public function restoreRef()
   {
      $ref = $_POST['ref'];
      $setOne = "no_ref = '" . $ref . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 0";
      $this->db($_SESSION['user']['book'])->update('sale', $set, $where);
   }

   public function poin($pelanggan)
   {
      $where = $this->wCabang . " AND id_pelanggan = " . $pelanggan . " AND bin = 0 AND id_poin > 0";
      $data_main = $this->db($_SESSION['user']['book'])->get_where('sale', $where);

      $prevRef = '';
      $countRef = 0;
      foreach ($data_main as $a) {
         $ref = $a['no_ref'];

         if ($prevRef <> $a['no_ref']) {
            $countRef = 0;
            $countRef++;
            $arrRef[$ref] = $countRef;
         } else {
            $countRef++;
            $arrRef[$ref] = $countRef;
         }
         $prevRef = $ref;
      }

      $no = 0;
      $urutRef = 0;
      $arrGetPoin = array();
      $arrTotalPoin = array();
      $arrPoin = array();
      $totalPoinPenjualan = 0;

      foreach ($data_main as $a) {
         $no++;
         $f6 = $a['qty'];
         $f7 = $a['harga'];
         $f16 = $a['min_order'];
         $noref = $a['no_ref'];
         $idPoin = $a['id_poin'];
         $perPoin = $a['per_poin'];

         $qty_real = 0;
         if ($f6 < $f16) {
            $qty_real = $f16;
         } else {
            $qty_real = $f6;
         }

         if ($no == 1) {
            $subTotal = 0;
            $urutRef++;
         }

         if ($idPoin > 0) {
            if (isset($arrPoin[$noref][$idPoin]) ==  TRUE) {
               $arrPoin[$noref][$idPoin] = $arrPoin[$noref][$idPoin] + ($qty_real * $f7);
            } else {
               $arrPoin[$noref][$idPoin] = ($qty_real * $f7);
            }
            $arrGetPoin[$noref][$idPoin] = $arrPoin[$noref][$idPoin] / $perPoin;
            $gPoin = 0;
            if (isset($arrGetPoin[$noref][$idPoin]) == TRUE) {
               foreach ($arrGetPoin[$noref] as $gpp) {
                  $gPoin = $gPoin + $gpp;
               }
               $arrTotalPoin[$noref] = floor($gPoin);
            }
         }
         $total = ($f7 * $qty_real);
         $subTotal = $subTotal + $total;
         foreach ($arrRef as $key => $m) {
            if ($key == $noref) {
               $arrCount = $m;
            }
         }
         if ($arrCount == $no) {
            if (isset($arrTotalPoin[$noref]) && $arrTotalPoin[$noref] > 0) {
               $totalPoinPenjualan  = $totalPoinPenjualan +  $arrTotalPoin[$noref];
            }
            $no = 0;
            $subTotal = 0;
         }
      }

      //POIN MEMBER
      $totalPoinMember = 0;
      $where_m = $this->wCabang . " AND id_pelanggan = " . $pelanggan . " AND id_poin > 0";
      $data_member = $this->db($_SESSION['user']['book'])->get_where('member', $where_m);
      foreach ($data_member as $z) {
         $harga = $z['harga'];
         $idPoin = $z['id_poin'];
         $perPoin = $z['per_poin'];
         $gPoin_m = 0;
         $gPoin_m = floor($harga / $perPoin);
         $totalPoinMember += $gPoin_m;
      }

      //POIN MANUAL
      $where = $this->wCabang . " AND id_pelanggan = " . $pelanggan;
      $data_manual = $this->db(0)->get_where('poin', $where);

      $arrPoinManual = array();
      $arrPoinManual = array_column($data_manual, 'poin_jumlah');
      $totalPoinManual = array_sum($arrPoinManual);
      $totalPoin = $totalPoinPenjualan + $totalPoinMember + $totalPoinManual;
      return $totalPoin;
   }

   public function getPoin($pelanggan)
   {
      echo $this->poin($pelanggan);
   }
}
