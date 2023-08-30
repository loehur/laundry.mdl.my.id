<?php

class Subscription_ extends Controller
{
   function cp($id)
   {
      $where = "id_trx = " . $id;
      $data['_c'] = __CLASS__;
      $data['data'] = $this->model('M_DB_1')->get_where_row('mdl_langganan', $where);
      $this->view("subscription/cp", $data);
   }

   function confirm($id, $c)
   {
      $set = "trx_status = " . $c;
      $where = "id_trx = " . $id;
      echo "<pre>";
      print_r($this->model("M_DB_1")->update("mdl_langganan", $set, $where));
      echo "<br>Confirm: " . $c;
      echo "</pre>";
   }
}
