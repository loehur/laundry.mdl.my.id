<?php

class WH_Fonnte extends Controller
{
   public function update()
   {
      header('Content-Type: application/json; charset=utf-8');

      $json = file_get_contents('php://input');
      $data = json_decode($json, true);

      echo "<pre>";
      print_r($data);
      echo "</pre>";

      if (isset($data['id']) && isset($data['stateid'])) {
         $id = $data['id'];
         $stateid = $data['stateid'];
         $status = $data['status'];
         $state = $data['state'];
         $set = "proses = '" . $status . "', state = '" . $state . "', id_state = '" . $stateid . "', status = 2";
         $where = "id_api = '" . $id . "'";
         $do = $this->model('M_DB_1')->update("notif", $set, $where);
         if ($do['errno'] <> 0) {
            $this->write($do['error']);
         }
      } else if (isset($data['id']) && !isset($data['stateid'])) {
         $id = $data['id'];
         $status = $data['status'];
         $set = "proses = '" . $status . "', status = 2";
         $where = "id_api = '" . $id . "'";
         $do = $this->model('M_DB_1')->update("notif", $set, $where);
         if ($do['errno'] <> 0) {
            $this->write($do['error']);
         }
      } else {
         $stateid = $data['stateid'];
         $state = $data['state'];
         $set = "state = '" . $state . "', status = 2";
         $where = "id_state = '" . $stateid . "'";
         $do = $this->model('M_DB_1')->update("notif", $set, $where);
         if ($do['errno'] <> 0) {
            $this->write($do['error']);
         }
      }
   }

   function cek_wa()
   {

      $token = $_POST['token'];
      $curl = curl_init();
      curl_setopt_array($curl, array(
         CURLOPT_URL => 'https://api.fonnte.com/device',
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token
         ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $res = json_decode($response, true);
      if ($res['status']) {
         echo strtoupper($res['device_status']);
      } else {
         echo "FAILED";
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
