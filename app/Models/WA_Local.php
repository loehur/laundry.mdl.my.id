<?php

class WA_Local extends Controller
{
    public function send($target, $message, $token = "")
    {
        $target = $this->valid_number($target);
        if ($target == false) {
            $res = [
                'status' => false,
                'reason' => 'invalid number'
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
        $rescode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if ($rescode['code'] <> 200) {
            $res = [
                'code' => $rescode,
                'status' => false,
                'data' => [
                    'status' => 'Error'
                ]
            ];
            return $res;
        }

        if (isset($error_msg)) {
            $res = [
                'code' => $rescode,
                'status' => false,
                'reason' => $error_msg,
                'data' => [
                    'status' => 'Error'
                ],
            ];
        } else {
            $response = json_decode($response, true);
            if ($response["status"]) {
                $status = $response["response"]['status'];
                $id = $response["response"]['key']['id'];

                $res = [
                    'code' => $rescode,
                    'status' => true,
                    'data' => [
                        'id' => $id,
                        'status' => $status
                    ],
                ];
            } else {
                $res = [
                    'code' => $rescode,
                    'status' => false,
                    'reason' => json_encode($response),
                    'data' => [
                        'status' => 'Error'
                    ],
                ];
            }
        }

        return $res;
    }
}
