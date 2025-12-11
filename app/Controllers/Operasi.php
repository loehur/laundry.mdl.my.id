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

      $pelanggan = $this->pelanggan[$id_pelanggan];

      if ($mode == 1) {
         $where = $this->wCabang . " AND id_pelanggan = $id_pelanggan AND bin = 0 AND tuntas = " . $mode . " ORDER BY id_penjualan DESC";
         $modeView = 2;
      } else {
         $where = $this->wCabang . " AND id_pelanggan = $id_pelanggan AND bin = 0 AND tuntas = 0 ORDER BY id_penjualan DESC";
      }
      $data_main = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where('sale', $where);
      $data_main2 = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_where('sale', $where, 'no_ref', 1);

      $viewData = 'operasi/view_load';

      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_unique(array_column($data_main, 'no_ref'));

      $operasi = [];
      $kas = [];
      $surcas = [];
      $notifBon = [];
      $notifSelesai = [];

      foreach ($numbers as $id) {

         $where_o = $this->wCabang . " AND id_penjualan = " . $id; //OPERASI
         $where_n = $this->wCabang . " AND tipe = 2 AND no_ref = '" . $id . "'"; //NOTIF SELESAI

         $i = $_SESSION[URL::SESSID]['user']['book'];
         while ($i <= date('Y')) {
            //OPERASI
            $ops = $this->db($i)->get_where('operasi', $where_o);
            if (count($ops) > 0) {
               foreach ($ops as $opsv) {
                  array_push($operasi, $opsv);
               }
            }

            //NOTIF SELESAI
            $ns = $this->db($i)->get_where_row('notif', $where_n);
            if (count($ns) > 0) {
               array_push($notifSelesai, $ns);
            }

            $i++;
         }
      }

      foreach ($refs as $rf) {
         $where_kas = $this->wCabang . " AND jenis_transaksi = 1 AND ref_transaksi = '" . $rf . "'"; //KAS
         $where_notif = $this->wCabang . " AND tipe = 1 AND no_ref = '" . $rf . "'"; //NOTIF BON

         $i = $_SESSION[URL::SESSID]['user']['book'];
         while ($i <= date('Y')) {
            //KAS
            $ks = $this->db($i)->get_where('kas', $where_kas);
            if (count($ks) > 0) {
               foreach ($ks as $ksv) {
                  array_push($kas, $ksv);
               }
            }
            //NOTIF NOTA
            $nf = $this->db($i)->get_where_row('notif', $where_notif);
            if (count($nf) > 0) {
               array_push($notifBon, $nf);
            }
            $i++;
         }


         //SURCAS
         $where = $this->wCabang . " AND no_ref = '" . $rf . "'";
         $sc = $this->db(0)->get_where('surcas', $where);
         if (count($sc) > 0) {
            foreach ($sc as $scv) {
               array_push($surcas, $scv);
            }
         }
      }

      $finance_history = [];
      foreach ($kas as $k) {
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

      //MEMBER
      $data_member = [];
      $where = $this->wCabang . " AND bin = 0 AND id_pelanggan = " . $id_pelanggan . " AND lunas = 0";
      $data_member = $this->db(0)->get_where('member', $where);

      $notif_member = [];
      $kas_member = [];
      foreach ($data_member as $dme) {
         $harga = $dme['harga'];
         $idm = $dme['id_member'];
         $historyBayar[$dme['id_member']] = [];

         $i = substr($dme['insertTime'], 0, 4);
         $where = $this->wCabang . " AND jenis_transaksi = 3 AND ref_transaksi = '" . $dme['id_member'] . "'";
         $where_notif = $this->wCabang . " AND tipe = 3 AND no_ref = '" . $dme['id_member'] . "'";
         while ($i <= date('Y')) {
            //KAS MEMBER
            $km = $this->db($i)->get_where('kas', $where);
            if (count($km) > 0) {
               if (!isset($kas_member[$idm])) {
                  $kas_member[$idm] = [];
               }

               foreach ($km as $kmv) {
                  array_push($kas_member[$idm], $kmv);
               }
            }

            //NOTIF MEMBER
            $nm = $this->db($i)->get_where_row('notif', $where_notif);
            if (count($nm) > 0) {
               array_push($notif_member, $nm);
            }

            $i += 1;
         }

         if (isset($kas_member[$idm])) {
            foreach ($kas_member[$idm] as $k) {
               if ($k['ref_transaksi'] == $idm && $k['status_mutasi'] == 3) {
                  array_push($historyBayar[$idm], $k['jumlah']);
               }
               $totalBayar = array_sum($historyBayar[$idm]);
               if ($totalBayar >= $harga) {
                  $lunas = $this->db(0)->update('member', ['lunas' => 1], 'id_member = ' . $idm);
                  if ($lunas['errno'] <> 0) {
                     echo "ERROR UPDATE PAID, MEMBER ID " . $idm;
                  }
               }
            }
         }
      }

      //SALDO DEPOSIT
      $sisaSaldo = $this->helper('Saldo')->getSaldoTunai($id_pelanggan);

      $users = $this->db(0)->get("user", "id_user");
      $this->view($viewData, [
         'modeView' => $modeView,
         'pelanggan' => $pelanggan,
         'data_main' => $data_main2,
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
         'saldoTunai' => $sisaSaldo,
         'users' => $users,
         'finance_history' => $finance_history
      ]);
   }

   public function payment_gateway_order($ref_finance)
   {

      //cek dulu status_mutasi sudah berubah belum
      $where = $this->wCabang . " AND ref_finance = '" . $ref_finance . "'";
      $kas = $this->db(date('Y'))->get_where_row('kas', $where);
      if ($kas['status_mutasi'] == 3) {
         echo json_encode(['status' => 'paid']);
         exit();
      }

      $nominal = isset($_GET['nominal']) ? intval($_GET['nominal']) : 0;
      if ($nominal <= 0) {
         echo "Nominal tidak valid";
         exit();
      }

      $metode = isset($_GET['metode']) ? $_GET['metode'] : 'QRIS';

      if ($metode <> 'QRIS') {
         echo "Hanya menerima metode QRIS";
         exit();
      }

      $ref_id = $ref_finance;
      
      $ref_id = $ref_finance;
      
      $gateway = defined('URL::PAYMENT_GATEWAY') ? URL::PAYMENT_GATEWAY : 'midtrans';

      if ($gateway == 'tokopay') {
         // TOKOPAY IMPLEMENTATION
         $res = $this->model('Tokopay')->createOrder($nominal, $ref_id, 'QRIS');
         $data = json_decode($res, true);

         if (isset($data['status']) && $data['status']) {
            $trx_id = $data['data']['trx_id'] ?? $ref_id;
            $qr_string = $data['data']['qr_string'] ?? ($data['data']['qr_link'] ?? '');
            
            // Insert to tracking table
            $insert = $this->db(100)->insertIgnore('wh_tokopay', [
               'trx_id' => $trx_id,
               'target' => 'kas_laundry',
               'ref_id' => $ref_finance,
               'book' => date('Y')
            ]);

            if ($insert['errno'] == 0) {
                if ($data['status'] == 'Success' || $data['status'] == 'Completed') {
                  $update = $this->db(date('Y'))->update('kas', ['status_mutasi' => 3], "ref_finance = '$ref_finance'");
                  if ($update['errno'] == 0) {
                     echo json_encode(['status' => 'PAID']);
                     exit();
                  }else{
                     echo json_encode(['status' => 'error', 'msg' => $update['error']]);
                     exit();
                  }
                } else {
                  echo json_encode([
                     'status' => $data['status'], 
                     'qr_string' => $qr_string,
                     'trx_id' => $trx_id
                  ]);
                  exit();
               }
            } else {
               echo json_encode(['status' => 'error', 'msg' => $insert['error']]);
               exit();
            }
         } else {
             echo $res;
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
                     'status' => $data['status'], 
                     'qr_string' => $qr_string,
                     'trx_id' => $trx_id
                     ]);
                  exit();
            } else {
               echo json_encode(['status' => 'error', 'msg' => $insert['error']]);
               exit();
            }
         } else {
            echo $midtransResponse;
            exit();
         }
      }
   }

   public function payment_gateway_check_status($ref_finance)
   {
      //cek dulu status_mutasi sudah berubah oleh webhook
      $where = $this->wCabang . " AND ref_finance = '" . $ref_finance . "'";
      $kas = $this->db(date('Y'))->get_where_row('kas', $where);
      if ($kas['status_mutasi'] == 3) {
         echo json_encode(['status' => 'PAID']);
         exit();
      }

      $gateway = defined('URL::PAYMENT_GATEWAY') ? URL::PAYMENT_GATEWAY : 'midtrans';

      if ($gateway == 'tokopay') {
          $status = $this->model('Tokopay')->checkStatus($ref_finance, $kas['jumlah']);
          $data = json_decode($status, true);
          
          $isPaid = false;
          // Tokopay check: assuming 'data'->'status' == 'Success' or similar
          if (isset($data['data']['status']) && strtoupper($data['data']['status']) == 'SUCCESS') {
             $isPaid = true;
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
          // Use Midtrans Model
          $status = $this->model('Midtrans')->checkStatus($ref_finance);
          $data = json_decode($status, true);
    
          // Midtrans status: settlement, capture = success
          // pending = pending
          // deny, cancel, expire = fail
          
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

   public function bayarMulti($idPelanggan, $karyawan = 0, $metode = 2, $note = "")
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

      arsort($data);
      $ref_f = (date('Y') - 2024) . date('mdHis') . rand(0, 9) . rand(0, 9) . $this->id_cabang;

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
            $sisaSaldo = $this->helper('Saldo')->getSaldoTunai($idPelanggan);
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
         $setOne = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND insertTime LIKE '%" . $minute . "%'";
         $where = $this->wCabang . " AND " . $setOne;
         $data_main = $this->db(date('Y'))->count_where('kas', $where);
         if ($data_main < 1) {
            $data = [
               'id_cabang' => $this->id_cabang,
               'jenis_mutasi' => 1,
               'jenis_transaksi' => $jt,
               'ref_transaksi' => $ref,
               'metode_mutasi' => $metode,
               'note' => $note,
               'status_mutasi' => $status_mutasi,
               'jumlah' => $jumlah,
               'id_user' => $karyawan,
               'id_client' => $idPelanggan,
               'ref_finance' => $ref_f,
               'insertTime' => $GLOBALS['now']
            ];
            $do = $this->db(date('Y'))->insert('kas', $data);
            $dibayar -= $jumlah;
            if ($do['errno'] <> 0) {
               print_r($do['error']);
               exit();
            }
         } else {
            echo "Pembayaran dengan jumlah yang sama terkunci, lakukan di jam berikutnya.";
            exit();
         }
      }
      echo 0;
   }

   public function ganti_operasi()
   {
      $karyawan = $_POST['f1'];
      $id = $_POST['id'];

      $set = ['id_user_operasi' => $karyawan];
      $where = $this->wCabang . " AND id_operasi = " . $id;
      $in = $this->db($_SESSION[URL::SESSID]['user']['book'])->update('operasi', $set, $where);
      if ($in['errno'] <> 0) {
         echo $in['error'];
      } else {
         echo 0;
      }
   }
}
