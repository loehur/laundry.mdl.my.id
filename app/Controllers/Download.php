<?php

class Download extends Controller
{

   public function __construct()
   {
      $this->session_cek();
   }
   public function index()
   {
      $data_operasi = ['title' => 'MDL Menu | Download'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('download/download_main');
   }
}
