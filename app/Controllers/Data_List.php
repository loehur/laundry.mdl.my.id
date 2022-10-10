<?php

class Data_List extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function i($page)
   {
      $d2 = array();
      $z = array();
      $data_main = array();
      $z = array('page' => $page);

      switch ($page) {
         case "item":
            $view = 'data_list/' . $page;
            $data_operasi = ['title' => 'Item Laundry'];
            $table = $page;
            $where = $this->wLaundry;
            $order = 'item ASC';
            $data_main = $this->model('M_DB_1')->get_where_order($table, $where, $order);
            break;
         case "item_pengeluaran":
            $view = 'data_list/' . $page;
            $data_operasi = ['title' => 'Item Pengeluaran'];
            $table = $page;
            $where = $this->wLaundry;
            $order = 'id_item_pengeluaran ASC';
            $data_main = $this->model('M_DB_1')->get_where_order($table, $where, $order);
            break;
         case "surcas":
            $view = 'data_list/' . $page;
            $data_operasi = ['title' => 'Surcharge'];
            $table = "surcas_jenis";
            $where = $this->wLaundry;
            $order = 'id_surcas_jenis ASC';
            $data_main = $this->model('M_DB_1')->get_where_order($table, $where, $order);
            break;
         case "user":
            $view = 'data_list/' . $page;
            $data_operasi = ['title' => 'Data Karyawan'];
            $table = $page;
            $where = $this->wLaundry;
            $d2 = $this->model('M_DB_1')->get_where('cabang', $where);
            $where = $this->wLaundry . " AND en = 1 ORDER BY id_cabang ASC";
            $data_main = $this->model('M_DB_1')->get_where($table, $where);
            break;
         case "userDisable":
            $view = 'data_list/userDisable';
            $data_operasi = ['title' => 'Approval Karyawan Aktif'];
            $table = 'user';
            $where = $this->wLaundry . " ORDER BY id_cabang ASC";
            $d2 = $this->model('M_DB_1')->get_where('cabang', $where);
            $where = $this->wLaundry . " AND en = 0";
            $data_main = $this->model('M_DB_1')->get_where($table, $where);
            break;
         case "pelanggan":
            $view = 'data_list/' . $page;
            $data_operasi = ['title' => 'Data Pelanggan'];
            $table = $page;
            $where = $this->wCabang;
            $order = 'id_pelanggan DESC';
            $data_main = $this->model('M_DB_1')->get_where_order($table, $where, $order);
            break;
      }
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, ['data_main' => $data_main, 'd2' => $d2, 'z' => $z]);
   }

   public function insert($page)
   {
      $table  = $page;
      switch ($page) {
         case "item":
            $cols = 'id_laundry, item';
            $f1 = $_POST['f1'];
            $vals = $this->id_laundry . ",'" . $f1 . "'";
            $setOne = "item = '" . $f1 . "'";
            $where = $this->wLaundry . " AND " . $setOne;
            $data_main = $this->model('M_DB_1')->count_where($table, $where);
            if ($data_main < 1) {
               $this->model('M_DB_1')->insertCols($table, $cols, $vals);
               $this->dataSynchrone();
            }
            break;
         case "item_pengeluaran":
            $cols = 'id_laundry, item_pengeluaran';
            $f1 = $_POST['f1'];
            $vals = $this->id_laundry . ",'" . $f1 . "'";
            $setOne = "item_pengeluaran = '" . $f1 . "'";
            $where = $this->wLaundry . " AND " . $setOne;
            $data_main = $this->model('M_DB_1')->count_where($table, $where);
            if ($data_main < 1) {
               $this->model('M_DB_1')->insertCols($table, $cols, $vals);
               $this->dataSynchrone();
            }
            break;
         case "surcas":
            $table = "surcas_jenis";
            $cols = 'id_laundry, surcas_jenis';
            $f1 = $_POST['f1'];
            $vals = $this->id_laundry . ",'" . $f1 . "'";
            $setOne = "surcas_jenis = '" . $f1 . "'";
            $where = $this->wLaundry . " AND " . $setOne;
            $data_main = $this->model('M_DB_1')->count_where($table, $where);
            if ($data_main < 1) {
               $this->model('M_DB_1')->insertCols($table, $cols, $vals);
               $this->dataSynchrone();
            }
            break;
         case "pelanggan":
            $cols = 'id_laundry, id_cabang, nama_pelanggan, nomor_pelanggan, id_notif_mode, alamat';
            $nama_pelanggan = $_POST['f1'];
            $vals = $this->id_laundry . "," . $this->id_cabang . ",'" . $nama_pelanggan . "','" . $_POST['f2'] . "'," . $_POST['f3'] . ",'" . $_POST['f4'] . "'";
            $setOne = "nama_pelanggan = '" . $_POST['f1'] . "'";
            $where = $this->wCabang . " AND " . $setOne;
            $data_main = $this->model('M_DB_1')->count_where($table, $where);
            if ($data_main < 1) {
               $this->model('M_DB_1')->insertCols($table, $cols, $vals);
               $this->dataSynchrone();
               echo 1;
            } else {
               echo "Gagal!, nama: <b>[ " . strtoupper($nama_pelanggan) . " ]</b> sudah digunakan";
            }
            break;
         case "user":
            $cols = 'id_laundry, id_cabang, no_user, nama_user, id_privilege, email, id_kota, domisili, akses_layanan, password';
            $akses_layanan = serialize($_POST['f9']);
            $vals = $this->id_laundry . "," . $_POST['f3'] . ",'" . $_POST['f5'] . "','" . $_POST['f1'] . "'," . $_POST['f4'] . ",'" . $_POST['f6'] . "'," . $_POST['f7'] . ",'" . $_POST['f8'] . "','" . $akses_layanan . "','" . md5('1234') . "'";
            $this->model('M_DB_1')->insertCols($table, $cols, $vals);
            $this->dataSynchrone();
            break;
      }
   }

   public function updateCell($page)
   {
      $table  = $page;
      $id = $_POST['id'];
      $value = $_POST['value'];
      $mode = $_POST['mode'];

      switch ($page) {
         case "item":
            if ($mode == 1) {
               $col = "item";
            }
            $where = $this->wLaundry . " AND id_item = " . $id;
            break;
         case "item_pengeluaran":
            if ($mode == 1) {
               $col = "item_pengeluaran";
            }
            $where = $this->wLaundry . " AND id_item_pengeluaran = " . $id;
            break;
         case "surcas_jenis":
            if ($mode == 1) {
               $col = "surcas_jenis";
            }
            $where = $this->wLaundry . " AND id_surcas_jenis = " . $id;
            break;
         case "pelanggan":
            switch ($mode) {
               case "1":
                  $col = "nama_pelanggan";
                  break;
               case "2":
                  $col = "nomor_pelanggan";
                  break;
               case "3":
                  $col = "id_notif_mode";
                  break;
               case "4":
                  $col = "alamat";
                  break;
               case "5":
                  $col = "disc";
                  if ($value > 100) {
                     $value = 100;
                  }
                  break;
            }
            $where = $this->wCabang . " AND id_pelanggan = " . $id;
            break;
         case "user":
            $table  = $page;
            $id = $_POST['id'];
            $value = $_POST['value'];
            $mode = $_POST['mode'];

            switch ($mode) {
               case "2":
                  $col = "nama_user";
                  break;
               case "3":
                  $col = "id_laundry";
                  break;
               case "4":
                  $col = "id_cabang";
                  break;
               case "5":
                  $col = "id_privilege";
                  break;
               case "6":
                  $col = "no_user";
                  break;
               case "7":
                  $col = "email";
                  break;
               case "8":
                  $col = "id_kota";
                  break;
               case "10":
                  $col = "domisili";
                  break;
               case "11":
                  $col = "akses_layanan";
                  $value = serialize($_POST['value']);
                  break;
            }
            $where = $this->wLaundry . " AND id_user = $id";
            break;
      }

      $set = $col . " = '" . $value . "'";
      $this->model('M_DB_1')->update($table, $set, $where);
      $this->dataSynchrone();
   }

   public function enable($bol)
   {
      $table  = 'user';
      $id = $_POST['id'];
      $where = $this->wLaundry . " AND id_user = " . $id;
      $set = "en = " . $bol;
      $this->model('M_DB_1')->update($table, $set, $where);
      $this->dataSynchrone();
   }

   public function wa_status($token)
   {
      $where = "notif_token = '" . $token . "'";
      $data = $this->model('M_DB_1')->get_where_row('laundry', $where);

      $log = 0;
      $auth = $data['notif_auth'];
      $log = $data['notif_log'];
      $log_time = $data['updateTime'];

      if ($log == 1) {
         echo "Whatsapp <span class='ml-1 text-bold text-success'>CONNECTED</span>";
      } else {
         date_default_timezone_set("Asia/Jakarta");
         $now = date('Y-m-d H:i:s');
         $beginTime = date_create($log_time);
         $finalTime = date_create($now);

         $diff  = date_diff($beginTime, $finalTime);

         $menit = $diff->i;
         $filePath = "";
         $filePath = $this->model('M_QR')->GenQR($auth);


         echo "<span class='ml-2'>" . $now . "</span>";

         if ($menit > 3) {
            echo "<span class='ml-2 text-bold text-danger'>QR Expired</span>";
         }
         echo "<span><img width='260' height='260' src='" . $this->BASE_URL . $filePath  . "' /></span>";
         echo "<span id='log' class='d-none'>" . $log . "</span>";
      }
   }

   public function synchrone()
   {
      $this->dataSynchrone();
   }
}
