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

      $this->view($view, ['data_operasi' => $data_operasi, 'data' => $data]);
   }

   public function insert()
   {
      $paket = $_POST['f1'];
      $bank = $_POST['f2'];

      if ($bank == 'bca') {
         $narek = "LUHUR GUNAWAN";
         $norek = "8455103793";
      }

      if ($paket > 12) {
         $paket = 12;
      }
      $jumlah = 60000 * $paket;

      $today = strtotime(date('Y-m-d'));
      if (isset($this->langganan['toDate'])) {
         $aktifTo = $this->langganan['toDate'];
         $aktifTo = strtotime($aktifTo);
      } else {
         $registered = strtotime($this->cabang_registerd);
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
      $cols = 'id_cabang, jumlah, bank, toDate, no_rek, nama_rek';
      $vals = $this->id_cabang . "," . $jumlah . ",'" . $bank . "','" . $toDateString . "','" . $norek . "','" . $narek . "'";

      if ($numberDays > -32) {
         $whereCount = $this->wCabang . " AND trx_status = 1";
         $dataCount = $this->model('M_DB_1')->count_where('mdl_langganan', $whereCount);
         if ($dataCount == 0) {
            $this->model('M_DB_1')->insertCols('mdl_langganan', $cols, $vals);
         }
      }
      header("location: Subscription.php", true, 301);
      exit();
   }
}
