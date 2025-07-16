<?php

class I extends Controller
{
   public function i($pelanggan) //invoice tagihan total
   {
      if (!is_numeric($pelanggan)) {
         exit();
      }
      $this->public_data($pelanggan);
      $viewData = 'invoice/invoice_main';

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

      for ($y = 2021; $y <= date('Y'); $y++) {
         $data_s = $this->db($y)->get_where('sale', $where);
         if (count($data_s) > 0) {
            foreach ($data_s as $ds) {
               array_push($data_main, $ds);
            }
         }
      }

      $numbers = [];
      $refs = [];
      foreach ($data_main as $dm) {
         $i = substr($dm['insertTime'], 0, 4);
         $numbers[$dm['id_penjualan']] = $i;
         $refs[$dm['no_ref']] = $i;
      }

      $where2 = "id_pelanggan = " . $pelanggan . " AND bin = 0 GROUP BY id_harga";
      $list_paket = $this->db(0)->get_where('member', $where2);

      foreach ($numbers as $id => $book) {
         //OPERASI
         $where = "id_cabang = " . $this->id_cabang_p . " AND id_penjualan = " . $id;
         for ($y = $book; $y <= date('Y'); $y++) {
            $ops = $this->db($y)->get_where('operasi', $where);
            if (count($ops) > 0) {
               foreach ($ops as $opsv) {
                  array_push($operasi, $opsv);
               }
            }
         }
      }

      foreach ($refs as $rf => $book) {
         $where = "id_cabang = " . $this->id_cabang_p . "  AND jenis_transaksi = 1 AND ref_transaksi = '" . $rf . "'";
         for ($y = $book; $y <= date('Y'); $y++) {
            //KAS
            $ks = $this->db($y)->get_where('kas', $where);
            if (count($ks) > 0) {
               foreach ($ks as $ksv) {
                  array_push($kas, $ksv);
               }
            }
         }

         //SURCAS
         $where = "id_cabang = " . $this->id_cabang_p . "  AND no_ref = '" . $rf . "'";
         $sc = $this->db(0)->get_where('surcas', $where);
         if (count($sc) > 0) {
            foreach ($sc as $scv) {
               array_push($surcas, $scv);
            }
         }
      }

      $data_member = array();
      $where = "id_cabang = " . $this->id_cabang_p . "  AND bin = 0 AND id_pelanggan = " . $pelanggan . " AND lunas = 0";
      $order = "id_member DESC";
      $data_member = $this->db(0)->get_where_order('member', $where, $order);

      $numbersMember = array();
      $kasM = array();

      if (count($data_member) > 0) {
         $numbersMember = array_column($data_member, 'id_member');

         $where = "id_cabang = " . $this->id_cabang_p . "  AND bin = 0 AND id_pelanggan = " . $pelanggan . " ORDER BY insertTime ASC LIMIT 1";
         $yr_first = $this->db(0)->get_where_row('member', $where)['insertTime'];
         $i = substr($yr_first, 0, 4);

         foreach ($numbersMember as $nm) {
            $where = "id_cabang = " . $this->id_cabang_p . "  AND jenis_transaksi = 3 AND ref_transaksi = '" . $nm . "'";
            for ($y = $i; $y <= date('Y'); $y++) {
               $kasMd = $this->db($y)->get_where('kas', $where);
               if (count($kasMd) > 0) {
                  foreach ($kasMd as $ksmV) {
                     array_push($kasM, $ksmV);
                  }
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
      $saldoTunai = $this->data('Saldo')->getSaldoTunai($pelanggan);

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
         "surcas" => $surcas,
         'saldoTunai' => $saldoTunai,
      ]);
   }

   public function m($pelanggan, $id_harga) //riwayat member
   {
      if (!is_numeric($pelanggan)) {
         exit();
      }
      $this->public_data($pelanggan);
      $data_main = [];
      $data_main2 = [];

      for ($y = 2021; $y <= date('Y'); $y++) {
         $where = "id_pelanggan = " . $pelanggan . " AND id_harga = $id_harga AND bin = 0 AND member = 1 ORDER BY insertTime ASC";
         $data_s = $this->db($y)->get_where('sale', $where);

         if (count($data_s) > 0) {
            foreach ($data_s as $ds) {
               array_push($data_main, $ds);
            }
         }
      }

      $where2 = "id_pelanggan = " . $pelanggan . " AND id_harga = $id_harga AND bin = 0 ORDER BY insertTime ASC";
      $data_main2 = $this->db(0)->get_where('member', $where2);


      $viewData = 'member/member_history';

      $this->view($viewData, [
         'data_pelanggan' => $this->pelanggan_p,
         'data_main' => $data_main,
         'data_main2' => $data_main2,
         'id_harga' => $id_harga,
      ]);
   }

   public function s($pelanggan) // saldo deposit pelanggan
   {
      if (!is_numeric($pelanggan)) {
         exit();
      }
      $this->public_data($pelanggan);

      $data = array();
      $where = "id_client = " . $pelanggan . " AND status_mutasi = 3 AND ((jenis_transaksi = 1 AND metode_mutasi = 3) OR (jenis_transaksi = 3 AND metode_mutasi = 3) OR jenis_transaksi = 6)";
      $cols = "id_kas, id_client, jumlah, metode_mutasi, note, insertTime, jenis_mutasi, jenis_transaksi";

      for ($y = 2021; $y <= date('Y'); $y++) {
         $kasMd = $this->db($y)->get_cols_where('kas', $cols, $where, 1);
         if (count($kasMd) > 0) {
            foreach ($kasMd as $ksmV) {
               array_push($data, $ksmV);
            }
         }
      }

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

   function q() //gambar qris
   {
      echo "<img style='display: block; margin-left: auto; margin-right: auto; margin-top:30px; max-width:100vw; max-height:100vh' src='" . $this->ASSETS_URL . "img/qris/qris.jpg'>";
   }

   function r($id) // reminder
   {
      $where = "id = " . $id;
      $data = $this->db(0)->get_where_row('reminder', $where);
      $t1 = strtotime($data['next_date']);
      $t2 = strtotime(date("Y-m-d H:i:s"));
      $diff = $t1 - $t2;
      $dates = floor(($diff / (60 * 60)) / 24);

      if ($dates > 0) {
         $data['class'] = 'success';
         $text_count = $dates . " Hari Lagi";
      } elseif ($dates < 0) {
         $data['class'] = 'danger';
         $text_count = "Terlewat " . $dates * -1 . " Hari";
      } else {
         $data['class'] = 'danger';
         $text_count = "Hari Ini";
      }
      $data['dates'] = $dates;
      $data['warning'] = $text_count;

      $this->view('invoice/reminder', $data);
   }
}
