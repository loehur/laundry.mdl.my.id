<?php

class Tools extends Controller
{
   function cek_wa($choice, $hp = '081268098300', $text = 'test')
   {
      $res_t = $this->model(URL::WA_API[$choice])->send($hp, $text, URL::WA_TOKEN[$choice]);
      echo "<pre>";
      print_r($res_t);
      echo "</pre>";
   }

   function transfer_pelanggan($table, $col_nama, $col_nomor, $target_id_cabang)
   {
      $data = $this->db(0)->get($table);
      foreach ($data as $d) {
         $insert = $this->insert_pelanggan($d[$col_nama], $d[$col_nomor], $target_id_cabang);
         if ($insert <> 0) {
            echo $insert;
            exit();
         }
      }
   }

   function cek_cookie()
   {
      if (isset($_COOKIE["MDLSESSID"])) {
         $cookie_value = $this->model("Enc")->dec_2($_COOKIE["MDLSESSID"]);

         $user_data = unserialize($cookie_value);
         if (isset($user_data['username']) && isset($user_data['no_user']) && isset($user_data['ip']) && isset($user_data['device'])) {
            $no_user = $user_data['no_user'];
            $username = $this->model("Enc")->username($no_user);

            $device = $_SERVER['HTTP_USER_AGENT'];
            if ($username == $user_data['username'] && $user_data['device'] == $device && $user_data['ip'] == $this->get_client_ip()) {
               echo "Valid";
            }
         } else {
            echo "tidak Valid";
         }
      } else {
         echo "tidak bias unseriliaze";
      }
   }

   function insert_pelanggan($nama, $nomor, $id_cabang)
   {
      $table = "pelanggan";
      $cols = 'id_cabang, nama_pelanggan, nomor_pelanggan';
      $vals = $id_cabang . ",'" . $nama . "','" . $nomor . "'";
      $where = "nama_pelanggan = '" . $nama . "' AND id_cabang = 12";
      $data_main = $this->db(0)->count_where($table, $where);
      if ($data_main < 1) {
         $do = $this->db(0)->insertCols($table, $cols, $vals);
         if ($do['errno'] <> 0) {
            return $do['error'];
         }
      } else {
         return 0;
      }
   }

   function db_slice_order($id_cabang, $year)
   {
      $data_main = [];
      $wCabang = "id_cabang = " . $id_cabang;
      $set = "book = '" . $year . "'";

      $where = $wCabang . " AND insertTime LIKE '" . $year . "%'";
      $data_main = $this->db($year)->get_where('sale', $where);

      $numbers = array_column($data_main, 'id_penjualan');
      $refs = array_unique(array_column($data_main, 'no_ref'));

      foreach ($numbers as $id) {
         //OPERASI
         $where = $wCabang . " AND id_penjualan = " . $id;
         $ops = $this->db(1)->update('operasi', $set, $where);

         //NOTIF SELESAI
         $where = $wCabang . " AND tipe = 2 AND no_ref = '" . $id . "'";
         $ns = $this->db(1)->update('notif', $set, $where);
      }

      foreach ($refs as $rf) {
         //KAS
         $where = $wCabang . " AND jenis_transaksi = 1 AND ref_transaksi = '" . $rf . "'";
         $ks = $this->db(1)->update('kas', $set, $where);

         //NOTIF BON
         $where = $wCabang . " AND tipe = 1 AND no_ref = '" . $rf . "'";
         $nf = $this->db(1)->update('notif', $set, $where);
      }
   }

   function db_slice_member($id_cabang, $year)
   {
      $wCabang = "id_cabang = " . $id_cabang;
      $data = $this->db(0)->get_where('member', $wCabang);
      $set = "book = '" . $year . "'";

      foreach ($data as $dme) {
         //KAS
         $where = $wCabang . " AND jenis_transaksi = 3 AND ref_transaksi = '" . $dme['id_member'] . "'";
         $ks = $this->db(1)->update('kas', $set, $where);

         //NOTIF BON
         $where = $wCabang . " AND tipe = 3 AND no_ref = '" . $dme['id_member'] . "'";
         $nm = $this->db(1)->update('notif', $set, $where);
      }
   }

   function db_slice_kas($year)
   {
      $set = "book = '" . $year . "'";
      $where = "jenis_transaksi <> 1 AND jenis_transaksi <> 3 AND insertTime LIKE '" . $year . "%'";
      $up = $this->db(1)->update('kas', $set, $where);
   }

   function repair_username()
   {
      $data = $this->db(0)->get('user');
      foreach ($data as $d) {
         $username = $this->model("Enc")->username($d['no_user']);
         $set = "username = '" . $username . "'";
         $where = "id_user = '" . $d['id_user'] . "'";
         $this->db(0)->update('user', $set, $where);
      }
   }

   function enc($text)
   {
      echo $this->model('Enc')->enc($text);
   }

   function enc_2($text)
   {
      echo $this->model('Enc')->enc_2($text);
   }

   function dec_2($text)
   {
      echo $this->model('Enc')->dec_2($text);
   }

   function browser()
   {
      echo $_SERVER['HTTP_USER_AGENT'];
   }

   function cek_session()
   {
      echo "<pre>";
      print_r($_SESSION['user']);
      echo "</pre>";
   }

   function get_client_ip()
   {
      $ipaddress = '';
      if (isset($_SERVER['HTTP_CLIENT_IP']))
         $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
      else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
         $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      else if (isset($_SERVER['HTTP_X_FORWARDED']))
         $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
      else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
         $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
      else if (isset($_SERVER['HTTP_FORWARDED']))
         $ipaddress = $_SERVER['HTTP_FORWARDED'];
      else if (isset($_SERVER['REMOTE_ADDR']))
         $ipaddress = $_SERVER['REMOTE_ADDR'];
      else
         $ipaddress = 'UNKNOWN';
      echo $ipaddress;
   }

   function tes_model($model, $method, $value)
   {
      echo $this->model($model)->$method($value);
   }
}
