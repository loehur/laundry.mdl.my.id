<?php

class Pre extends Controller
{
    function bulan_ini()
    {
        $month = date("Y-m");
        $col = "price";
        $where = "insertTime LIKE '%" . $month . "%' AND tr_status = 1 AND id_cabang = " . $_SESSION['user']['id_cabang'];
        return $this->db(0)->sum_col_where('prepaid', $col, $where);
    }
}
