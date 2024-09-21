<?php

class Tools extends Controller
{

   function cek_wa($hp = '081268098300')
   {
      $token = 'M2tCJhb_mcr5tHFo5r4B';
      $res = $this->model("M_WA")->send($hp, "test", $token);
      echo "<pre>";
      print_r($res);
      echo "</pre><br>";

      if (isset($res["id"])) {
         foreach ($res["id"] as $v) {
            $status = $res["process"];
            echo "ID: " . $v . ", Status: " . $status . "<br>";
         }
      }
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

      $cookie_user = "MDLSESSID";
      if (isset($_COOKIE[$cookie_user])) {
         echo "<pre>";
         $data = $this->model("Enc")->dec_2($_COOKIE[$cookie_user]);
         print_r(unserialize($data));
         echo "</pre>";
      }
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
      return $ipaddress;
   }
}
