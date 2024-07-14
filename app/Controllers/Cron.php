<?php

class Cron extends Controller
{
   public function send()
   {
      $where = "proses = '' ORDER BY insertTime ASC";
      $data = $this->model('M_DB_1')->get_where('notif', $where);

      foreach ($data as $dm) {
         $id_notif = $dm['id_notif'];
         $hp = $dm['phone'];
         $text = $dm['text'];
         $res = $this->model("M_WA")->send($hp, $text, $this->dLaundry['notif_token']);

         if (is_array($res)) {
            foreach ($res["id"] as $v) {
               $status = $res["process"];
               $set = "status = 1, proses = '" . $status . "', id_api = '" . $v . "'";
               $where2 = "id_notif = '" . $id_notif . "'";
               $this->model('M_DB_1')->update('notif', $set, $where2);
            }
         }
         sleep(2);
      }
   }

   function wa($hp = '081268098300')
   {
      $res = $this->model("M_WA")->send($hp, "halo", $this->dLaundry['notif_token']);
      print_r($res);
   }
}
