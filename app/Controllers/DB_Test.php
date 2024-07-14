<?php

class DB_Test extends Controller
{
    public function index()
    {
        $this->model('M_DB_1')->test();
    }

    function get($table)
    {
        echo "<pre>";
        print_r($this->model("M_DB_1")->get($table));
        echo "</pre>";
    }

    function wa($target, $text)
    {
        $res = $this->model("M_WA")->send($target, $text, $this->dLaundry['notif_token']);
        $res = json_encode($res);
        echo "<pre>";
        print_r($res);
        echo "</pre>";
    }

    function tes_wa()
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
                'target' => '08123456789',
                'message' => 'test message',
                'countryCode' => '62', //optional
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: M2tCJhb_mcr5tHFo5r4B' //change TOKEN to your actual token
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
        }
        curl_close($curl);

        if (isset($error_msg)) {
            echo $error_msg;
        }
        echo $response;
    }
}
