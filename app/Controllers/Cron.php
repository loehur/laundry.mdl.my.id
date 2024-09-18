<?php

class Cron extends Controller
{
   public function send()
   {
      $where = "proses = '' AND token <> '' AND status <> 5 AND id_api = '' ORDER BY insertTime ASC";
      $data = $this->db(1)->get_where('notif_' . $this->id_cabang, $where);

      foreach ($data as $dm) {
         $id_notif = $dm['id_notif'];

         $expired = false;

         $t1 = strtotime($dm['insertTime']);
         $t2 = strtotime(date("Y-m-d H:i:s"));
         $diff = $t2 - $t1;
         $hours = round($diff / (60 * 60), 1);

         if ($hours > 15) {
            $expired = true;
         }

         if ($expired == false) {
            $hp = $dm['phone'];
            $text = $dm['text'];
            $token = $dm['token'];
            $res = $this->model("M_WA")->send($hp, $text, $token);

            if (isset($res['id'])) {
               foreach ($res['id'] as $v) {
                  $status = $res["process"];
                  $set = "status = 1, proses = '" . $status . "', id_api = '" . $v . "'";
                  $where2 = "id_notif = '" . $id_notif . "'";
                  $this->db(1)->update('notif_' . $this->id_cabang, $set, $where2);
               }
            } else {
               continue;
            }
         } else {
            $status = "expired";
            $set = "status = 2, proses = '" . $status . "'";
            $where2 = "id_notif = '" . $id_notif . "'";
            $this->db(1)->update('notif_' . $this->id_cabang, $set, $where2);
         }

         sleep(1);
      }
   }

   public function cek()
   {
      $where = "proses = '' AND token <> '' AND status <> 5 AND id_api = '' ORDER BY insertTime ASC";
      $data = $this->db(1)->get_where('notif_' . $this->id_cabang, $where);

      foreach ($data as $dm) {
         $id_notif = $dm['id_notif'];
         $hp = $dm['phone'];
         $text = $dm['text'];

         echo $id_notif . ": [" . $hp . "] " . $text . "<br>";
      }
   }

   function wa($hp = '081268098300')
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

   function transfer_pelanggan($table, $col_nama, $col_nomor)
   {
      $data = $this->db(0)->get($table);
      foreach ($data as $d) {
         $insert = $this->insert_pelanggan($d[$col_nama], $d[$col_nomor]);
         if ($insert <> 0) {
            echo $insert;
            exit();
         }
      }
   }

   function insert_pelanggan($nama, $nomor)
   {
      $id_cabang = 12; //CEK BAIK2 ID CABANG
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
}
