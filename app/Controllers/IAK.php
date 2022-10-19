<?php

class IAK extends Controller
{
   public function callBack()
   {
      $rawRequestInput = file_get_contents("php://input");
      $arrRequestInput = json_decode($rawRequestInput, true);
      $d = $arrRequestInput['data'];

      $ref_id = $d['ref_id'];
      $tr_status = $d['status'];
      $product_code = $d['product_code'];
      $customer_id = $d['customer_id'];
      $price = $d['price'];
      $message = $d['message'];
      $sn = $d['sn'];
      $balance = $d['balance'];
      $tr_id = $d['tr_id'];
      $rc = $d['rc'];
      $sign = $d['sign'];

      $where = "ref_id = '" . $ref_id . "'";

      $cek = $this->model('M_DB_1')->count_where('mdl_langganan', $where);
      if ($cek == 0) {
         $cols = 'ref_id, tr_status, product_code, customer_id, price, sn, balance, tr_id, rc, sign';
         $vals =  "'" . $ref_id . "'," . $tr_status . ",'" . $product_code . "','" . $customer_id . "'," . $price . ",'" . $message . "','" . $sn . "','" . $balance . "','" . $tr_id . "','" . $rc . "','" . $sign . "'";
         print_r($this->model('M_DB_1')->insertCols('callback', $cols, $vals));
      } else {
         $set =  "ref_id = '$ref_id', tr_status = $tr_status, product_code = '$product_code', customer_id = '$customer_id', price = $price, message = '$message', sn = '$sn', balance = $balance, tr_id = $tr_id, rc = '$rc', sign = '$sign'";
         print_r($this->model('M_DB_1')->update('callback', $set, $where));
      }
   }
}
