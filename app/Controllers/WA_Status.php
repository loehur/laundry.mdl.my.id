<?php

class WA_Status extends Controller
{
   function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   function index()
   {
      $data_operasi = ['title' => __CLASS__];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view(__CLASS__ . '/loader');
   }

   function content()
   {
      $res = $this->model('WA_Local')->cek_status();
      $this->view(__CLASS__ . '/content', $res);
   }
}
