<?php

class User extends Controller
{
    function pin_today($username, $otp)
    {
        $today = date("Ymd");
        $where = "username = '" . $username . "' AND otp = '" . $otp . "' AND otp_active = '" . $today . "' AND en = 1";
        return $this->db(0)->get_where_row('user', $where);
    }

    function last_login($username)
    {
        $device = $_SERVER['HTTP_USER_AGENT'];
        $where = "username = '" . $username . "'";
        $dateTime = date('Y-m-d H:i:s');
        $set = "last_login = '" . $dateTime . "', last_device = '" . $device . "'";
        $this->db(0)->update('user', $set, $where);
        $this->db(0)->query("SET GLOBAL time_zone = '+07:00'");
    }
}
