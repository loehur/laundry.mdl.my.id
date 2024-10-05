<?php

class WH_Fonnte extends Controller
{
   public function update()
   {
      header('Content-Type: application/json; charset=utf-8');
      $json = file_get_contents('php://input');
      $data = json_decode($json, true);

      $id = $data['id'];
      $stateid = $data['stateid'];
      $status = $data['status'];
      $state = $data['state'];

      //update status and state
      if (isset($id) && isset($stateid)) {
         $id = $data['id'];
         $stateid = $data['stateid'];
         $status = $data['status'];
         $state = $data['state'];
         $set = "proses = '" . $status . "', state = '" . $state . "', id_state = '" . $stateid . "', status = 2";
         $where = "id_api = '" . $id . "'";
      } else if (isset($id) && !isset($stateid)) {
         $id = $data['id'];
         $status = $data['status'];
         $set = "proses = '" . $status . "', status = 2";
         $where = "id_api = '" . $id . "'";
      } else {
         $stateid = $data['stateid'];
         $state = $data['state'];
         $set = "state = '" . $state . "', status = 2";
         $where = "id_state = '" . $stateid . "'";
      }

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
