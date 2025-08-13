<?php

class Reminder extends Controller
{
   public function cek()
   {
      $data = $this->db(0)->get('reminder');
      foreach ($data as $d) {
         $t1 = strtotime($d['next_date']);
         $t2 = strtotime(date("Y-m-d H:i:s"));
         $t1 = new DateTime($d['next_date'] . ' 00:00:00');
         $t2 = new DateTime();
         $beda = $t1->diff($t2);

         $selisih_hari = $beda->days - 1;

         $rentang = $d['range'];

         if ($selisih_hari <= $rentang) {
            if ($selisih_hari > 0) {
               $text_count = $selisih_hari . " Hari Lagi";
            } elseif ($selisih_hari < 0) {
               $text_count = "Terlewat " . $selisih_hari * -1 . " Hari";
            } else {
               $text_count = "Hari Ini";
            }

            $note = "";
            if ($d['note'] <> "") {
               $note = "\n" . $d['note'];
            }

            $ops_link = URL::HOST_URL . "/I/r/" . $d['id'];
            $hp = $d['notif_number'];
            $text = "*" . $d['name'] . "* " . $note . " \n" . $text_count . " \n" . $ops_link;
            echo $d['name'] . " " . $text_count . " \n";

            //$res = $this->data('Notif')->send_wa($hp, $text);
         }
      }
   }

   function update()
   {
      $id = $_POST['id'];
      $where = "id = " . $id;
      $d = $this->db(0)->get_where_row('reminder', $where);
      $cycle = $d['cycle'];

      $t1 = strtotime($d['next_date']);
      $t2 = strtotime(date("Y-m-d H:i:s"));
      $diff = $t1 - $t2;
      $selisih_hari = floor(($diff / (60 * 60)) / 24);
      $rentang = $d['range'];

      if ($selisih_hari <= $rentang) {
         $next_date = date("Y-m-d", strtotime($d['next_date'] . " +" . $cycle . " " . $d['cycle_type']));
         $up = $this->db(0)->update('reminder', "next_date = '" . $next_date . "'", $where);
         if ($up['errno'] == 0) {
            echo 0;
         } else {
            echo "Error Updating, Hubungi Admin";
         }
      }
   }

   function cek_kas_cabang()
   {
      $hp = URL::WA_PRIVATE[1];
      $cabangs = $this->db(0)->get("cabang", "id_cabang");
      $data = $this->data('Saldo')->kasCabang();
      $text = "";
      foreach ($data as $key => $s) {
         if ($s >= 1000000) {
            if (strlen($text) == 0) {
               $text = "*" . $cabangs[$key]['kode_cabang'] . "* Rp" . number_format($s);
            } else {
               $text .= "\n*" . $cabangs[$key]['kode_cabang'] . "* Rp" . number_format($s);
            }

            $text_log = $cabangs[$key]['kode_cabang'] . " Rp" . number_format($s);
            echo $text_log . " \n";
         }
      }

      if (strlen($text) > 0) {
         $res = $this->data('Notif')->send_wa($hp, $text);
      } else {
         echo "ALL CASH UNDER 1.000.000 \n";
      }
   }
}
