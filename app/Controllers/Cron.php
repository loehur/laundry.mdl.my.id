<?php

class Cron extends Controller
{
   public function send()
   {
      $where = "proses = '' AND token <> '' AND status <> 5 ORDER BY insertTime ASC";
      $data = $this->model('M_DB_1')->get_where('notif', $where);

      foreach ($data as $dm) {
         $id_notif = $dm['id_notif'];
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
            exit();
         }

         sleep(2);
      }
   }

   function wa($hp = '081268098300')
   {
      $token = 'M2tCJhb_mcr5tHFo5r4B';
      $res = $this->model("M_WA")->send($hp, "Whatsapp OK", $token);
      print_r($res);
   }
}
