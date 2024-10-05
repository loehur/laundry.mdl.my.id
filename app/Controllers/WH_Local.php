<?php

class WH_Local extends Controller
{
   public function update()
   {
      header('Content-Type: application/json; charset=utf-8');
      $json = file_get_contents('php://input');
      $data = json_decode($json, true);

      $state_arr = [
         0 => "error",
         1 => "pending",
         2 => "server",
         3 => "delivered",
         4 => "read",
         5 => "played",
      ];

      $proses_arr = [
         0 => "failed",
         1 => "processing",
         2 => "sent",
         3 => "sent",
         4 => "sent",
         5 => "sent",
      ];

      $id = $data['key']['id'];
      $res_state = $data['update']['status'];
      $state = $state_arr[$res_state];
      $status = $proses_arr[$res_state];

      $set = "proses = '" . $status . "', state = '" . $state . "'";
      $where = "id_api = '" . $id . "' OR id_api_2 = '" . $id . "'";

      foreach (URL::cabang_list_id as $cli) {
         $do = $this->db(1)->update('notif_' . $cli, $set, $where);
         if ($do['errno'] <> 0) {
            $this->write($do['error']);
         }
      }
   }

   function write($text)
   {
      $uploads_dir = "logs/wa/" . date('Y/') . date('m/');
      $file_name = date('d');
      $data_to_write = date('Y-m-d H:i:s') . " " . $text . "\n";
      $file_path = $uploads_dir . $file_name;

      if (!file_exists($uploads_dir)) {
         mkdir($uploads_dir, 0777, TRUE);
         $file_handle = fopen($file_path, 'w');
      } else {
         $file_handle = fopen($file_path, 'a');
      }

      fwrite($file_handle, $data_to_write);
      fclose($file_handle);
   }
}
