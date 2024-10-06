<?php

class I extends Controller
{
   public function i($pelanggan)
   {
      if (!is_numeric($pelanggan)) {
         exit();
      }
      $this->public_data($pelanggan);

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
         $where = "id_pelanggan = " . $pelanggan . " AND insertTime LIKE '" . $bulannya . "%' AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      } else {
         $where = "id_pelanggan = " . $pelanggan . " AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      }

      $data_main = $this->db(1)->get_where('sale_' . $this->id_cabang_p, $where);
      $where2 = "id_pelanggan = " . $pelanggan . " AND bin = 0 GROUP BY id_harga";
      $list_paket = $this->db(1)->get_where('member', $where2);

      $viewData = 'invoice/invoice_main';
      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_column($data_main, 'no_ref');

      if (count($numbers) > 0) {
         $min = min($numbers);
         $max = max($numbers);

         //OPERASI
         $where = "id_cabang = " . $this->id_cabang_p . " AND id_penjualan BETWEEN " . $min . " AND " . $max;
         $operasi = $this->db(1)->get_where('operasi', $where);
      }
      if (count($refs) > 0) {
         $min_ref = min($refs);
         $max_ref = max($refs);
         $where = "id_cabang = " . $this->id_cabang_p . "  AND jenis_transaksi = 1 AND (ref_transaksi BETWEEN " . $min_ref . " AND " . $max_ref . ")";
         $kas = $this->db(1)->get_where('kas', $where);

         //SURCAS
         $where = "id_cabang = " . $this->id_cabang_p . "  AND no_ref BETWEEN " . $min_ref . " AND " . $max_ref;
         $surcas = $this->db(0)->get_where('surcas', $where);
      }

      $data_member = array();
      $where = "id_cabang = " . $this->id_cabang_p . "  AND bin = 0 AND id_pelanggan = " . $pelanggan;
      $order = "id_member DESC";
      $data_member = $this->db(1)->get_where_order('member', $where, $order);

      $numbersMember = array();
      $kasM = array();
      if (count($data_member) > 0) {
         $numbersMember = array_column($data_member, 'id_member');

         foreach ($numbersMember as $nm) {
            $where = "id_cabang = " . $this->id_cabang_p . "  AND jenis_transaksi = 3 AND ref_transaksi = '" . $nm . "'";
            $kasMd = $this->db(1)->get_where('kas', $where);
            if (count($kasMd) > 0) {
               foreach ($kasMd as $ksmV) {
                  array_push($kasM, $ksmV);
               }
            }
         }

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
         'data_pelanggan' => $this->pelanggan_p,
         'dataTanggal' => $data_tanggal,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'kasM' => $kasM,
         'dTerima' => $data_terima,
         'dKembali' => $data_kembali,
         'listPaket' => $list_paket,
         'data_member' => $data_member,
         'surcas' => $surcas,
         'saldoTunai' => $saldoTunai,
      ]);
   }

   public function m($pelanggan, $id_harga)
   {
      if (!is_numeric($pelanggan)) {
         exit();
      }
      $this->public_data($pelanggan);
      $data_main = array();

      $where = "id_pelanggan = " . $pelanggan . " AND id_harga = $id_harga AND bin = 0 AND member = 1 ORDER BY insertTime ASC";
      $data_main = $this->db(1)->get_where('sale_' . $this->id_cabang_p, $where);

      $where2 = "id_pelanggan = " . $pelanggan . " AND id_harga = $id_harga AND bin = 0 ORDER BY insertTime ASC";
      $data_main2 = $this->db(1)->get_where('member', $where2);
      $viewData = 'member/member_history';

      $this->view($viewData, [
         'data_pelanggan' => $this->pelanggan_p,
         'data_main' => $data_main,
         'data_main2' => $data_main2,
         'id_harga' => $id_harga,
      ]);
   }

   public function s($pelanggan)
   {
      if (!is_numeric($pelanggan)) {
         exit();
      }
      $this->public_data($pelanggan);

      $data = array();
      $where = "id_client = " . $pelanggan . " AND status_mutasi = 3 AND ((jenis_transaksi = 1 AND metode_mutasi = 3) OR (jenis_transaksi = 3 AND metode_mutasi = 3) OR jenis_transaksi = 6)";
      $cols = "id_kas, id_client, jumlah, metode_mutasi, note, insertTime, jenis_mutasi, jenis_transaksi";
      $data = $this->db(1)->get_cols_where('kas', $cols, $where, 1);

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
         'data_pelanggan' => $this->pelanggan_p,
         'data_main' => $data,
      ]);
   }

   function getSaldoTunai($pelanggan)
   {
      if (!is_numeric($pelanggan)) {
         exit();
      }
      //SALDO DEPOSIT
      $saldo = 0;
      $pakai = 0;

      //Kredit
      $where = "id_client = " . $pelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 AND status_mutasi = 3 GROUP BY id_client ORDER BY saldo DESC";
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

   function q()
   {
      echo "<img style='max-width:100vw; max-height:100vh' src='" . $this->ASSETS_URL . "img/qris/qris.jpg'>";
   }
}
