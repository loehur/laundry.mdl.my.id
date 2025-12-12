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

      for ($y = URL::FIRST_YEAR; $y <= date('Y'); $y++) {
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

      $finance_history = [];
      $c_history = array_merge($kas, $kasM);
      foreach ($c_history as $k) {
         if (!isset($k['ref_finance']) || $k['ref_finance'] == '') continue;
         $rf = $k['ref_finance'];
         if (!isset($finance_history[$rf])) {
            $finance_history[$rf] = [
               'ref_finance' => $rf,
               'total' => 0,
               'status' => $k['status_mutasi'],
               'metode' => $k['metode_mutasi'],
               'note' => $k['note'],
               'insertTime' => $k['insertTime']
            ];
         }
         $finance_history[$rf]['total'] += intval($k['jumlah']);
         if (isset($k['insertTime']) && $k['insertTime'] > $finance_history[$rf]['insertTime']) {
            $finance_history[$rf]['insertTime'] = $k['insertTime'];
            $finance_history[$rf]['status'] = $k['status_mutasi'];
            $finance_history[$rf]['metode'] = $k['metode_mutasi'];
            $finance_history[$rf]['note'] = $k['note'];
         }
      }

      $finance_history = array_filter($finance_history, function ($item) {
         return $item['status'] == 2;
      });

      foreach ($finance_history as $key => $fh) {
         $check_moota = $this->db(100)->get_where_row("wh_moota", "trx_id = '" . $fh['ref_finance'] . "'");
         if(isset($check_moota['amount']) && $check_moota['amount'] > 0){
             $finance_history[$key]['total'] = $check_moota['amount'];
         }
      }

      $saldoTunai = 0;
      $saldoTunai = $this->helper('Saldo')->getSaldoTunai($pelanggan);

      // Hardcoded fallback to prevent URL class access errors
      $nonTunaiGuide = [
          'BCA' => ['label' => 'BCA (BANK CENTRAL ASIA)', 'number' => '8455103793', 'name' => 'LUHUR GUNAWAN'],
          'BRI' => ['label' => 'BRI (BANK RAKYAT INDONESIA)', 'number' => '327901031534535', 'name' => 'LUHUR GUNAWAN']
      ];
      
      // Try to use constant if available
      if (class_exists('URL') && defined('URL::NON_TUNAI_GUIDE')) {
          $nonTunaiGuide = URL::NON_TUNAI_GUIDE;
      }

      $this->view($viewData, [
         'data_pelanggan' => $this->pelanggan_p,
         'dataTanggal' => $data_tanggal,
         'data_main' => $data_main,
         'operasi' => $operasi,
         'kas' => $kas,
         'kasM' => $kasM,
         'nonTunaiGuide' => $nonTunaiGuide,
         'dTerima' => $data_terima,
         'dKembali' => $data_kembali,
         'listPaket' => $list_paket,
         'data_member' => $data_member,
         "surcas" => $surcas,
         'saldoTunai' => $saldoTunai,
         'saldoTunai' => $saldoTunai,
         'finance_history' => $finance_history,
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

      for ($y = URL::FIRST_YEAR; $y <= date('Y'); $y++) {
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

      for ($y = URL::FIRST_YEAR; $y <= date('Y'); $y++) {
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
      echo "<img style='display: block; margin-left: auto; margin-right: auto; margin-top:30px; max-width:100vw; max-height:100vh' src='" . URL::FILES_URL . "img/qris/qris.jpg'>";
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

   public function bayar()
   {
      if (!isset($_POST['id_pelanggan']) || !isset($_POST['rekap'])) {
         echo "Data incomplete";
         exit();
      }

      $id_pelanggan = $_POST['id_pelanggan'];
      $this->public_data($id_pelanggan); // Load cabang data
      
      $rekap = $_POST['rekap']; // Array [ref => amount]
      $metode_bayar = $_POST['metode']; // e.g. "Transfer"
      
      // Assume Non-Tunai (2) for online/invoice payments
      // The specific method (BCA, etc) goes into note
      $metode = 2; 
      $note = $metode_bayar;
      $dibayar = 0; // For NON_TUNAI, usually 0 cash given, or equal to amount. KasModel logic handles it.
      
      // KasModel expects rekap as [ref => amount]
      // Ensure rekap is in correct format
      
      $karyawan = 0; // System/Self
      $id_cabang = $this->id_cabang_p; 

      $res = $this->model('KasModel')->bayarMulti($rekap, $dibayar, $id_pelanggan, $id_cabang, $karyawan, $metode, $note);
      echo $res;
   }

   public function payment_gateway_order($ref_finance)
   {
      // Public Payment Gateway Order (for QRIS)
      $where = "ref_finance = '" . $ref_finance . "'";
      $kas = $this->db(date('Y'))->get_where_row('kas', $where);
      
      if (!isset($kas['id_kas'])) {
         echo json_encode(['status' => 'error', 'msg' => 'Transaction not found']);
         exit();
      }

      if ($kas['status_mutasi'] == 3) {
         echo json_encode(['status' => 'paid']);
         exit();
      }

      $nominal = isset($_GET['nominal']) ? intval($_GET['nominal']) : 0;
      if ($nominal <= 0) {
         $nominal = intval($kas['jumlah']);
      }
      if ($nominal <= 0) {
         echo json_encode(['status' => 'error', 'msg' => 'Nominal tidak valid']);
         exit();
      }

      $metode = isset($_GET['metode']) ? $_GET['metode'] : 'QRIS';

      if (strtoupper($metode) <> 'QRIS') {
         echo json_encode(['status' => 'error', 'msg' => 'Hanya menerima metode QRIS']);
         exit();
      }

      $ref_id = $ref_finance;
      
      // Check if we already have QR string stored
      $existing = $this->db(100)->get_where_row('wh_tokopay', "ref_id = '" . $ref_id . "'");
      if (isset($existing['qr_string']) && !empty($existing['qr_string'])) {
         echo json_encode([
            'status' => true, 
            'qr_string' => $existing['qr_string'],
            'trx_id' => $existing['trx_id'] ?? $ref_id
         ]);
         exit();
      }
      
      $gateway = defined('URL::PAYMENT_GATEWAY') ? URL::PAYMENT_GATEWAY : 'midtrans';

      if ($gateway == 'tokopay') {
         // TOKOPAY IMPLEMENTATION
         $res = $this->model('Tokopay')->createOrder($nominal, $ref_id, 'QRIS');
         $data = json_decode($res, true);

         if (isset($data['status']) && $data['status']) {
            $trx_id = $data['data']['trx_id'] ?? $ref_id;
            
            // Try multiple possible locations for qr_string
            $qr_string = '';
            if (isset($data['data']['qr_string']) && !empty($data['data']['qr_string'])) {
               $qr_string = $data['data']['qr_string'];
            } elseif (isset($data['data']['qr_link']) && !empty($data['data']['qr_link'])) {
               $qr_string = $data['data']['qr_link'];
            } elseif (isset($data['data']['pay_url']) && !empty($data['data']['pay_url'])) {
               $qr_string = $data['data']['pay_url'];
            } elseif (isset($data['qr_string']) && !empty($data['qr_string'])) {
               $qr_string = $data['qr_string'];
            }
            
            // Insert or update tracking table with qr_string
            $existingTrx = $this->db(100)->get_where_row('wh_tokopay', "ref_id = '" . $ref_finance . "'");
            if (isset($existingTrx['id'])) {
               // Update existing record with qr_string if available
               if (!empty($qr_string)) {
                  $this->db(100)->update('wh_tokopay', ['qr_string' => $qr_string], "ref_id = '" . $ref_finance . "'");
               }
            } else {
               // Insert new record
               $insertData = [
                  'trx_id' => $trx_id,
                  'target' => 'kas_laundry',
                  'ref_id' => $ref_finance,
                  'book' => date('Y')
               ];
               if (!empty($qr_string)) {
                  $insertData['qr_string'] = $qr_string;
               }
               $this->db(100)->insertIgnore('wh_tokopay', $insertData);
            }

            if (isset($data['data']['status']) && (strtolower($data['data']['status']) == 'success' || strtolower($data['data']['status']) == 'paid')) {
               $update = $this->db(date('Y'))->update('kas', ['status_mutasi' => 3], "ref_finance = '$ref_finance'");
               if ($update['errno'] == 0) {
                  echo json_encode(['status' => 'paid']);
                  exit();
               } else {
                  echo json_encode(['status' => 'error', 'msg' => $update['error']]);
                  exit();
               }
            } else {
               echo json_encode([
                  'status' => $data['status'], 
                  'qr_string' => $qr_string,
                  'trx_id' => $trx_id,
                  'raw' => $data // Include raw data for debugging
               ]);
               exit();
            }
         } else {
            echo json_encode(['status' => 'error', 'msg' => $data, 'raw' => $res]);
            exit();
         }
      } else {
         // MIDTRANS IMPLEMENTATION
         $midtransResponse = $this->model('Midtrans')->createTransaction($ref_id, $nominal);
         $data = json_decode($midtransResponse, true);

         // Check for success (Midtrans usually returns 200 or 201 with transaction_status)
         if (isset($data['transaction_id'])) {
            $trx_id = $data['transaction_id'];
            $qr_string = isset($data['qr_string']) ? $data['qr_string'] : '';
            
            $insert = $this->db(0)->insertIgnore('wh_midtrans', [
               'trx_id' => $trx_id,
               'target' => 'kas_laundry',
               'ref_id' => $ref_finance,
               'book' => date('Y')
            ]);

            if ($insert['errno'] == 0) {   
                  echo json_encode([
                     'status' => $data['status'] ?? 'pending', 
                     'qr_string' => $qr_string,
                     'trx_id' => $trx_id
                     ]);
                  exit();
            } else {
               echo json_encode(['status' => 'error', 'msg' => $insert['error']]);
               exit();
            }
         } else {
            echo json_encode(['status' => 'error', 'msg' => $data]);
            exit();
         }
      }
   }

   public function payment_gateway_check_status($ref_finance)
   {
      // Public Check Status
      $where = "ref_finance = '" . $ref_finance . "'";
      $kas = $this->db(date('Y'))->get_where_row('kas', $where);
      
      if (!isset($kas['id_kas'])) {
          echo json_encode(['status' => 'ERROR', 'msg' => 'Transaction not found']);
          exit();
      }

      if ($kas['status_mutasi'] == 3) {
         echo json_encode(['status' => 'PAID']);
         exit();
      }

      $note_trx = isset($kas['note']) ? strtoupper($kas['note']) : '';

      if ($note_trx <> 'QRIS') {
         // Manual check (already handled by top check: if status==3 return PAID)
         // If we are here, status is NOT 3 (likely 2).
         // Since it's manual/transfer, we just return PENDING or check if status changed.
         
         if ($kas['status_mutasi'] == 3) {
             echo json_encode(['status' => 'PAID']);
         } else {
             echo json_encode(['status' => 'PENDING', 'msg' => 'Menunggu Konfirmasi Admin']);
         }
         exit();
      }

      $gateway = defined('URL::PAYMENT_GATEWAY') ? URL::PAYMENT_GATEWAY : 'midtrans';

      if ($gateway == 'tokopay') {
          // Assuming Tokopay model handles public access or does not require session specific data
          $status = $this->model('Tokopay')->checkStatus($ref_finance, $kas['jumlah']);
          $data = json_decode($status, true);
          
          $isPaid = false;
          if (isset($data['data']['status'])) {
            if(strtolower($data['data']['status']) == 'success' || strtolower($data['data']['status']) == 'paid') {
              $isPaid = true;
            }
          } 
          
          if ($isPaid) {
             $update = $this->db(date('Y'))->update('kas', ['status_mutasi' => 3], "ref_finance = '$ref_finance'");
             if ($update['errno'] == 0) {
                echo json_encode(['status' => 'PAID']);
             } else {
                echo json_encode(['status' => 'ERROR', 'msg' => $update['error']]);
             }
          } else {
             echo json_encode(['status' => 'PENDING', 'data' => $data]);
          }

      } else {
          // Midtrans
          $status = $this->model('Midtrans')->checkStatus($ref_finance);
          $data = json_decode($status, true);
    
          $isPaid = false;
          if (isset($data['transaction_status'])) {
             if ($data['transaction_status'] == 'settlement' || $data['transaction_status'] == 'capture') {
                $isPaid = true;
             }
          }
    
          if ($isPaid) {
             $update = $this->db(date('Y'))->update('kas', ['status_mutasi' => 3], "ref_finance = '$ref_finance'");
             if ($update['errno'] == 0) {
                echo json_encode(['status' => 'PAID']);
             } else {
                echo json_encode(['status' => 'ERROR', 'msg' => $update['error']]);
             }
          } else {
             echo json_encode(['status' => 'PENDING', 'data' => $data]);
          }
      }
   }
}
