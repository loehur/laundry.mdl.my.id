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

      $cookie_user = $this->model("Enc")->enc("user_londri");
      if (isset($_COOKIE[$cookie_user])) {
         echo "<pre>";
         $data = $this->model("Enc")->dec_2($_COOKIE[$cookie_user]);
         print_r(unserialize($data));
         echo "</pre>";
      }
   }
}
