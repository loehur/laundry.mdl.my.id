<?php

class Cron extends Controller
{
   public function send()
   {
      $pending = 0;
      $expire = 0;
      $sent = 0;
      $where = "proses = '' AND token <> '' AND status <> 5 AND id_api = '' ORDER BY insertTime ASC";

      foreach (URL::cabang_list_id as $cli) {
         $data = $this->db(1)->get_where('notif_' . $cli, $where);
         $pending += count($data);
         foreach ($data as $dm) {
            $id_notif = $dm['id_notif'];

            $expired_bol = false;

            $t1 = strtotime($dm['insertTime']);
            $t2 = strtotime(date("Y-m-d H:i:s"));
            $diff = $t2 - $t1;
            $hours = round($diff / (60 * 60), 1);

            if ($hours > 24) {
               $expired_bol = true;
            }

            if ($expired_bol == false) {
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
               $expire += 1;
            }
         }
      }

      echo "Pending: " . $pending . " \nExpired: " . $expire . " \nSent: " . $sent . "\n";
   }

   function pay_bill()
   {
      //cek semua tagihan
      $month = date('Ym');
      $week = 1;
      $thi = date('d');
      if ($thi > 0 && $thi <= 5) {
         $week = 1;
      } else if ($thi > 5 && $thi <= 10) {
         $week = 2;
      } else if ($thi > 10 && $thi <= 15) {
         $week = 3;
      } else if ($thi > 15 && $thi <= 20) {
         $week = 4;
      } else if ($thi > 20 && $thi <= 25) {
         $week = 5;
      } else {
         $week = 6;
      }
      $month .= $week;

      $data = $this->db(0)->get('postpaid_list');
      foreach ($data as $dt) {
         $code = $dt['code'];
         $customer_id = $dt['customer_id'];

         if ($dt['last_bill'] == $month && $dt['last_status'] == 1) {
            echo $dt['description'] . " PAID\n";
            continue;
         }

         //cek tagihan yg udah pernah di cek atau dibayar
         $where = "customer_id = '" . $dt['customer_id'] . "' AND code = '" . $dt['code'] . "' AND (tr_status = 3 OR tr_status = 4)";
         $cek = $this->db(0)->get_where('postpaid', $where);
         if (count($cek) > 0) {
            foreach ($cek as $a) {
               $ref_id = $a['ref_id'];

               if ($a['tr_status'] == 3) {
                  //cek karna sudah pernah dibayar
                  $response = $this->model('IAK')->post_cek($ref_id);
                  if (isset($response['data'])) {
                     $d = $response['data'];
                     if (isset($d['status'])) {
                        if ($d['status'] == $a['tr_status']) {
                           echo $dt['description'] . " Pending " . $a['message'] . "\n";
                           continue;
                        }
                     }

                     $message = isset($d['message']) ? $d['message'] : $a['message'];
                     $rc = isset($d['response_code']) ? $d['response_code'] : $a['response_code'];
                     $price = isset($d['price']) ? $d['price'] : $a['price'];
                     $balance = isset($d['balance']) ? $d['balance'] : $a['balance'];
                     $tr_id = isset($d['tr_id']) ? $d['tr_id'] : $a['tr_id'];
                     $datetime = isset($d['datetime']) ? $d['datetime'] : $a['datetime'];
                     $noref = isset($d['noref']) ? $d['noref'] : $a['noref'];
                     $tr_status = isset($d['status']) ? $d['status'] : $a['tr_status'];

                     if ($tr_status == 1) {
                        $where = "customer_id = '" . $customer_id . "' AND code = '" . $code . "'";
                        $set =  "last_bill = '" . $month . "', last_status = 1";
                        $update = $this->db(0)->update('postpaid_list', $set, $where);
                        if ($update['errno'] == 0) {
                           echo $dt['description'] . " - Postpaid List - " . $message . "\n";
                        } else {
                           $alert = "Update postpaid_list error, " . $update['error'];
                           echo $alert . "\n";
                           $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                           if (!isset($res["id"])) {
                              echo "Whatsapp Error, Sending Failed\n";
                           }
                           exit();
                        }
                     }

                     $where = "ref_id = '" . $ref_id . "'";
                     $set =  "tr_status = " . $tr_status . ", datetime = '" . $datetime . "', noref = '" . $noref . "', price = " . $price . ", message = '" . $message . "', balance = " . $balance . ", tr_id = '" . $tr_id . "', response_code = '" . $rc . "'";
                     $update = $this->db(0)->update('postpaid', $set, $where);
                     if ($update['errno'] == 0) {
                        echo $dt['description'] . " - Postpaid - " . $a['message'] . "\n";
                     } else {
                        $alert = "DB Error " . $update['error'];
                        echo $alert . "\n";
                        $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                        if (!isset($res["id"])) {
                           echo "Whatsapp Error, Sending Failed\n";
                        }
                     }
                  } else {
                     $alert = "Not found data, Res: " . json_encode($response);
                     echo $alert . "\n";
                     $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                     if (!isset($res["id"])) {
                        echo "Whatsapp Error, Sending Failed\n";
                     }
                  }
               } else {
                  //bayar karna sudah pernah di cek
                  $response = $this->model('IAK')->post_pay($a);
                  if (isset($response['data'])) {
                     $d = $response['data'];

                     $rc = isset($d['response_code']) ? $d['response_code'] : $a['response_code'];
                     $balance = isset($d['balance']) ? $d['balance'] : $a['balance'];
                     $price = isset($d['price']) ? $d['price'] : $a['price'];

                     if ($rc == '17') {
                        $alert = "Not Enough Balance to pay " . $dt['description'] . " Rp" . number_format($price);
                        echo $alert . "\n";
                        $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                        if (!isset($res["id"])) {
                           echo "Whatsapp Error, Sending Failed\n";
                        }
                        exit();
                     }

                     $message = isset($d['message']) ? $d['message'] : $a['message'];
                     $tr_id = isset($d['tr_id']) ? $d['tr_id'] : $a['tr_id'];
                     $datetime = isset($d['datetime']) ? $d['datetime'] : $a['datetime'];
                     $noref = isset($d['noref']) ? $d['noref'] : $a['noref'];
                     $tr_status = isset($d['status']) ? $d['status'] : 3;

                     $where = "ref_id = '" . $ref_id . "'";
                     $set =  "tr_status = " . $tr_status . ", datetime = '" . $datetime . "', noref = '" . $noref . "', price = " . $price . ", message = '" . $message . "', balance = " . $balance . ", tr_id = '" . $tr_id . "', response_code = '" . $rc . "'";
                     $update = $this->db(0)->update('postpaid', $set, $where);
                     if ($update['errno'] == 0) {
                        echo $dt['description'] . " " . $a['message'] . "\n";
                     } else {
                        $alert = "DB Error " . $update['error'];
                        echo $alert . "\n";
                        $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                        if (!isset($res["id"])) {
                           echo "Whatsapp Error, Sending Failed\n";
                        }
                     }
                  } else {
                     $alert = "Not found data, Res: " . json_encode($response);
                     echo $alert . "\n";
                     $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                     if (!isset($res["id"])) {
                        echo "Whatsapp Error, Sending Failed\n";
                     }
                  }
               }
            }
         } else {
            //cek tagihan udah dibayar belum
            $response = $this->model('IAK')->post_inquiry($code, $customer_id, $dt['id_cabang']);
            if (isset($response['data'])) {
               $d = $response['data'];

               if (isset($d['response_code'])) {
                  switch ($d['response_code']) {
                     case "01":
                        //SUDAH DIBAYAR
                        $where = "customer_id = '" . $customer_id . "' AND code = '" . $code . "'";
                        $set =  "last_bill = '" . $month . "', last_status = 1";
                        $update = $this->db(0)->update('postpaid_list', $set, $where);
                        if ($update['errno'] == 0) {
                           echo $dt['description'] . " " . $d['message'] . "\n";
                        } else {
                           $alert = "DB Error " . $update['error'];
                           echo $alert . "\n";
                           $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                           if (!isset($res["id"])) {
                              echo "Whatsapp Error, Sending Failed\n";
                           }
                        }
                        break;
                        echo $dt['description'] . " " . $a['message'] . "\n";
                     case "00":
                     case "05":
                     case "39":
                     case "201":
                        $col = "response_code, message, tr_id, tr_name, period, nominal, admin, ref_id, code, customer_id, price, selling_price, description, tr_status, id_cabang";
                        $val = "'" . $d['response_code'] . "','" . $d['message'] . "'," . $d['tr_id'] . ",'" . $d['tr_name'] . "','" . $d['period'] . "'," . $d['nominal'] . "," . $d['admin'] . ",'" . $d['ref_id'] . "','" . $d['code'] . "','" . $d['hp'] . "'," . $d['price'] . "," . $d['selling_price'] . ",'" . serialize($d['desc']) . "',4," . $dt['id_cabang'];
                        $do = $this->db(0)->insertCols("postpaid", $col, $val);
                        if ($do['errno'] == 0) {
                           echo $dt['description'] . " " . $d['message'] . "\n";
                        } else {
                           $alert = "DB Error " . $do['error'] . "\n";
                           echo $alert . "\n";
                           $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                           if (!isset($res["id"])) {
                              echo "Whatsapp Error, Sending Failed\n";
                           }
                        }
                        break;
                     default:
                        if (isset($d['message'])) {
                           $alert = $dt['description'] . " - response code: " . $d['response_code'] . " - " . $d['message'];
                        } else {
                           $alert = "Unknown response code: " . $d['response_code'];
                        }
                        echo $alert . "\n";
                        $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                        if (!isset($res["id"])) {
                           echo "Whatsapp Error, Sending Failed\n";
                        }
                        break;
                  }
               } else {
                  $alert = "Not found response code, res: " . json_encode($d);
                  echo $alert . "\n";
                  $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
                  if (!isset($res["id"])) {
                     echo "Whatsapp Error, Sending Failed\n";
                  }
               }
            } else {
               $alert = "Not found data, Res: " . json_encode($response);
               echo $alert . "\n";
               $res = $this->model("M_WA")->send(URL::WA_ADMIN, $alert, URL::WA_TOKEN);
               if (!isset($res["id"])) {
                  echo "Whatsapp Error, Sending Failed\n";
               }
            }
         }
      }
   }
}
