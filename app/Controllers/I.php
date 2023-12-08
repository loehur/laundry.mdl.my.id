<?php

class I extends Controller
{
   private $host;
   public function __construct()
   {
      $this->table = 'penjualan';
      $this->host = "laundry.mdl.my.id";
   }

   public function i($idLaundry, $pelanggan)
   {
      $this->public_data($idLaundry, $pelanggan);

      $operasi = array();
      $kas = array();
      $data_main = array();
      $data_terima = array();
      $data_kembali = array();
      $surcas = array();

      $data_tanggal = array();
      if (isset($_POST['Y'])) {
         $data_tanggal = array('bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
      }

      if (count($data_tanggal) > 0) {
         $bulannya = $data_tanggal['tahun'] . "-" . $data_tanggal['bulan'];
         $where = $this->wLaundry . " AND id_pelanggan = " . $pelanggan . " AND insertTime LIKE '" . $bulannya . "%' AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      } else {
         $where = $this->wLaundry . " AND id_pelanggan = " . $pelanggan . " AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      }

      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);
      $where2 = "id_pelanggan = " . $pelanggan . " AND bin = 0 GROUP BY id_harga";
      $list_paket = $this->model('M_DB_1')->get_where("member", $where2);

      $viewData = 'invoice/invoice_main';
      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_column($data_main, 'no_ref');

      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);
         $where = $this->wLaundry . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->model('M_DB_1')->get_where('operasi', $where);
      }
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = "jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $kas = $this->model('M_DB_1')->get_where('kas', $where);

         //SURCAS
         $where = "no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $surcas = $this->model('M_DB_1')->get_where('surcas', $where);
      }

      $data_member = array();
      $where = "bin = 0 AND id_pelanggan = " . $pelanggan;
      $order = "id_member DESC";
      $data_member = $this->model('M_DB_1')->get_where_order('member', $where, $order);

      $numbersMember = array();
      $kasM = array();
      if (count($data_member) > 0) {
         $numbersMember = array_column($data_member, 'id_member');
         $min = min($numbersMember);
         $max = max($numbersMember);
         $where = "jenis_transaksi = 3 AND (ref_transaksi BETWEEN " . $min . " AND " . $max . ")";
         $kasM = $this->model('M_DB_1')->get_where('kas', $where);

         foreach ($data_member as $key => $value) {
            $lunasNya = false;
            $totalNya = $value['harga'];
            $akumBayar = 0;
            foreach ($kasM as $ck) {
               if ($value['id_member'] == $ck['ref_transaksi']) {
                  $akumBayar += $ck['jumlah'];
                  break;
               }
            }
            if ($akumBayar >= $totalNya) {
               $lunasNya = true;
            }
            if ($lunasNya == true) {
               unset($data_member[$key]);
            }
         }
      }

      $saldoTunai = 0;
      $saldoTunai = $this->getSaldoTunai($pelanggan);

      $this->view($viewData, [
         'pelanggan' => $pelanggan,
         'dataTanggal' => $data_tanggal,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'kasM' => $kasM,
         'dTerima' => $data_terima,
         'dKembali' => $data_kembali,
         'listPaket' => $list_paket,
         'laundry' => $idLaundry,
         'data_member' => $data_member,
         'surcas' => $surcas,
         'saldoTunai' => $saldoTunai
      ]);
   }

   public function m($idLaundry, $pelanggan, $id_harga)
   {
      $this->public_data($idLaundry, $pelanggan);
      $data_main = array();

      $where = $this->wLaundry . " AND id_pelanggan = " . $pelanggan . " AND id_harga = $id_harga AND bin = 0 AND member = 1 ORDER BY insertTime ASC";
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);

      $where2 = "id_pelanggan = " . $pelanggan . " AND id_harga = $id_harga AND bin = 0 ORDER BY insertTime ASC";
      $data_main2 = $this->model('M_DB_1')->get_where("member", $where2);
      $viewData = 'member/member_history';

      $this->view($viewData, [
         'pelanggan' => $pelanggan,
         'data_main' => $data_main,
         'data_main2' => $data_main2,
         'id_harga' => $id_harga,
         'laundry' => $idLaundry
      ]);
   }

   public function s($idLaundry, $pelanggan)
   {
      $this->public_data($idLaundry, $pelanggan);
      $data = array();
      $where = "id_client = " . $pelanggan . " AND status_mutasi = 3 AND ((jenis_transaksi = 6 AND jenis_mutasi = 1) OR (jenis_transaksi = 1 AND jenis_mutasi = 2) OR (jenis_transaksi = 3 AND jenis_mutasi = 2))";
      $cols = "id_kas, id_client, jumlah, metode_mutasi, note, insertTime, jenis_mutasi, jenis_transaksi";
      $data = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 1);

      $saldo = 0;
      foreach ($data as $key => $v) {
         if ($v['jenis_mutasi'] == 1) {
            $saldo += $v['jumlah'];
         } else {
            $saldo -= $v['jumlah'];
         }
         $data[$key]['saldo'] = $saldo;
      }

      $viewData = 'saldoTunai/member_history';

      $this->view($viewData, [
         'pelanggan' => $pelanggan,
         'data_main' => $data,
         'laundry' => $idLaundry
      ]);
   }

   function getSaldoTunai($pelanggan)
   {
      //SALDO TUNAI
      $saldo = 0;
      $pakai = 0;

      //Kredit
      $where = "id_client = " . $pelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 AND status_mutasi = 3 GROUP BY id_client ORDER BY saldo DESC";
      $cols = "id_client, SUM(jumlah) as saldo";
      $data = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 1);

      //Debit
      if (count($data) > 0) {
         foreach ($data as $a) {
            $idPelanggan = $a['id_client'];
            $saldo = $a['saldo'];
            $where = $this->wCabang . " AND id_client = " . $idPelanggan . " AND metode_mutasi = 3 AND jenis_mutasi = 2";
            $cols = "SUM(jumlah) as pakai";
            $data2 = $this->model('M_DB_1')->get_cols_where('kas', $cols, $where, 0);
            if (isset($data2['pakai'])) {
               $pakai = $data2['pakai'];
            }
         }
      }

      $sisaSaldo = $saldo - $pakai;
      return $sisaSaldo;
   }

   function q($id_laundry)
   {
      $d = $this->model('M_DB_1')->get_cols_where("laundry", "qris_path", "id_laundry = " . $id_laundry, 0);
      echo "<img style='max-width:100vw' src='" . $this->BASE_URL . $d['qris_path'] . "'>";
   }
}
