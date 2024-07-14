<?php

class Cron extends Controller
{
   public function send()
   {
      $where = "proses = '' ORDER BY insertTime ASC";
      $data = $this->model('M_DB_1')->get_where('notif', $where);

      foreach ($data as $dm) {
         $noref = $dm['noref'];
         $hp = $dm['phone'];
         $text = $dm['text'];
         $res = $this->model("M_WA")->send($hp, $text, $this->dLaundry['notif_token']);

         if (is_array($res)) {
            foreach ($res["id"] as $v) {
               $status = $res["process"];
               $set = "status = 1, proses = '" . $status . "', id_api = '" . $v . "'";
               $where2 = "no_ref = '" . $noref . "'";
               $this->model('M_DB_1')->update('notif', $set, $where2);
            }
         }
         sleep(2);
      }
   }
}
