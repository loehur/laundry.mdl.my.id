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
      $operasi = array();
      $kas = array();
      $notif = array();
      $notifPenjualan = array();
      $dataTanggal = array();
      $data_main = array();
      $idOperan = "";
      $data_terima = array();
      $data_kembali = array();
      $pelanggan = array();
      $surcas = array();

      switch ($antrian) {
         case 0:
            //DALAM PROSES ALL
            $data_operasi = ['title' => 'Data Order Proses ALL'];
            $viewData = 'antrian/view';
            break;
         case 1:
            //DALAM PROSES 10 HARI
            $data_operasi = ['title' => 'Data Order Proses H7-'];
            $viewData = 'antrian/view';
            break;
         case 2:
            //TUNTAS
            $data_operasi = ['title' => 'Data Order Tuntas'];
            $viewData = 'antrian/view';
            break;
         case 5;
            //KINERJA
            if (isset($_POST['m'])) {
               $date = $_POST['Y'] . "-" . $_POST['m'];
               $dataTanggal = array('bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
            } else {
               $date = date('Y-m');
            }
            $table = "operasi";
            $tb_join = $this->table;
            $join_where = "operasi.id_penjualan = penjualan.id_penjualan";
            $where = "operasi.id_laundry = " . $this->id_laundry . " AND penjualan.bin = 0 AND operasi.insertTime LIKE '" . $date . "%'";
            $data_operasi = ['title' => 'Kinerja'];
            $data_main = $this->model('M_DB_1')->innerJoin1_where($table, $tb_join, $join_where, $where);

            $cols = "id_user, id_cabang, COUNT(id_user) as terima";
            $where = $this->wLaundry . " AND insertTime LIKE '" . $date . "%' GROUP BY id_user, id_cabang";
            $data_terima = $this->model('M_DB_1')->get_cols_where($this->table, $cols, $where, 1);

            $cols = "id_user_ambil, id_cabang, COUNT(id_user_ambil) as kembali";
            $where = $this->wLaundry . " AND tgl_ambil LIKE '" . $date . "%' GROUP BY id_user_ambil, id_cabang";
            $data_kembali = $this->model('M_DB_1')->get_cols_where($this->table, $cols, $where, 1);
            $viewData = 'antrian/kinerja';
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
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);

      $this->view($viewData, [
         'pelanggan' => $pelanggan,
         'modeView' => $antrian,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'notif' => $notif,
         'notif_penjualan' => $notifPenjualan,
         'dataTanggal' => $dataTanggal,
         'idOperan' => $idOperan,
         'dTerima' => $data_terima,
         'dKembali' => $data_kembali,
         'surcas' => $surcas
      ]);
   }

   public function load($antrian)
   {
      $operasi = array();
      $kas = array();
      $notif = array();
      $notifPenjualan = array();
      $dataTanggal = array();
      $data_main = array();
      $idOperan = "";
      $data_terima = array();
      $data_kembali = array();
      $pelanggan = array();
      $surcas = array();

      switch ($antrian) {
         case 0:
            //DALAM PROSES ALL
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
            $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
            $viewData = 'antrian/view_load';
            break;
         case 1:
            //DALAM PROSES 10 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) <= (insertTime + INTERVAL 7 DAY) ORDER BY id_penjualan DESC";
            $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
            $viewData = 'antrian/view_load';
            break;
         case 2:
            $viewData = 'antrian/view_load';
            break;
         case 6:
            //DALAM PROSES > 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 7 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 30 DAY) ORDER BY id_penjualan DESC";
            $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
            $viewData = 'antrian/view_load';
            break;
         case 7:
            //DALAM PROSES > 30 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 30 DAY) ORDER BY id_penjualan DESC";
            $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
            $viewData = 'antrian/view_load';
            break;
      }

      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_column($data_main, 'no_ref');

      if ($antrian == 0 || $antrian == 1 || $antrian == 2 || $antrian == 6 || $antrian == 7) {
         if (count($numbers) > 0) {
            $min = min($numbers);
            $max = max($numbers);
            $where = $this->wLaundry . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
            $operasi = $this->model('M_DB_1')->get_where('operasi', $where);

            //NOTIF SELESAI
            $where = $this->wCabang . " AND tipe = 2 AND no_ref BETWEEN " . $min . " AND " . $max;
            $notifPenjualan = $this->model('M_DB_1')->get_where('notif', $where);
         }
         if (count($refs) > 0) {
            //KAS
            $min_ref = min($refs);
            $max_ref = max($refs);
            $where = $this->wCabang . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
            $kas = $this->model('M_DB_1')->get_where('kas', $where);

            //NOTIF BON
            $where = $this->wCabang . " AND tipe = 1 AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
            $notif = $this->model('M_DB_1')->get_where('notif', $where);

            //SURCAS
            $where = $this->wCabang . " AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
            $surcas = $this->model('M_DB_1')->get_where('surcas', $where);
         }
      } elseif ($antrian == 3) {
         if (count($numbers) > 0) {
            $min = min($numbers);
            $max = max($numbers);
            $where = $this->wLaundry . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
            $operasi = $this->model('M_DB_1')->get_where('operasi', $where);
         }
      }

      $this->view($viewData, [
         'pelanggan' => $pelanggan,
         'modeView' => $antrian,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'notif' => $notif,
         'notif_penjualan' => $notifPenjualan,
         'dataTanggal' => $dataTanggal,
         'idOperan' => $idOperan,
         'dTerima' => $data_terima,
         'dKembali' => $data_kembali,
         'surcas' => $surcas
      ]);
   }

   public function loadTuntas($getPelanggan, $getTahun)
   {
      $operasi = array();
      $kas = array();
      $notif = array();
      $notifPenjualan = array();
      $dataTanggal = array();
      $data_main = array();
      $idOperan = "";
      $data_terima = array();
      $data_kembali = array();
      $pelanggan = array();
      $surcas = array();

      $thisMonth = $getTahun;
      $dataTanggal = array('tahun' => $getTahun);
      $pelanggan = $getPelanggan;
      $where = $this->wCabang . " AND id_pelanggan = $pelanggan AND bin = 0 AND tuntas = 1 AND insertTime LIKE '" . $thisMonth . "%' ORDER BY id_penjualan DESC";
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);

      $viewData = 'antrian/view_load';

      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_column($data_main, 'no_ref');

      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wLaundry . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->model('M_DB_1')->get_where('operasi', $where);

         //NOTIF SELESAI
         $where = $this->wCabang . " AND tipe = 2 AND no_ref BETWEEN " . $min . " AND " . $max;
         $notifPenjualan = $this->model('M_DB_1')->get_where('notif', $where);
      }
      if (count($refs) > 0) {
         //KAS
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = $this->wCabang . " AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $kas = $this->model('M_DB_1')->get_where('kas', $where);

         //NOTIF BON
         $where = $this->wCabang . " AND tipe = 1 AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $notif = $this->model('M_DB_1')->get_where('notif', $where);

         //SURCAS
         $where = $this->wCabang . " AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $surcas = $this->model('M_DB_1')->get_where('surcas', $where);
      }

      $this->view($viewData, [
         'pelanggan' => $pelanggan,
         'modeView' => 2,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'notif' => $notif,
         'notif_penjualan' => $notifPenjualan,
         'dataTanggal' => $dataTanggal,
         'idOperan' => $idOperan,
         'dTerima' => $data_terima,
         'dKembali' => $data_kembali,
         'surcas' => $surcas
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
      $cols = 'id_laundry, id_cabang, id_penjualan, jenis_operasi, id_user_operasi';
      $vals = $this->id_laundry . "," . $this->id_cabang . "," . $penjualan . "," . $operasi . "," . $karyawan;
      $setOne = 'id_penjualan = ' . $penjualan . " AND jenis_operasi =" . $operasi;
      $where = $this->wCabang . " AND " . $setOne;

      $data_main = $this->model('M_DB_1')->count_where('operasi', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('operasi', $cols, $vals);
      }

      //INSERT NOTIF SELESAI TAPI NOT READY
      $hp = $_POST['hp'];
      $text = $_POST['text'];
      $mode = $_POST['mode'];
      $time = date('Y-m-d H:i:s');

      $cols = 'insertTime, notif_token, id_cabang, no_ref, phone, text, mode, status, tipe';
      $vals = "'" . $time . "','" . $this->dLaundry['notif_token'] . "'," . $this->id_cabang . "," . $penjualan . ",'" . $hp . "','" . $text . "'," . $mode . ",5,2";
      $setOne = "no_ref = '" . $penjualan . "' AND tipe = 2";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
      }

      if (isset($_POST['rak'])) {
         if (strlen($_POST['rak']) > 0) {
            $rak = $_POST['rak'];
            $set = "letak = '" . $rak . "'";
            $where = $this->wCabang . " AND id_penjualan = " . $penjualan;
            $this->model('M_DB_1')->update($this->table, $set, $where);

            //CEK SUDAH TERKIRIM BELUM
            $setOne = "no_ref = '" . $penjualan . "' AND status = 2 AND tipe = 2";
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

   public function operasiOperan()
   {

      $hp = $_POST['hp'];
      $text = $_POST['text'];
      $karyawan = $_POST['f1'];
      $penjualan = $_POST['f2'];
      $operasi = $_POST['f3'];
      $idCabang = $_POST['idCabang'];

      if ($idCabang == 0 || strlen($hp) == 0) {
         exit;
      };

      $cols = 'id_laundry, id_cabang, id_penjualan, jenis_operasi, id_user_operasi';
      $vals = $this->id_laundry . "," . $idCabang . "," . $penjualan . "," . $operasi . "," . $karyawan;
      $setOne = 'id_penjualan = ' . $penjualan . " AND jenis_operasi = " . $operasi;
      $where = "id_cabang = " . $idCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('operasi', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('operasi', $cols, $vals);
      }

      //INSERT NOTIF SELESAI TAPI NOT READY
      $mode = $_POST['mode'];
      $time = date('Y-m-d H:i:s');
      $cols = 'insertTime, notif_token, id_cabang, no_ref, phone, text, mode, status, tipe';
      $vals = "'" . $time . "','" . $this->dLaundry['notif_token'] . "'," . $idCabang . "," . $penjualan . ",'" . $hp . "','" . $text . "'," . $mode . ",5,2";

      $setOne = "no_ref = '" . $penjualan . "' AND tipe = 2";
      $where = "id_cabang = " . $idCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('notif', $cols, $vals);
      }
   }

   public function updateRak()
   {
      $rak = $_POST['value'];
      $id = $_POST['id'];

      $set = "letak = '" . $rak . "'";
      $where = $this->wCabang . " AND id_penjualan = " . $id;
      $this->model('M_DB_1')->update($this->table, $set, $where);

      //CEK SUDAH TERKIRIM BELUM
      $setOne = "no_ref = '" . $id . "' AND tipe = 2 AND status = 2";
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
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);
      if ($data_main > 0) {
         $set = "status = 1";
         $where = $this->wCabang . " AND no_ref = '" . $idPenjualan . "' AND tipe = 2";
         $this->model('M_DB_1')->update('notif', $set, $where);
      }
   }

   public function bayar()
   {
      $maxBayar = $_POST['maxBayar'];
      $jumlah = $_POST['f1'];

      if ($jumlah > $maxBayar) {
         $jumlah = $maxBayar;
      }

      $karyawan = $_POST['f2'];
      $ref = $_POST['f3'];
      $metode = $_POST['f4'];
      $idPelanggan = $_POST['idPelanggan'];
      $note = $_POST['noteBayar'];

      if (strlen($note) == 0 && $metode == 2) {
         $note = "Non_Tunai";
      }

      $status_mutasi = 3;
      switch ($metode) {
         case "2":
            $status_mutasi = 2;
            break;
         default:
            $status_mutasi = 3;
            break;
      }

      if ($this->id_privilege == 100 || $this->id_privilege == 101) {
         $status_mutasi = 3;
      }

      $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, ref_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client';
      $vals = $this->id_cabang . ", 1, 1,'" . $ref . "'," . $metode . ",'" . $note . "'," . $status_mutasi . "," . $jumlah . "," . $karyawan . "," . $idPelanggan;

      $setOne = 'ref_transaksi = ' . $ref . ' AND jumlah = ' . $jumlah;
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('kas', $where);
      if ($data_main < 1) {
         $this->model('M_DB_1')->insertCols('kas', $cols, $vals);
      }
   }

   public function sendNotif($countMember, $tipe)
   {
      $hp = $_POST['hp'];
      $mode = $_POST['mode'];
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

      $cols =  'insertTime, notif_token, id_cabang, no_ref, phone, text, mode, tipe';
      $vals = "'" . $time . "','" . $this->dLaundry['notif_token'] . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "'," . $mode . "," . $tipe;

      $setOne = "no_ref = '" . $noref . "' AND tipe = 1";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('notif', $where);
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
      $setOne = "no_ref = '" . $ref . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 1";
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

   public function poin()
   {
      $pelanggan = $_POST['id'];
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

      $where = $this->wCabang . " AND id_pelanggan = " . $pelanggan;
      $data_manual = $this->model('M_DB_1')->get_where('poin', $where);

      $arrPoinManual = array();
      $arrPoinManual = array_column($data_manual, 'poin_jumlah');
      $totalPoinManual = array_sum($arrPoinManual);
      $totalPoin = $totalPoinPenjualan + $totalPoinManual;
      return $totalPoin;
   }

   public function getPoin()
   {
      echo $this->poin();
   }
}
