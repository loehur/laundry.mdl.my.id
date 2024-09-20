<?php

class Cron extends Controller
{
   public function send()
   {
      $pending = 0;
      $expired = 0;
      $sent = 0;
      $where = "proses = '' AND token <> '' AND status <> 5 AND id_api = '' ORDER BY insertTime ASC";

      foreach (URL::cabang_list_id as $cli) {
         $data = $this->db(1)->get_where('notif_' . $cli, $where);
         $pending += count($data);
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
                     $this->db(1)->update('notif_' . $cli, $set, $where2);
                  }
                  $sent += 1;
               } else {
                  continue;
               }
            } else {
               $status = "expired";
               $set = "status = 2, proses = '" . $status . "'";
               $where2 = "id_notif = '" . $id_notif . "'";
               $this->db(1)->update('notif_' . $cli, $set, $where2);
               $expired += 1;
            }
         }
      }

      echo "Pending: " . $pending . " \nExpired: " . $expired . " \nSent: " . $sent . "\n";
   }

   function pay_bill()
   {
      $data = $this->db(0)->get('postpaid_list');
      foreach ($data as $d) {
      }
   }
}
