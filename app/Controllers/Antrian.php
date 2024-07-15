<?php

class Antrian extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'penjualan';
   }

   public function i($antrian)
   {
      $kas = array();
      $notif = array();
      $notifPenjualan = array();
      $data_main = array();
      $surcas = array();

      switch ($antrian) {
         case 1:
            //DALAM PROSES 10 HARI
            $data_operasi = ['title' => 'Data Order Proses H7-'];
            $viewData = 'antrian/view';
            break;
         case 6:
            //DALAM PROSES > 7 HARI
            $data_operasi = ['title' => 'Data Order Proses H7+'];
            $viewData = 'antrian/view';
            break;
         case 7:
            //DALAM PROSES > 30 HARI
            $data_operasi = ['title' => 'Data Order Proses H30+'];
            $viewData = 'antrian/view';
            break;
         case 8:
            //DALAM PROSES > 1 Tahun
            $data_operasi = ['title' => 'Data Order Proses H365+'];
            $viewData = 'antrian/view';
            break;
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('antrian/form', [
         'modeView' => $antrian,
      ]);
      $this->view($viewData, [
         'modeView' => $antrian,
         'data_main' => $data_main,
         'kas' => $kas,
         'notif' => $notif,
         'notif_penjualan' => $notifPenjualan,
         'surcas' => $surcas,
      ]);
   }

   public function p($antrian)
   {
      $kas = array();
      $notif = array();
      $notifPenjualan = array();
      $data_main = array();
      $surcas = array();

      switch ($antrian) {
         case 100:
            //DALAM PROSES 10 HARI
            $data_operasi = ['title' => 'Data Piutang H7-'];
            $viewData = 'antrian/view';
            break;
         case 101:
            //DALAM PROSES > 7 HARI
            $data_operasi = ['title' => 'Data Piutang H7+'];
            $viewData = 'antrian/view';
            break;
         case 102:
            //DALAM PROSES > 30 HARI
            $data_operasi = ['title' => 'Data Piutang H30+'];
            $viewData = 'antrian/view';
            break;
         case 103:
            //DALAM PROSES > 1 Tahun
            $data_operasi = ['title' => 'Data Piutang H365+'];
            $viewData = 'antrian/view';
            break;
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('antrian/form', [
         'modeView' => $antrian,
      ]);
      $this->view($viewData, [
         'modeView' => $antrian,
         'data_main' => $data_main,
         'kas' => $kas,
         'notif' => $notif,
         'notif_penjualan' => $notifPenjualan,
         'surcas' => $surcas,
      ]);
   }

   public function loadList($antrian)
   {
      $data_main = array();
      $operasi = array();
      $kas = array();
      $surcas = array();
      $notif = array();
      $viewData = 'antrian/view_content';
      switch ($antrian) {
         case 1:
            //DALAM PROSES 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) <= (insertTime + INTERVAL 7 DAY) ORDER BY id_penjualan DESC";
            $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
            break;
         case 6:
            //DALAM PROSES > 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 7 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 30 DAY) ORDER BY id_penjualan DESC";
            $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
            break;
         case 7:
            //DALAM PROSES > 30 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 30 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 365 DAY) ORDER BY id_penjualan DESC";
            $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
            break;
         case 8:
            //DALAM PROSES > 1 TAHUN
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 365 DAY) ORDER BY id_penjualan DESC";
            $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
            break;
         case 100:
            //PIUTANG 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND id_user_ambil <> 0 AND DATE(NOW()) <= (insertTime + INTERVAL 7 DAY) ORDER BY id_penjualan ASC";
            $data_main_a = $this->model('M_DB_1')->get_where($this->table, $where);
            foreach ($data_main_a as $a) {
               $where = $this->wCabang . " AND no_ref = '" . $a['no_ref'] . "'";
               $data_main_b = $this->model('M_DB_1')->get_where($this->table, $where);
               foreach ($data_main_b as $key => $value) {
                  array_push($data_main, $value);
               }
            }
            break;
         case 101:
            //PIUTANG > 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND id_user_ambil <> 0 AND DATE(NOW()) > (insertTime + INTERVAL 7 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 30 DAY) ORDER BY id_penjualan ASC";
            $data_main_a = $this->model('M_DB_1')->get_where($this->table, $where);
            foreach ($data_main_a as $a) {
               $where = $this->wCabang . " AND no_ref = '" . $a['no_ref'] . "'";
               $data_main_b = $this->model('M_DB_1')->get_where($this->table, $where);
               foreach ($data_main_b as $key => $value) {
                  array_push($data_main, $value);
               }
            }
            break;
         case 102:
            //PIUTANG > 30 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND id_user_ambil <> 0 AND DATE(NOW()) > (insertTime + INTERVAL 30 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 365 DAY) ORDER BY id_penjualan ASC";
            $data_main_a = $this->model('M_DB_1')->get_where($this->table, $where);
            foreach ($data_main_a as $a) {
               $where = $this->wCabang . " AND no_ref = '" . $a['no_ref'] . "'";
               $data_main_b = $this->model('M_DB_1')->get_where($this->table, $where);
               foreach ($data_main_b as $key => $value) {
                  array_push($data_main, $value);
               }
            }
            break;
         case 103:
            //PIUTANG > 1 TAHUN
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND id_user_ambil <> 0 AND DATE(NOW()) > (insertTime + INTERVAL 365 DAY) ORDER BY id_penjualan ASC";
            $data_main_a = $this->model('M_DB_1')->get_where($this->table, $where);
            foreach ($data_main_a as $a) {
               $where = $this->wCabang . " AND no_ref = '" . $a['no_ref'] . "'";
               $data_main_b = $this->model('M_DB_1')->get_where($this->table, $where);
               foreach ($data_main_b as $key => $value) {
                  array_push($data_main, $value);
               }
            }
            break;
      }

      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_column($data_main, 'no_ref');

      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wLaundry . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->model('M_DB_1')->get_where('operasi', $where);
      }

      if (count($refs) > 0) {
         //KAS
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = $this->wCabang . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $kas = $this->model('M_DB_1')->get_where('kas', $where);

         //SURCAS
         $where = $this->wCabang . " AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $surcas = $this->model('M_DB_1')->get_where('surcas', $where);

         //NOTIF BON
         $where = $this->wCabang . " AND tipe = 1 AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $notif = $this->model('M_DB_1')->get_where('notif', $where);
      }

      $this->view($viewData, [
         'modeView' => $antrian,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'surcas' => $surcas,
         'notif' => $notif,
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

   public function operasi()
   {
      $karyawan = $_POST['f1'];
      $penjualan = $_POST['f2'];
      $operasi = $_POST['f3'];
      $cols = 'id_laundry, id_cabang, id_penjualan, jenis_operasi, id_user_operasi, insertTime';
      $vals = $this->id_laundry . "," . $this->id_cabang . "," . $penjualan . "," . $operasi . "," . $karyawan . ",'" . $GLOBALS['now'] . "'";
      $setOne = 'id_penjualan = ' . $penjualan . " AND jenis_operasi =" . $operasi;
      $where = $this->wCabang . " AND " . $setOne;

      $data_main = $this->model('M_DB_1')->count_where('operasi', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('operasi', $cols, $vals);
      }

      //INSERT NOTIF SELESAI TAPI NOT READY
      $hp = $_POST['hp'];
      $text = $_POST['text'];
      $time = date('Y-m-d H:i:s');

      $cols = 'insertTime, id_cabang, no_ref, phone, text, status, tipe, token';
      $vals = "'" . $time . "'," . $this->id_cabang . "," . $penjualan . ",'" . $hp . "','" . $text . "',5,2,'" . $this->dLaundry['notif_token'] . "'";
      $setOne = "no_ref = '" . $penjualan . "' AND tipe = 2";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
      }

      if (isset($_POST['rak'])) {
         if (strlen($_POST['rak']) > 0) {
            $rak = $_POST['rak'];
            $pack = $_POST['pack'];
            $hanger = $_POST['hanger'];
            $set = "letak = '" . $rak . "', pack = " . $pack . ", hanger = " . $hanger;
            $where = $this->wCabang . " AND id_penjualan = " . $penjualan;
            $this->model('M_DB_1')->update($this->table, $set, $where);

            //CEK SUDAH TERKIRIM BELUM
            $setOne = "no_ref = '" . $penjualan . "' AND proses <> '' AND tipe = 2";
            $where = $setOne;
            $data_main = $this->model('M_DB_1')->count_where('notif', $where);
            if ($data_main < 1) {
               $this->notifReadySend($penjualan);
            }
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
      $data_main = $this->model('M_DB_1')->count_where('surcas', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('surcas', $cols, $vals);
      }
   }

   public function updateRak($mode = 0)
   {
      $rak = $_POST['value'];
      $id = $_POST['id'];

      switch ($mode) {
         case 0:
            $set = "letak = '" . $rak . "'";
            break;
         case 1:
            $set = "pack = '" . $rak . "'";
            break;
         case 2:
            $set = "hanger = '" . $rak . "'";
            break;
         default:
            $set = "letak = '" . $rak . "'";
            break;
      }
      $where = $this->wCabang . " AND id_penjualan = " . $id;
      $this->model('M_DB_1')->update($this->table, $set, $where);

      //CEK SUDAH TERKIRIM BELUM
      $setOne = "no_ref = '" . $id . "' AND proses <> '' AND tipe = 2";
      $where = $setOne;
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);
      if ($data_main < 1) {
         $this->notifReadySend($id);
      }
   }

   public function tuntasOrder($ref)
   {
      $set = "tuntas = 1";
      $where = $this->wCabang . " AND no_ref = " . $ref;
      $this->model('M_DB_1')->update($this->table, $set, $where);
   }

   public function notifReadySend($idPenjualan)
   {
      $setOne = "no_ref = '" . $idPenjualan . "' AND tipe = 2";
      $where = $this->wCabang . " AND " . $setOne;
      $dm = $this->model('M_DB_1')->get_where_row('notif', $where);
      $hp = $dm['phone'];
      $text = $dm['text'];
      $res = $this->model("M_WA")->send($hp, $text, $this->dLaundry['notif_token']);
      if (isset($res["id"])) {
         foreach ($res["id"] as $v) {
            $status = $res["process"];
            $set = "status = 1, proses = '" . $status . "', id_api = '" . $v . "'";
            $where2 = $this->wCabang . " AND no_ref = '" . $idPenjualan . "' AND tipe = 2";
            $this->model('M_DB_1')->update('notif', $set, $where2);
         }
      }
   }

   public function sendNotif($countMember, $tipe)
   {
      $hp = $_POST['hp'];
      $noref = $_POST['ref'];
      $time =  $_POST['time'];
      $text = $_POST['text'];
      $idPelanggan = $_POST['idPelanggan'];
      $text = str_replace("<sup>2</sup>", "²", $text);
      $text = str_replace("<sup>3</sup>", "³", $text);

      if ($countMember > 0) {
         $textMember = $this->textSaldoNotif($idPelanggan);
         $text = $text . $textMember;
      }

      $res = $this->model("M_WA")->send($hp, $text, $this->dLaundry['notif_token']);
      $setOne = "no_ref = '" . $noref . "' AND tipe = 1";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);

      if (isset($res["id"])) {
         $cols =  'insertTime, id_cabang, no_ref, phone, text, tipe, id_api, proses';
         foreach ($res["id"] as $k => $v) {
            $status = $res["process"];
            $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "'," . $tipe . ",'" . $v . "','" . $status . "'";
         }
      } else {
         $cols =  'insertTime, id_cabang, no_ref, phone, text, tipe, token';
         $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "'," . $tipe . ", '" . $this->dLaundry['notif_token'] . "'";
      }

      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
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
      $this->model('M_DB_1')->update($this->table, $set, $where);

      echo $text;
   }

   public function textSaldoNotif($idPelanggan)
   {
      $textSaldo = "";
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $idPelanggan . " GROUP BY id_harga";
      $cols = "id_harga, SUM(qty) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('member', $cols, $where, 1);

      foreach ($data as $a) {
         $saldoPengurangan = 0;
         $idHarga = $a['id_harga'];
         $where = $this->wCabang . " AND id_pelanggan = " . $idPelanggan . " AND id_harga = " . $idHarga . " AND member = 1";
         $cols = "SUM(qty) as saldo";
         $data2 = $this->model('M_DB_1')->get_cols_where('penjualan', $cols, $where, 0);

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
      $this->model('M_DB_1')->update($this->table, $set, $where);
   }

   public function hapusRef()
   {
      $ref = $_POST['ref'];
      $note = $_POST['note'];
      $setOne = "no_ref = '" . $ref . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 1, bin_note = '" . $note . "'";
      $this->model('M_DB_1')->update("penjualan", $set, $where);
   }

   public function restoreRef()
   {
      $ref = $_POST['ref'];
      $setOne = "no_ref = '" . $ref . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 0";
      $this->model('M_DB_1')->update("penjualan", $set, $where);
   }

   public function poin($pelanggan)
   {
      $where = $this->wCabang . " AND id_pelanggan = " . $pelanggan . " AND bin = 0 AND id_poin > 0";
      $data_main = $this->model('M_DB_1')->get_where('penjualan', $where);

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
      $data_member = $this->model('M_DB_1')->get_where('member', $where_m);
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
      $data_manual = $this->model('M_DB_1')->get_where('poin', $where);

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
