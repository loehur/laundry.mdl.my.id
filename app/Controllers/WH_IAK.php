<?php

class WH_IAK extends Controller
{
   public function update()
   {
      header('Content-Type: application/json; charset=utf-8');
      $json = file_get_contents('php://input');
      $data = json_decode($json, true);
      $this->model('Log')->write(json_encode($data));
   }
}
