<?php

class Webhook extends Controller
{
   public function update()
   {
      header('Content-Type: application/json; charset=utf-8');

      $json = file_get_contents('php://input');
      $data = json_decode($json, true);
      $id = $data['id'];
      $status = $data['status'];
      $state = $data['state'];

      if (isset($id)) {
         $set = "proses = '" . $status . "', status = 2, state = '" . $state . "'";
         $where = "id_api = '" . $id . "'";
         $this->model('M_DB_1')->update("notif", $set, $where);
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
}
