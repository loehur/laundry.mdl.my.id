<?php

class Notif extends Controller
{
    function insertOTP($res, $today, $hp, $otp)
    {
        //SAVE DB NOTIF
        $id_cabang = $_SESSION['user']['id_cabang'];
        $cols =  'id_cabang, no_ref, phone, text, tipe, id_api, proses';
        foreach ($res["id"] as $k => $v) {
            $status = $res["process"];
            $vals =  $id_cabang . ",'" . $today . "','" . $hp . "','" . $otp . "',6,'" . $v . "','" . $status . "'";
        }
        $do = $this->db(1)->insertCols('notif_' . $id_cabang, $cols, $vals);
        return $do;
    }

    function cek_deliver($hp, $date)
    {
        $where = "hp = '" . $hp . "' AND noref = '" . $date . "' AND (state = 'delivered' OR stete = 'read') AND uid <> ''";
        return $this->db(1)->get_where_row('notif_' . $_SESSION['user']['id_cabang'], $where);
    }

    function update_uid($uid) {}
}
