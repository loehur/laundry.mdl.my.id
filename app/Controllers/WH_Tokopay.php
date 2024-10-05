<?php

class WH_Tokopay extends Controller
{
   public function update()
   {
      $merchant_id = "M240926BMTGB612";
      $secret_key = "4aea0ede516df65d88ccb773a443c61b3b3702fe1b9647deb9293cac07fd72bf";

      $json = file_get_contents('php://input');
      $data = json_decode($json, true);

      if (isset($data['status'], $data['reff_id'], $data['signature'])) {
         $status = $data['status'];
         $ref_id = $data['reff_id'];

         if ($status === "Success" || $status === "Completed") {
            $signature_from_tokopay = $data['signature'];
            $signature_validasi = md5("$merchant_id:$secret_key:$ref_id");
            if ($signature_from_tokopay === $signature_validasi) {
               $set = "status_mutasi = 3";
               $where = "ref_finance = '" . $ref_id . "'";
               $up = $this->db(1)->update('kas', $set, $where);
               if ($up['errno'] <> 0) {
                  $res = $this->model(URL::WA_API[0])->send(URL::WA_ADMIN, "WH Tokopay Error - " . $up['error'], URL::WA_TOKEN[0]);
               }
               echo json_encode(['status' => true]);
            } else {
               $res = $this->model(URL::WA_API[0])->send(URL::WA_ADMIN, "WH Tokopay Error - Invalid Signature", URL::WA_TOKEN[0]);
               echo json_encode(['error' => "Invalid Signature"]);
            }
         } else {
            $set = "status_mutasi = 4";
            $where = "ref_finance = '" . $ref_id . "'";
            $up = $this->db(1)->update('kas', $set, $where);
            if ($up['errno'] <> 0) {
               $res = $this->model(URL::WA_API[0])->send(URL::WA_ADMIN, "WH Tokopay Error - " . $up['error'], URL::WA_TOKEN[0]);
            }
            $res = $this->model(URL::WA_API[0])->send(URL::WA_ADMIN, "WH Tokopay Error - Status payment tidak success", URL::WA_TOKEN[0]);
            echo json_encode(['error' => "Status payment tidak success"]);
         }
      } else {
         $res = $this->model(URL::WA_API[0])->send(URL::WA_ADMIN, "WH Tokopay Error - Data json tidak sesuai", URL::WA_TOKEN[0]);
         echo json_encode(['error' => "Data json tidak sesuai"]);
      }
   }
}
