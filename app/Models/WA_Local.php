<?php

class WA_Local extends Controller
{
    public function send($target, $message, $token = "")
    {

        if ($this->valid_number($target) == false) {
            $res = [
                'status' => false,
                'response' => 'invalid number'
            ];
            return $res;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://127.0.0.1:8033/send-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('message' => $message, 'number' => $target),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            $res = [
                'status' => false,
                'reason' => $error_msg
            ];
        } else {
            $response = json_decode($response, true);
            if ($response["status"]) {
                $status = $response["response"]['status'];
                $id = $response["response"]['key']['id'];

                $res = [
                    'status' => true,
                    'data' => [
                        'id' => $id,
                        'status' => $status
                    ],
                    'res' => $response
                ];
            } else {
                $res = [
                    'status' => false,
                    'response' => $response["response"]
                ];
            }
        }

        return $res;
    }
}
