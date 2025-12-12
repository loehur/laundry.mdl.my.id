<?php

class Notif extends Controller
{

    function send_wa($hp, $text, $private = true)
    {
        if ($private == true) {
            $res = $this->model(URL::WA_API[0])->send($hp, $text, URL::WA_TOKEN[0]);
            if ($res['forward']) {
                //ALTERNATIF WHATSAPP
                $res = $this->model(URL::WA_API[1])->send($hp, $text, URL::WA_TOKEN[1]);
            }
        } else {
            if (URL::WA_PUBLIC == true) {
                if (URL::WA_USER == 1) {
                    $res = $this->model(URL::WA_API[0])->send($hp, $text, URL::WA_TOKEN[0]);
                    if ($res['forward']) {
                        //ALTERNATIF WHATSAPP
                        $res = $this->model(URL::WA_API[1])->send($hp, $text, URL::WA_TOKEN[1]);
                    }
                } else {
                    $res = $this->model(URL::WA_API[1])->send($hp, $text, URL::WA_TOKEN[1]);
                }
            } else {
                $res = [
                    'code' => 0,
                    'status' => false,
                    'forward' => false,
                    'error' => 'No Error',
                    'data' => [
                        'status' => 'Disabled'
                    ],
                ];
            }
        }

        return $res;
    }

    function insertOTP($res, $today, $hp, $otp, $id_cabang)
    {
        $status = $res['data']['status'];
        //SAVE DB NOTIF
        $data = [
            'insertTime' => date('Y-m-d H:i:s'),
            'id_cabang' => $id_cabang,
            'no_ref' => $today,
            'phone' => $hp,
            'text' => $otp,
            'tipe' => 6,
            'id_api' => $res['data']['id'],
            'proses' => $status
        ];
        $do = $this->db(date('Y'))->insert('notif', $data);
        return $do;
    }

    function cek_deliver($hp, $date)
    {
        $where = "phone = '" . $hp . "' AND no_ref = '" . $date . "' AND state NOT IN ('delivered','read') AND id_api_2 = ''";

        $cek = $this->db(date('Y'))->get_where_row('notif', $where);
        if (isset($cek['text'])) {
            return $cek;
        }
        return $cek;
    }
}
