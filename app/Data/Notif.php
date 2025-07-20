<?php

class Notif extends Controller
{

    function send_wa($hp, $text)
    {
        $private = false;
        foreach (URL::WA_PRIVATE as $private_number) {
            if ($hp == $private_number) {
                $private = true;
                break;
            }
        }

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
        //SAVE DB NOTIF
        $cols =  'insertTime, id_cabang, no_ref, phone, text, tipe, id_api, proses';
        $status = $res['data']['status'];
        $vals =  "'" . date('Y-m-d H:i:s') . "'," . $id_cabang . ",'" . $today . "','" . $hp . "','" . $otp . "',6,'" . $res['data']['id'] . "','" . $status . "'";
        $do = $this->db(date('Y'))->insertCols('notif', $cols, $vals);
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
