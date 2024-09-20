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
      //cek semua tagihan
      $month = date('Ym');
      $data = $this->db(0)->get('postpaid_list');
      foreach ($data as $dt) {
         if ($dt['last_bill'] == $month && $dt['last_status'] == 1) {
            echo $dt['desc'] . " PAID\n";
            continue;
         }
         //cek tagihan yg udah pernah di cek
         $where = "customer_id = '" . $dt['customer_id'] . "' AND code = '" . $dt['code'] . "' AND tr_status = ''";
         $cek = $this->db(0)->get_where('postpaid', $where);
         if (count($cek) > 0) {
            foreach ($cek as $a) {
               //cek satu2 statusnya
               $ref_id = $a['ref_id'];
               $response = $this->model('IAK')->post_cek($ref_id);
               if (isset($response['data'])) {
                  $d = $response['data'];

                  if (isset($d['status'])) {
                     if ($d['status'] == $a['tr_status']) {
                        echo $dt['desc'] . " Pending " . $a['message'] . "\n";
                        continue;
                     }
                  }

                  $price = isset($d['price']) ? $d['price'] : $a['price'];
                  $message = isset($d['message']) ? $d['message'] : $a['message'];
                  $balance = isset($d['balance']) ? $d['balance'] : $a['balance'];
                  $tr_id = isset($d['tr_id']) ? $d['tr_id'] : $a['tr_id'];
                  $rc = isset($d['response_code']) ? $d['response_code'] : $a['rc'];
                  $datetime = isset($d['datetime']) ? $d['datetime'] : $a['datetime'];
                  $noref = isset($d['noref']) ? $d['noref'] : $a['noref'];
                  $tr_status = isset($d['status']) ? $d['status'] : $a['tr_status'];

                  $where = "ref_id = '" . $ref_id . "'";
                  $set =  "tr_status = " . $tr_status . ", datetime = '" . $datetime . "', noref = '" . $noref . "', price = " . $price . ", message = '" . $message . "', balance = " . $balance . ", tr_id = '" . $tr_id . "', response_code = '" . $rc . "'";
                  $update = $this->model('M_DB_1')->update('postpaid', $set, $where);
                  if ($update['errno'] == 0) {
                     echo $dt['desc'] . " " . $a['message'] . "\n";
                  } else {
                     echo $update['error'] . "\n";
                  }
               }
            }
         } else {
            //cek tagihan udah dibayar belum
            $code = $dt['code'];
            $customer_id = $dt['customer_id'];
            $response = $this->model('IAK')->post_inquiry($code, $customer_id);
            if (isset($response['data'])) {
               $d = $response['data'];

               if (isset($d['response_code'])) {
                  switch ($d['response_code']) {
                     case "00":
                     case "05":
                     case "39":
                     case "201":
                        $col = "response_code, message, tr_id, tr_name, period, nominal, admin, ref_id, code, customer_id, price, selling_price, desc";
                        $val = "'" . $d['response_code'] . "','" . $d['message'] . "'," . $d['tr_id'] . ",'" . $d['tr_name'] . "','" . $d['period'] . "'," . $d['nominal'] . "," . $d['admin'] . ",'" . $d['ref_id'] . "','" . $d['code'] . "','" . $d['hp'] . "'," . $d['price'] . "," . $d['selling_price'] . ",'" . serialize($d['desc']) . "'";
                        $do = $this->model('M_DB_1')->insertCols("postpaid", $col, $val);
                        if ($do['errno'] == 0) {
                           echo $dt['desc'] . " " . $d['message'] . "\n";
                        } else {
                           echo $do['error'] . "\n";
                        }
                        break;
                     default:
                        echo $data['data']['message'] . "\n";
                        break;
                  }
               } else {
                  $data['data']['message'] = "NO RESPONSE CODE!";
                  echo $data['data']['message'] . "\n";
               }
            } else {
               $data['data']['message'] = "PARSE ERROR!";
               echo $data['data']['message'] . "\n";
            }
         }
      }
   }
}
