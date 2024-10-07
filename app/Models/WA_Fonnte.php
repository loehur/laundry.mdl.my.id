<?php

class WA_Fonnte extends Controller
{
    public function send($target, $message, $token)
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
        $rescode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if ($rescode <> 200) {
            $res = [
                'code' => $rescode,
                'status' => false,
                'forward' => true,
                'error' => 'server',
                'data' => [
                    'status' => ''
                ]
            ];
            return $res;
        }

        if (isset($error_msg)) {
            $res = [
                'code' => $rescode,
                'status' => false,
                'forward' => true,
                'error' => $error_msg,
                'data' => [
                    'status' => ''
                ],
            ];
        } else {
            $response = json_decode($response, true);
            if (isset($response["id"])) {
                foreach ($response["id"] as $k => $v) {
                    $status = $response["process"];
                    $id = $v;
                    $res = [
                        'code' => $rescode,
                        'status' => true,
                        'forward' => false,
                        'error' => 0,
                        'data' => [
                            'id' => $id,
                            'status' => $status
                        ],
                    ];
                }
            } else if (isset($response['reason'])) {
                $reason = $response['reason'];
                $res = [
                    'code' => $rescode,
                    'status' => false,
                    'forward' => true,
                    'error' => $reason,
                    'data' => [
                        'status' => ''
                    ],
                ];
            } else {
                $res = [
                    'code' => $rescode,
                    'status' => false,
                    'forward' => true,
                    'error' => json_encode($response),
                    'data' => [
                        'status' => ''
                    ],
                ];
            }
        }

        return $res;
    }
}
