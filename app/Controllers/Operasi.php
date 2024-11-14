<?php

class Operasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function i($modeOperasi, $id_pelanggan)
   {
      $viewData = 'operasi/form_proses';
      $formData = array('id_pelanggan' => $id_pelanggan, 'mode' => $modeOperasi);
      switch ($modeOperasi) {
         case 0:
            //DALAM PROSES
            $data_operasi = ['title' => 'Operasi Order Proses'];
            break;
         case 1:
            //TUNTAS
            $data_operasi = ['title' => 'Operasi Order Tuntas'];
            break;
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData, $formData);
   }

   public function loadData($id_pelanggan, $mode = 0)
   {
      $formData = [];
      $data_main = [];
      $idOperan = "";
      $modeView = 1;

      $pelanggan = [];

      foreach ($this->pelanggan as $c) {
         if ($c['id_pelanggan'] == $id_pelanggan) {
            $pelanggan = $c;
         }
      }

      if ($mode == 1) {
         $where = $this->wCabang . " AND id_pelanggan = $id_pelanggan AND bin = 0 AND tuntas = " . $mode . " ORDER BY id_penjualan DESC";
         $modeView = 2;
      } else {
         $where = $this->wCabang . " AND id_pelanggan = $id_pelanggan AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      }
      $data_main = $this->db($_SESSION['user']['book'])->get_where('sale', $where);

      $viewData = 'operasi/view_load';

      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_unique(array_column($data_main, 'no_ref'));

      $operasi = [];
      $kas = [];
      $surcas = [];
      $notifBon = [];
      $notifSelesai = [];

      foreach ($numbers as $id) {

         //OPERASI
         $where = $this->wCabang . " AND id_penjualan = " . $id;
         $ops = $this->db($_SESSION['user']['book'])->get_where('operasi', $where);
         if (count($ops) > 0) {
            foreach ($ops as $opsv) {
               array_push($operasi, $opsv);
            }
         }
         $ops = $this->db($_SESSION['user']['book'] + 1)->get_where('operasi', $where);
         if (count($ops) > 0) {
            foreach ($ops as $opsv) {
               array_push($operasi, $opsv);
            }
         }

         //NOTIF SELESAI
         $where = $this->wCabang . " AND tipe = 2 AND no_ref = '" . $id . "'";
         $ns = $this->db($_SESSION['user']['book'])->get_where_row("notif", $where);
         if (count($ns) > 0) {
            array_push($notifSelesai, $ns);
         }
         $ns = $this->db($_SESSION['user']['book'] + 1)->get_where_row("notif", $where);
         if (count($ns) > 0) {
            array_push($notifSelesai, $ns);
         }
      }

      foreach ($refs as $rf) {
         //KAS
         $where = $this->wCabang . " AND jenis_transaksi = 1 AND ref_transaksi = '" . $rf . "'";
         $ks = $this->db($_SESSION['user']['book'])->get_where('kas', $where);
         if (count($ks) > 0) {
            foreach ($ks as $ksv) {
               array_push($kas, $ksv);
            }
         }
         $ks = $this->db($_SESSION['user']['book'] + 1)->get_where('kas', $where);
         if (count($ks) > 0) {
            foreach ($ks as $ksv) {
               array_push($kas, $ksv);
            }
         }

         //SURCAS
         $where = $this->wCabang . " AND no_ref = '" . $rf . "'";
         $sc = $this->db(0)->get_where('surcas', $where);
         if (count($sc) > 0) {
            foreach ($sc as $scv) {
               array_push($surcas, $scv);
            }
         }

         //NOTIF BON
         $where = $this->wCabang . " AND tipe = 1 AND no_ref = '" . $rf . "'";
         $nf = $this->db($_SESSION['user']['book'])->get_where_row("notif", $where);
         if (count($nf) > 0) {
            array_push($notifBon, $nf);
         }
         $nf = $this->db($_SESSION['user']['book'] + 1)->get_where_row("notif", $where);
         if (count($nf) > 0) {
            array_push($notifBon, $nf);
         }
      }

      //MEMBER
      $data_member = [];
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $id_pelanggan;
      $order = "id_member DESC LIMIT 12";
      $data_member = $this->db(0)->get_where_order('member', $where, $order);

      $notif_member = [];
      $kas_member = [];
      foreach ($data_member as $dme) {

         //KAS
         $where = $this->wCabang . " AND jenis_transaksi = 3 AND ref_transaksi = '" . $dme['id_member'] . "'";
         $km = $this->db($_SESSION['user']['book'])->get_where_row('kas', $where);
         if (count($km) > 0) {
            array_push($kas_member, $km);
         }
         $km = $this->db($_SESSION['user']['book'] + 1)->get_where_row('kas', $where);
         if (count($km) > 0) {
            array_push($kas_member, $km);
         }

         //NOTIF MEMBER
         $where = $this->wCabang . " AND tipe = 3 AND no_ref = '" . $dme['id_member'] . "'";
         $nm = $this->db($_SESSION['user']['book'])->get_where_row("notif", $where);
         if (count($nm) > 0) {
            array_push($notif_member, $nm);
         }
         $nm = $this->db($_SESSION['user']['book'] + 1)->get_where_row("notif", $where);
         if (count($nm) > 0) {
            array_push($notif_member, $nm);
         }
      }

      //SALDO DEPOSIT
      $sisaSaldo = $this->data('Saldo')->getSaldoTunai($id_pelanggan);

      $this->view($viewData, [
         'modeView' => $modeView,
         'pelanggan' => $pelanggan,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'notif_bon' => $notifBon,
         'notif_selesai' => $notifSelesai,
         'notif_member' => $notif_member,
         'formData' => $formData,
         'idOperan' => $idOperan,
         "surcas" => $surcas,
         'data_member' => $data_member,
         'kas_member' => $kas_member,
         'saldoTunai' => $sisaSaldo
      ]);
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
               $note = "Saldo";
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

         if ($jumlah == 0) {
            continue;
         }

         $ref = substr($xNoref, 2);
         $tipe = substr($xNoref, 0, 1);

         if ($dibayar < $jumlah) {
            $jumlah = $dibayar;
         }

         $jenis_mutasi = 1;
         if ($metode == 3) {
            $sisaSaldo = $this->data('Saldo')->getSaldoTunai($idPelanggan);
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

         $jt = $tipe == "M" ? 3 : 1;
         $vals = $this->id_cabang . ", " . $jenis_mutasi . ", " . $jt . ",'" . $ref . "'," . $metode . ",'" . $note . "'," . $status_mutasi . "," . $jumlah . "," . $karyawan . "," . $idPelanggan . ",'" . $ref_f . "', '" . $GLOBALS['now'] . "'";

         $setOne = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND insertTime LIKE '%" . $minute . "%'";
         $where = $this->wCabang . " AND " . $setOne;
         $data_main = $this->db(date('Y'))->count_where('kas', $where);
         if ($data_main < 1) {
            $do = $this->db(date('Y'))->insertCols('kas', $cols, $vals);
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
      $in = $this->db($_SESSION['user']['book'])->update('operasi', $set, $where);
      if ($in['errno'] <> 0) {
         echo $in['error'];
      } else {
         echo 0;
      }
   }
}
