<?php

class WA_Fonnte extends Controller
{
    public function send($target, $message, $token)
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
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token
            ),
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
            if (isset($response["id"])) {
                foreach ($response["id"] as $k => $v) {
                    $status = $response["process"];
                    $id = $v;

                    $res = [
                        'status' => true,
                        'data' => [
                            'id' => $id,
                            'status' => $status
                        ],
                        'res' => $response
                    ];
                }
            } else if (isset($response['reason'])) {
                $reason = $response['reason'];
                $res = [
                    'status' => false,
                    'reason' => $reason,
                    'res' => $response
                ];
            } else {
                $res = [
                    'status' => false,
                    'reason' => 'RES [ID] NOT FOUND',
                    'res' => $response
                ];
            }
        }

        return $res;
    }
}
