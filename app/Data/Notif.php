<?php

class Notif extends Controller
{
    function insertOTP($res, $today, $hp, $otp, $id_cabang)
    {
        //SAVE DB NOTIF
        $cols =  'insertTime, id_cabang, no_ref, phone, text, tipe, id_api, proses';
        $status = $res['data']['status'];
        $vals =  "'" . date('Y-m-d H:i:s') . "'," . $id_cabang . ",'" . $today . "','" . $hp . "','" . $otp . "',6,'" . $res['data']['id'] . "','" . $status . "'";
        $do = $this->db(1)->insertCols('notif_' . $id_cabang, $cols, $vals);
        return $do;
    }

    function cek_deliver($hp, $date)
    {
        foreach (URL::cabang_list_id as $cli) {
            $where = "phone = '" . $hp . "' AND no_ref = '" . $date . "' AND state NOT IN ('delivered','read') AND id_api_2 = ''";
            $cek = $this->db(1)->get_where_row('notif_' . $cli, $where);
            if (isset($cek['text'])) {
                return $cek;
            }
        }
        return $cek;
    }
}