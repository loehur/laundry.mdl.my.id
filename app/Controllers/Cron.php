<?php

class Cron extends Controller
{
   public function send()
   {
      $where = "proses = '' AND token <> '' AND status <> 5 AND id_api = '' ORDER BY insertTime ASC";
      $data = $this->model('M_DB_1')->get_where('notif', $where);

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
                  $this->model('M_DB_1')->update('notif', $set, $where2);
               }
            } else {
               continue;
            }
         } else {
            $status = "expired";
            $set = "status = 2, proses = '" . $status . "'";
            $where2 = "id_notif = '" . $id_notif . "'";
            $this->model('M_DB_1')->update('notif', $set, $where2);
         }

         sleep(1);
      }
   }

   public function cek()
   {
      $where = "proses = '' AND token <> '' AND status <> 5 AND id_api = '' ORDER BY insertTime ASC";
      $data = $this->model('M_DB_1')->get_where('notif', $where);

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
      $res = $this->model("M_WA")->send($hp, "Whatsapp OK", $token);
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
}
