<?php

class Operasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function i($modeOperasi, $id_pelanggan, $getTahun = 0)
   {
      switch ($modeOperasi) {
         case 1:
            //DALAM PROSES
            $data_operasi = ['title' => 'Operasi Order Proses'];
            $viewData = 'operasi/form_proses';
            $formData = array('id_pelanggan' => $id_pelanggan);
            break;
         case 2:
            //TUNTAS
            $data_operasi = ['title' => 'Operasi Order Tuntas'];
            $viewData = 'operasi/form_tuntas';
            $formData = array('tahun' => $getTahun, 'id_pelanggan' => $id_pelanggan);
            break;
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData, ['formData' => $formData]);
   }

   public function loadData($id_pelanggan, $getTahun)
   {
      $operasi = [];
      $kas = [];
      $notif = [];
      $notifPenjualan = [];
      $formData = [];
      $data_main = [];
      $idOperan = "";
      $surcas = [];
      $modeView = 1;

      $thisMonth = $getTahun;
      $pelanggan = [];

      foreach ($this->pelanggan as $c) {
         if ($c['id_pelanggan'] == $id_pelanggan) {
            $pelanggan = $c;
         }
      }

      if ($getTahun <> 0) {
         $where = $this->wCabang . " AND id_pelanggan = $id_pelanggan AND bin = 0 AND tuntas = 1 AND insertTime LIKE '" . $thisMonth . "%' ORDER BY id_penjualan DESC";
         $modeView = 2;
      } else {
         $where = $this->wCabang . " AND id_pelanggan = $id_pelanggan AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      }
      $data_main = $this->db(1)->get_where('sale_' . $this->id_cabang, $where);

      $viewData = 'operasi/view_load';
      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_column($data_main, 'no_ref');

      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);

         //OPERASI
         $where = $this->wCabang . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->db(1)->get_where('operasi', $where);

         //NOTIF SELESAI
         $where = $this->wCabang . " AND tipe = 2 AND no_ref BETWEEN " . $min . " AND " . $max;
         $notifPenjualan = $this->db(1)->get_where('notif_' . $this->id_cabang, $where);
      }
      if (count($refs) > 0) {
         //KAS
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = $this->wCabang . " AND jenis_transaksi = 1 AND (id_client = " . $id_pelanggan . " OR id_client = 0) AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $kas = $this->db(1)->get_where('kas', $where);

         //NOTIF BON
         $where = $this->wCabang . " AND tipe = 1 AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $notif = $this->db(1)->get_where('notif_' . $this->id_cabang, $where);

         //SURCAS
         $where = $this->wCabang . " AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $surcas = $this->db(0)->get_where('surcas', $where);
      }

      //MEMBER
      $data_member = [];
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $id_pelanggan;
      $order = "id_member DESC LIMIT 12";
      $data_member = $this->db(1)->get_where_order('member', $where, $order);
      $notif_member = [];

      $kas_member = [];
      if (count($data_member) > 0) {
         $numbers = array_column($data_member, 'id_member');
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wCabang . " AND jenis_transaksi = 3 AND (ref_transaksi BETWEEN " . $min . " AND " . $max . ")";
         $kas_member = $this->db(1)->get_where('kas', $where);

         //NOTIF MEMBER
         $where = $this->wCabang . " AND tipe = 3 AND no_ref BETWEEN " . $min . " AND " . $max;
         $notif_member = $this->db(1)->get_where('notif_' . $this->id_cabang, $where);
      }

      //SALDO TUNAI
      $sisaSaldo = $this->getSaldoTunai($id_pelanggan);

      $this->view($viewData, [
         'modeView' => $modeView,
         'pelanggan' => $pelanggan,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'notif_bon' => $notif,
         'notif_selesai' => $notifPenjualan,
         'notif_member' => $notif_member,
         'formData' => $formData,
         'idOperan' => $idOperan,
         'surcas' => $surcas,
         'data_member' => $data_member,
         'kas_member' => $kas_member,
         'saldoTunai' => $sisaSaldo
      ]);
   }

   function getSaldoTunai($id_pelanggan)
   {
      //SALDO TUNAI
      $saldo = 0;
      $pakai = 0;

      //Kredit
      $where = $this->wCabang . " AND id_client = " . $id_pelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 AND status_mutasi = 3 GROUP BY id_client ORDER BY saldo DESC";
      $cols = "id_client, SUM(jumlah) as saldo";
      $data = $this->db(1)->get_cols_where('kas', $cols, $where, 1);

      //Kredit
      $where2 = $this->wCabang . " AND id_client = " . $id_pelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 2 AND status_mutasi = 3 GROUP BY id_client ORDER BY saldo DESC";
      $cols = "id_client, SUM(jumlah) as saldo";
      $data3 = $this->db(1)->get_cols_where('kas', $cols, $where2, 1);

      //Debit
      if (count($data) > 0) {
         foreach ($data as $a) {
            $idPelanggan = $a['id_client'];
            $saldo = $a['saldo'];
            $where = $this->wCabang . " AND id_client = " . $idPelanggan . " AND metode_mutasi = 3 AND jenis_mutasi = 2";
            $cols = "SUM(jumlah) as pakai";
            $data2 = $this->db(1)->get_cols_where('kas', $cols, $where, 0);
            if (isset($data2['pakai'])) {
               $pakai += $data2['pakai'];
            }
         }
      }

      if (count($data3) > 0) {
         foreach ($data3 as $a3) {
            $idPelanggan = $a3['id_client'];
            $pakai += $a3['saldo'];
         }
      }

      $sisaSaldo = $saldo - $pakai;
      return $sisaSaldo;
   }

   public function bayarMulti($karyawan, $idPelanggan, $metode, $note)
   {
      $minute = date('Y-m-d H:');

      $data = $_POST['rekap'][0];
      if (count($data) == 0) {
         exit();
      }

      $dibayar = $_POST['dibayar'];
      $note = str_replace("_SPACE_", " ", $note);

      if (strlen($note) == 0) {
         switch ($metode) {
            case 2:
               $note = "Non_Tunai";
               break;
            case 3:
               $note = "Saldo_Tunai";
               break;
            default:
               $note = "";
               break;
         }
      }

      ksort($data);
      $ref_f = date('YmdHis') . $this->id_cabang;

      $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, ref_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client, ref_finance, insertTime';
      foreach ($data as $key => $value) {
         if ($dibayar == 0) {
            exit();
         }

         $xNoref = $key;
         $jumlah = $value;
         $ref = substr($xNoref, 2);
         $tipe = substr($xNoref, 0, 1);

         if ($dibayar < $jumlah) {
            $jumlah = $dibayar;
         }

         $jenis_mutasi = 1;
         if ($metode == 3) {
            $sisaSaldo = $this->getSaldoTunai($idPelanggan);
            if ($sisaSaldo > 0) {
               if ($jumlah > $sisaSaldo) {
                  $jumlah = $sisaSaldo;
               }
            } else {
               exit();
            }
            $jenis_mutasi = 2;
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

         if ($this->id_privilege == 100) {
            $status_mutasi = 3;
         }

         $jt = $tipe == "M" ? 3 : 1;
         $vals = $this->id_cabang . ", " . $jenis_mutasi . ", " . $jt . ",'" . $ref . "'," . $metode . ",'" . $note . "'," . $status_mutasi . "," . $jumlah . "," . $karyawan . "," . $idPelanggan . ",'" . $ref_f . "', '" . $GLOBALS['now'] . "'";

         echo $ref . " " . $tipe . " " . $jenis_mutasi . " " . $metode . " " . $status_mutasi;
         continue;

         $setOne = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND insertTime LIKE '%" . $minute . "%'";
         $where = $this->wCabang . " AND " . $setOne;
         $data_main = $this->db(1)->count_where('kas', $where);
         if ($data_main < 1) {
            $do = $this->db(1)->insertCols('kas', $cols, $vals);
            $dibayar -= $jumlah;
            if ($do['errno'] <> 0) {
               print_r($do['error']);
               exit();
            }
         }
      }
      echo 0;
   }

   public function ganti_operasi()
   {
      $karyawan = $_POST['f1'];
      $id = $_POST['id'];

      $set = "id_user_operasi = '" . $karyawan . "'";
      $where = $this->wCabang . " AND id_operasi = " . $id;
      $this->db(1)->update('operasi', $set, $where);
   }
}
