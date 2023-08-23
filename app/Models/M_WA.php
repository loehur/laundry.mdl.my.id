<?php

class M_WA
{
    private $db;

    public function __construct()
    {
        $this->db = DB_1::getInstance();
    }

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
            CURLOPT_POSTFIELDS => array('target' => $target, 'message' => $message),
            CURLOPT_HTTPHEADER => array('Authorization: ' . $token)
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response, true);
        return $res;
    }
}
