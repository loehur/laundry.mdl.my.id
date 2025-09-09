<?php

class Antrian extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function i($antrian)
   {
      $kas = [];
      $notif = [];
      $notifPenjualan = [];
      $data_main = [];
      $surcas = [];

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
         "notif" => $notif,
         'notif_penjualan' => $notifPenjualan,
         "surcas" => $surcas,
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
         "notif" => $notif,
         'notif_penjualan' => $notifPenjualan,
         "surcas" => $surcas,
      ]);
   }

   public function loadList($antrian)
   {
      $data_main = [];
      $viewData = 'antrian/view_content';
      switch ($antrian) {
         case 1:
            //DALAM PROSES 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) <= (insertTime + INTERVAL 7 DAY) ORDER BY id_penjualan DESC";
            break;
         case 6:
            //DALAM PROSES > 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 7 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 30 DAY) ORDER BY id_penjualan DESC";
            break;
         case 7:
            //DALAM PROSES > 30 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 30 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 365 DAY) ORDER BY id_penjualan DESC";
            break;
         case 8:
            //DALAM PROSES > 1 TAHUN
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND DATE(NOW()) > (insertTime + INTERVAL 365 DAY) ORDER BY id_penjualan DESC";
            break;
         case 100:
            //PIUTANG 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND id_user_ambil <> 0 AND DATE(NOW()) <= (insertTime + INTERVAL 7 DAY) ORDER BY id_penjualan ASC";
            break;
         case 101:
            //PIUTANG > 7 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND id_user_ambil <> 0 AND DATE(NOW()) > (insertTime + INTERVAL 7 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 30 DAY) ORDER BY id_penjualan ASC";
            break;
         case 102:
            //PIUTANG > 30 HARI
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND id_user_ambil <> 0 AND DATE(NOW()) > (insertTime + INTERVAL 30 DAY) AND DATE(NOW()) <= (insertTime + INTERVAL 365 DAY) ORDER BY id_penjualan ASC";
            break;
         case 103:
            //PIUTANG > 1 TAHUN
            $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 AND id_user_ambil <> 0 AND DATE(NOW()) > (insertTime + INTERVAL 365 DAY) ORDER BY id_penjualan ASC";
            break;
      }

      if ($_SESSION[URL::SESSID]['user']['book'] <> date('Y')) {
         $where = $this->wCabang . " AND id_pelanggan <> 0 AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      }

      $data_main = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_cols_where('sale', 'id_penjualan', $where, 1, 'id_penjualan');
      $data_main2 = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where('sale', $where, 'no_ref', 1);

      $numbers = array_keys($data_main);
      $refs = array_keys($data_main2);

      $operasi = [];
      $kas = [];
      $surcas = [];
      $notif = [];

      if (count($refs) > 0) {
         $ref_list = "";
         foreach ($refs as $r) {
            $ref_list .= $r . ",";
         }
         $ref_list = rtrim($ref_list, ',');

         $where = $this->wCabang . " AND jenis_transaksi = 1 AND ref_transaksi IN (" . $ref_list . ")";
         $kas1 = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where('kas', $where);
         $kas2 = $this->db($_SESSION[URL::SESSID]['user']['book'] + 1)->get_where('kas', $where);
         $kas = array_merge($kas1, $kas2);

         $where = $this->wCabang . " AND no_ref IN (" . $ref_list . ")";
         $surcas = $this->db(0)->get_where('surcas', $where);

         $where = $this->wCabang . " AND tipe = 1 AND no_ref IN (" . $ref_list . ")";
         $notif1 = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where('notif', $where);
         $notif2 = $this->db($_SESSION[URL::SESSID]['user']['book'] + 1)->get_where('notif', $where);
         $notif = array_merge($notif1, $notif2);
      }

      if (count($numbers) > 0) {
         $no_list = "";
         foreach ($numbers as $r) {
            $no_list .= $r . ",";
         }
         $no_list = rtrim($no_list, ',');

         //OPERASI
         $where = $this->wCabang . " AND id_penjualan IN (" . $no_list . ")";
         $operasi1 = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where('operasi', $where);
         $operasi2 = $this->db($_SESSION[URL::SESSID]['user']['book'] + 1)->get_where('operasi', $where);
         $operasi = array_merge($operasi1, $operasi2);
      }

      $karyawan = $this->userAll;

      $this->view($viewData, [
         'modeView' => $antrian,
         'data_main' => $data_main2,
         'operasi' => $operasi,
         'kas' => $kas,
         "surcas" => $surcas,
         'data_notif' => $notif,
         "karyawan" => $karyawan
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
      $users = $this->db(0)->get_where_row("user", "id_user = " . $karyawan);
      $nm_karyawan = $users['nama_user'];
      $karyawan_code = strtoupper(substr($nm_karyawan, 0, 2)) . substr($karyawan, -1);
      $hp = $_POST['hp'];
      $text = $_POST['text'];
      $totalNotif = $_POST['totalNotif'];
      $text = str_replace("|STAFF|", $karyawan_code, $text);

      print_r($text);
      exit();

      $penjualan = $_POST['f2'];
      $operasi = $_POST['f3'];
      $cols = 'id_cabang, id_penjualan, jenis_operasi, id_user_operasi, insertTime';
      $vals = $this->id_cabang . "," . $penjualan . "," . $operasi . "," . $karyawan . ",'" . $GLOBALS['now'] . "'";
      $setOne = 'id_penjualan = ' . $penjualan . " AND jenis_operasi =" . $operasi;
      $where = $this->wCabang . " AND " . $setOne;

      $data_main = $this->db(date('Y'))->count_where('operasi', $where);
      if ($data_main < 1) {
         $in = $this->db(date('Y'))->insertCols('operasi', $cols, $vals);
         if ($in['errno'] <> 0) {
            echo $in['error'];
            exit();
         }
      }

      //INSERT NOTIF SELESAI TAPI NOT READY
      $time = date('Y-m-d H:i:s');

      $cols = 'insertTime, id_cabang, no_ref, phone, text, status, tipe';
      $vals = "'" . $time . "'," . $this->id_cabang . "," . $penjualan . ",'" . $hp . "','" . $text . "',5,2";
      $setOne = "no_ref = '" . $penjualan . "' AND tipe = 2";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->db(date('Y'))->count_where('notif', $where);
      if ($data_main < 1) {
         $do = $this->db(date('Y'))->insertCols('notif', $cols, $vals);
         if ($do['errno'] <> 0) {
            $this->data('Notif')->send_wa(URL::WA_PRIVATE[0], $do['error']);
         }
      }

      if (isset($_POST['rak'])) {
         if (strlen($_POST['rak']) > 0) {
            $rak = $_POST['rak'];
            $pack = $_POST['pack'];
            $hanger = $_POST['hanger'];
            $set = "letak = '" . $rak . "', pack = " . $pack . ", hanger = " . $hanger;
            $where = $this->wCabang . " AND id_penjualan = " . $penjualan;
            $this->db($_SESSION[URL::SESSID]['user']['book'])->update('sale', $set, $where);

            //CEK SUDAH TERKIRIM BELUM
            $setOne = "no_ref = '" . $penjualan . "' AND proses <> '' AND tipe = 2";
            $where = $setOne;
            $data_main = $this->db(date('Y'))->count_where('notif', $where);
            if ($data_main < 1) {
               $this->notifReadySend($penjualan, $totalNotif);
            }
         }
      }

      echo 0;
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
         $in = $this->db(0)->insertCols('surcas', $cols, $vals);
         if ($in['errno'] <> 0) {
            echo $in['error'];
            exit();
         }
      }
      echo 0;
   }

   public function updateRak($mode = 0)
   {
      $rak = $_POST['value'];
      $id = $_POST['id'];
      $totalNotif = $_POST['totalNotif'];

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
      $this->db($_SESSION[URL::SESSID]['user']['book'])->update('sale', $set, $where);

      //CEK SUDAH TERKIRIM BELUM
      $setOne = "no_ref = '" . $id . "' AND proses <> '' AND tipe = 2";
      $where = $setOne;
      $data_main = $this->db(date('Y'))->count_where('notif', $where);
      if ($data_main < 1) {
         $this->notifReadySend($id, $totalNotif);
      }
   }

   public function tuntasOrder($ref)
   {
      $set = "tuntas = 1";
      $where = $this->wCabang . " AND no_ref = " . $ref;
      $this->db($_SESSION[URL::SESSID]['user']['book'])->update('sale', $set, $where);
   }

   public function notifReadySend($idPenjualan, $totalNotif = "")
   {
      $setOne = "no_ref = '" . $idPenjualan . "' AND tipe = 2";
      $where = $this->wCabang . " AND " . $setOne;
      $dm = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where_row('notif', $where);
      if (!isset($dm['phone'])) {
         $dm = $this->db($_SESSION[URL::SESSID]['user']['book'] + 1)->get_where_row('notif', $where);
      }
      $hp = $dm['phone'];
      $text = $dm['text'];
      $text = str_replace("|TOTAL|", "\n" . $totalNotif, $text);
      $res = $this->data('Notif')->send_wa($hp, $text, false);

      $where2 = $this->wCabang . " AND no_ref = '" . $idPenjualan . "' AND tipe = 2";
      if ($res['status']) {
         $status = $res['data']['status'];
         $set = "status = 1, proses = '" . $status . "', id_api = '" . $res['data']['id'] . "'";
         $this->db($_SESSION[URL::SESSID]['user']['book'])->update('notif', $set, $where2);
      } else {
         $status = $res['data']['status'];
         $set = "status = 4, proses = '" . $status . "'";
         $this->db($_SESSION[URL::SESSID]['user']['book'])->update('notif', $set, $where2);
      }
   }

   public function sendNotif($countMember, $tipe)
   {
      $id_harga = $_POST['id_harga'];
      $hp = $_POST['hp'];
      $noref = $_POST['ref'];
      $time =  $_POST['time'];
      $text = $_POST['text'];
      $idPelanggan = $_POST['idPelanggan'];
      $text = str_replace("<sup>2</sup>", "²", $text);
      $text = str_replace("<sup>3</sup>", "³", $text);

      if ($countMember > 0) {
         $textMember = $this->textSaldoNotif($idPelanggan, $id_harga);
         $text = $text . $textMember;
      }

      $res = $this->data("Notif")->send_wa($hp, $text, false);

      $setOne = "no_ref = '" . $noref . "' AND tipe = 1";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->db(date('Y'))->count_where('notif', $where);
      $cols =  'insertTime, id_cabang, no_ref, phone, text, tipe, id_api, proses';

      if ($res['status']) {
         $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "'," . $tipe . ",'" . $res['data']['id'] . "','" . $res['data']['status'] . "'";
      } else {
         $status = $res['data']['status'];
         $vals = "'" . $time . "'," . $this->id_cabang . ",'" . $noref . "','" . $hp . "','" . $text . "'," . $tipe . ",'','" . $status . "'";
      }

      if ($data_main < 1) {
         $do = $this->db(date('Y'))->insertCols('notif', $cols, $vals);

         echo $do['errno'] == 0 ? 0 : $do['error'];
      }
   }

   public function textSaldoNotif($idPelanggan, $id_harga)
   {
      $saldo_akhir = $this->data('Saldo')->saldoMember($idPelanggan, $id_harga);
      $unit = $this->data('Saldo')->unit_by_idHarga($id_harga);
      $textSaldo = "\nM" . $id_harga . " " . number_format($saldo_akhir, 2) . $unit;
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
      $up = $this->db($_SESSION[URL::SESSID]['user']['book'])->update('sale', $set, $where);
      echo $up['errno'] == 0 ? 0 : $up['error'];
   }

   public function hapusRef()
   {
      $ref = $_POST['ref'];
      $note = $_POST['note'];
      $setOne = "no_ref = '" . $ref . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 1, bin_note = '" . $note . "'";
      $this->db($_SESSION[URL::SESSID]['user']['book'])->update('sale', $set, $where);
   }

   public function restoreRef()
   {
      $ref = $_POST['ref'];
      $setOne = "no_ref = '" . $ref . "'";
      $where = $this->wCabang . " AND " . $setOne;
      $set = "bin = 0";
      $this->db($_SESSION[URL::SESSID]['user']['book'])->update('sale', $set, $where);
   }
}
