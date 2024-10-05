<?php

class WA_Fonnte
{
    public function send($target, $message, $token)
    {

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
            $res = $error_msg;
            $this->write($error_msg);
        } else {
            $res = json_decode($response, true);
        }
        return $res;
    }

    public function send_b($target, $message, $token)
    {
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
                'delay' => '2-5'
            ),
            CURLOPT_HTTPHEADER => array('Authorization: ' . $token)
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response, true);
        return $res;
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
