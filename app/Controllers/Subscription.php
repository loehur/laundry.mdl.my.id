<?php

class Subscription extends Controller
{

   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function index()
   {
      $view = 'subscription/sub_main';
      $data_operasi = ['title' => 'MDL Menu | Subscription'];
      $this->view('layout', ['data_operasi' => $data_operasi]);

      $where = $this->wCabang;
      $order = "id_trx DESC";
      $data = $this->model('M_DB_1')->get_where_order('mdl_langganan', $where, $order);

      $bank = $this->model('M_DB_1')->get('bank');
      $this->view($view, [
         'data_operasi' => $data_operasi,
         'data' => $data,
         'bank' => $bank
      ]);
   }

   public function insert()
   {
      if ($_SESSION['masa'] == 1) {
         header("location: Subscription.php", true, 301);
         exit();
      }

      $paket = $_POST['f1'];
      $id_bank = $_POST['f2'];
      $data_bank = $this->model('M_DB_1')->get('bank');

      foreach ($data_bank as $b) {
         if ($b['id_bank'] == $id_bank) {
            $bank = $b['bank'];
            $narek = $b['nama'];
            $norek = $b['norek'];
            $kode_bank = $b['kode_bank'];
         }
      }

      if ($paket > 12) {
         $paket = 12;
      }

      $jumlah = $this->bulanan * $paket;

      $today = strtotime(date('Y-m-d'));
      if (isset($this->langganan['toDate'])) {
         $aktifTo = $this->langganan['toDate'];
         $aktifTo = strtotime($aktifTo);
      } else {
         $registered = strtotime($this->cabang_registered);
         $aktifTo =  strtotime("+30 day", $registered);
      }

      //tenggang hari
      $timeDiff = abs($today - $aktifTo);
      $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
      $numberDays = intval($numberDays);

      $paketPlus = $paket * 30;
      $toDate = strtotime("+" . $paketPlus . " day", $aktifTo);

      if ($numberDays > 40) {
         $toDate = strtotime("+" . $paketPlus . " day", $today);
      } else {
         $toDate = strtotime("+" . $paketPlus . " day", $aktifTo);
      }
      $toDateString = date('Y-m-d', $toDate);
      $cols = 'id_cabang, jumlah, bank, toDate, no_rek, nama_rek, kode_bank';
      $vals = $this->id_cabang . "," . $jumlah . ",'" . $bank . "','" . $toDateString . "','" . $norek . "','" . $narek . "','" . $kode_bank . "'";

      if ($numberDays > -32) {
         $whereCount = $this->wCabang . " AND trx_status = 1";
         $dataCount = $this->model('M_DB_1')->count_where('mdl_langganan', $whereCount);
         if ($dataCount == 0) {
            $go = $this->model('M_DB_1')->insertCols('mdl_langganan', $cols, $vals);
            if ($go['errno'] == 0) {
               header("location: Subscription.php", true, 301);
            } else {
               print_r($go);
            }
         }
      }
   }

   function push()
   {
      $id = $_POST['id'];
      $set = "trx_status = 2";
      $where = "id_trx = " . $id;
      $do = $this->model("M_DB_1")->update("mdl_langganan", $set, $where);

      if ($do['errno'] == 0) {
         $user = $this->id_cabang . " " . $this->dLaundry['nama_laundry'];
         $message = "MDL Laundry " . $user . ", " . $this->HOST_URL . "/Subscription_/cp/" . $id;
         $curl = curl_init();
         curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('target' => '081268098300', 'message' => $message),
            CURLOPT_HTTPHEADER => array('Authorization: M2tCJhb_mcr5tHFo5r4B')
         ));
         curl_exec($curl);
         curl_close($curl);
      }
   }
}
